<?php

namespace Sergeich5\PaymentService\Common;

interface IPaymentService
{
    function getPaymentUrl(int $accountId, int $amount, array $data, string $url = '') : string;
    function chargeByToken(string $token, int $accountId, int $amount, array $data, array $items = []);
}
