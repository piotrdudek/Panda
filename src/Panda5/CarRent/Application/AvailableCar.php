<?php

namespace Panda5\CarRent\Application;

use Panda5\CarRent\Domain\Model\Car;
use Panda5\CarRent\Infrastructure\mySQLCars;

class AvailableCar
{
	public function getCars(){
		$interface= new mySQLCars();		
		$interface->checkTimeReservation();
		return $interface->getCars();
	}

}
