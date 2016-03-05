<?php

namespace Panda5\CarRent\Domain;
use Panda5\CarRent\Domain\Transaction;
use Panda5\CarRent\Domain\Payment;

interface CarsForRent
{
    public function getCars();

	public function getCar($id);

	public function saveTransaction(Transaction $tra);

	public function getTransaction();

	public function savePayment(Payment $pay);

}
