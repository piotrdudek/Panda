<?php

namespace Panda5\CarRent\Domain;

class Car
{
	private $id;
	private $name;
	private $segment;
	private $price;

	public function __construct(
		$id,
		$name,
		$segment,
		$price
	){
		$this->id = $id;
		$this->name = $name;
		$this->segment = $segment;
		$this->price = $price;
	}

	public function getId(){
		return $this->id;
	}

	public function getName(){
		return $this->name;
	}

	public function getSegment(){
		return $this->segment;
	}

	public function getPrice(){
		return $this->price;
	}
}