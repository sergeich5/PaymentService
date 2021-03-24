<?php

namespace Sergeich5\PaymentService\GazPromBank;

use Sergeich5\PaymentService\Common\IPaymentService;

class GazPromBankService implements IPaymentService
{
    const API_URL = '';

    function getPaymentUrl(int $accountId, int $amount, array $data, string $url = ''): string
    {
        $params = array(
            'lang' => 'RU',
            'merch_id' => '7BFY70Z24904C6E7F0C03QA2R34367E5',
            'back_url_s' => 'https://merchant.ru/succeeded.jsp',
            'back_url_f' => 'http://merchant.ru/failed.jsp',
            'o.order_id' => '28735'
        );

        return 'https://lt.pga.gazprombank.ru/pages/?' . http_build_query($params);
    }

    function chargeByToken(string $token, int $accountId, int $amount, array $data, array $items = [])
    {
        // TODO: Implement chargeByToken() method.
    }
}
