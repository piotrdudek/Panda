<?php

namespace Panda5\CarRent\Infrastructure;
use Panda5\CarRent\Domain\Model\Payment;
class DotpayPaymentFactory
{
    public function createPayment(DotpayCompletePayment $completePayment)
    {
        $payment = new Payment();
        if ($completePayment->isSuccessful()) {
            $payment->confirm();
            return $payment;
        }
        $payment->deny();
        return $payment;
    }
}