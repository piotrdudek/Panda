<?php

namespace Panda5\CarRent\Application;

use Panda5\CarRent\Infrastructure\mySQLCars;
use Panda5\CarRent\Domain\Transaction;

class MakeReservation 
{
    public function newReservation($idCar,$days,$price )
	{
		$res = new Transaction(0, time() + (3 * 60),"" ,"Rozpoczęta",$price,$days, $idCar,"","");

		$interface=new mySQLCars();
		if (!$interface->isAvailableCar($idCar))
			return $res;

		$interface->saveTransaction($res);
		return $res;
	}
}