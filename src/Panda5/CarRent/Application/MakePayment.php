<?php

namespace Panda5\CarRent\Application;

use Panda5\CarRent\Infrastructure\DotpayCompletePayment;
use Panda5\CarRent\Domain\Payment;

class MakePayment 
{
	public function createPayment(DotpayCompletePayment $completePayment){
    	$payment = new Payment($completePayment);
        if ($completePayment->isSuccessful()) {
            $payment->confirm();
            return $payment;
        }
        $payment->deny();
        return $payment;
    }
}