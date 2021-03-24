<?php

namespace Sergeich5\PaymentService\PayMaster;

use Sergeich5\PaymentService\Common\IPaymentService;

class PayMasterService implements IPaymentService
{
    const API_URL = 'https://paymaster.ru';

    function getPaymentUrl(int $accountId, int $amount, array $data, string $url = ''): string
    {
        // TODO: Implement getPaymentUrl() method.
    }

    function chargeByToken(string $token, int $accountId, int $amount, array $data, array $items = [])
    {
        // TODO: Implement chargeByToken() method.
    }
}
