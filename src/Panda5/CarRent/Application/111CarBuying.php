<?php

namespace Panda5\CarRent\Application;

use Jkan\BookCanonicalModel\BookIdentifier;
use Panda5\CarRent\Domain\Exception\StoreException;
use Panda5\CarRent\Domain\Model\Payment;
use Jkan\BookStore\Domain\Model\OrderRegistry;
class CarBuying
{
    /**
     * @var OrderRegistry
     */
    private $orderRegistry;
    public function completePurchase(OrderIdentifier $id, Payment $payment)
    {
        if ($payment->isCompleaded()) {
            $order = $this->orderRegistry->getOrderIdentifiedWith($id);
            $order->confirm();
            //do sth else
            return;
        }
        throw new StoreException('Missing payment');
    }
}