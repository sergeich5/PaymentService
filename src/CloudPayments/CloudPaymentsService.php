<?php

namespace Sergeich5\PaymentService\CloudPayments;

use Sergeich5\PaymentService\Common\Exceptions\PaymentServiceException;
use Sergeich5\PaymentService\Common\IPaymentService;

class CloudPaymentsService implements IPaymentService
{
    const API_URL = 'https://api.cloudpayments.ru';
    const STOP_PAYMENTS_REASON_CODES = [
        5300,
        5207,
        5206,
        5004,
        5007,
        5012,
        5030,
        5031,
        5033,
        5036,
        5041,
        5043,
        5062,
        5063,
    ];

    const INSUFFICIENT_FUNDS_CODE = 5051; // недостаточно средств
    const EXPIRED_CARD_CODE = 5033; // Карта просрочена или неверно указан срок действия
    const TRANSACTION_NOT_PERMITTED_CODE = 5057; // Ограничение на карте
    const ANTI_FRAUD_CODE = 5300; // Лимиты эквайера на проведение операций
    const SYSTEM_ERROR_CODE = 5096; // Ошибка банка-эквайера или сети
    const TIMEOUT_CODE = 5091; // Эмитент недоступен
    const CANNOT_REACH_NETWORK = 5092; // Эмитент недоступен	Повторите попытку позже или воспользуйтесь другой картой


    private string $privateKey;
    private string $publicID;

    function __construct(string $publicID, string $privateKey)
    {
        $this->publicID = $publicID;
        $this->privateKey = $privateKey;
    }

    function getPaymentUrl(int $accountId, int $amount, array $data, string $domain = ''): string
    {
        return $domain . '?' . http_build_query(array_merge(
                array(
                    'user_id' => $accountId,
                    'amount' => $amount
                ),
                $data
            ));
    }

//        $items = array([
//            'label' => $title,              //наименование товара
//            'price' => $amount,             //цена
//            'quantity' => 1,                //количество
//            'amount' => $amount,            //сумма
//            'vat' => 0,                     //ставка НДС
//        ], [
//            'label' => $title,              //наименование товара
//            'price' => $amount,             //цена
//            'quantity' => 1,                //количество
//            'amount' => $amount,            //сумма
//            'vat' => 0,                     //ставка НДС
//        ]);

    function chargeByToken(string $token, int $accountId, int $amount, array $data, array $items = []) : bool
    {
        $receipt = [];
        if (count($items) > 0) {
            foreach ($items as $item)
                $receipt['cloudPayments']['CustomerReceipt']['Items'][] = $item;

            $receipt['cloudPayments']['CustomerReceipt']['Amounts'][] = ['Electronic' => $amount];
        }

        $response = $this->call('/payments/tokens/charge', [
            'Amount' => $amount,
            'Currency' => 'RUB',
            'AccountId' => $accountId,
            'Token' => $token,
            'JsonData' => array_merge(
                $data,
                $receipt
            )
        ]);

        if ($response['Success'])
            return true;

        if (isset($response['Model']) && isset($response['Model']['ReasonCode']))
            throw new PaymentServiceException('error', $response['Model']['ReasonCode']);

        return false;
    }

    private function call(string $method, array $data)
    {
        $ch = $this->prepareCurl();

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_URL, self::API_URL.$method);

        $response = $this->executeCurl($ch);

        return $response;
    }

    private function getCurl()
    {
        // Create or reuse existing cURL handle
        $this->ch = $this->ch ?? curl_init();

        // Throw exception if the cURL handle failed
        if (! $this->ch) {
            throw new PaymentServiceException('Could not initialise cURL!', 0);
        }

        return $this->ch;
    }

    private function prepareCurl()
    {
        $ch = $this->getCurl();

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic '.base64_encode($this->publicID.':'.$this->privateKey),
            'Accept: application/json',
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        return $ch;
    }

    private function executeCurl($ch)
    {
        $responseData = curl_exec($ch);

        return $responseData;
    }
}
