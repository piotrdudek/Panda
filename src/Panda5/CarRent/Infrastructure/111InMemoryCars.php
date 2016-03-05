<?php

namespace Panda5\CarRent\Infrastructure;

use Panda5\CarRent\Domain\Car;
use Panda5\CarRent\Domain\CarsForRent;
//use Jkan\BookReader\Domain\Page;

class InMemoryCars implements CarsForRent
{
    public function getCars()
    {

		$cars[] = new Car(1,'Fiat Panda','A - mini',500);
		$cars[] = new Car(2,'Ford Fiesta','B - miejskie',600);
		$cars[] = new Car(3,'Audi A3','C - premium',700);
        return $cars;
    }

    public function getCar($id)
    {

		if ($id == 1)
		{
			$car = new Car(1,'Fiat Panda','A - mini',500);
		}
		if ($id == 2)
		{
			$car = new Car(2,'Ford Fiesta','B - miejskie',600);
		}
		if ($id == 3)
		{
			$car = new Car(3,'Audi A3','C - premium',700);
		}
        return $car;
    }

}