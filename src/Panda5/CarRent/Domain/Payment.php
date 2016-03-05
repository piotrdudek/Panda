<?php

namespace Panda5\CarRent\Domain;
use  Panda5\CarRent\Infrastructure\DotpayCompletePayment;
use Panda5\CarRent\Infrastructure\mySQLCars;
class Payment
{
    private $compleaded;
	private $number;
	private $status;
	private $amount;
	private $control;
	private $mail;

	public function __construct(DotpayCompletePayment $completePayment) {
		$this->number = $completePayment->getNumber();
		$this->status = $completePayment->getStatus();
		$this->amount = $completePayment->getAmount();
		$this->control = $completePayment->getControl();
		$this->mail = $completePayment->getMail();
	}

    public function confirm(){
        $this->compleaded = true;
    }

    public function deny(){
        $this->compleaded = false;
    }

    public function isCompleaded(){
        return $this->compleaded;
    }

	public function getNumber() {
		return $this->number;
	}

	public function getStatus() {
		return $this->status;
	}

	public function getAmount() {
		return $this->amount;
	}

	public function getControl() {
		return $this->control;
	}

	public function getMail() {
		return $this->mail;
	}


	public function savePayment() {
		$baz = new mySQLCars();
		$baz->savePayment($this);
	}
}