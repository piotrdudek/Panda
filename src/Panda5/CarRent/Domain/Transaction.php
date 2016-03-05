<?php

namespace Panda5\CarRent\Domain;

class Transaction
{
	private $id;
	private $czas;
	private $name;
	private $status;
	private $price;
	private $days;
	private $car_id;
	private $numer;

	public function __construct(
		$id,
		$czas,
		$name,
		$status,
		$price,
		$days,
		$car_id,
		$numer,
		$mail
	){
		$this->id = $id;
		$this->czas = $czas;
		$this->name = $name;
		$this->status = $status;
		$this->price = $price;
		$this->days = $days;
		$this->car_id = $car_id;
		$this->numer = $numer;
		$this->mail = $mail;
	}

		
	public function setCzas($czas){
		$this->czas=$czas;
	}

	public function getId(){
		return $this->id;
	}

	public function getCzas(){
		return $this->czas;
	}

	public function getName(){
		return $this->name;
	}

	public function getStatus(){
		return $this->status;
	}

	public function getPrice(){
		return $this->price;
	}

	public function getDays(){
		return $this->days;
	}

	public function getCarId(){
		return $this->car_id;
	}

	public function getNumer(){
		return $this->numer;
	}

	public function getMail(){
		return $this->mail;
	}

	public function setId($id){
		$this->id=$id;
	}

}