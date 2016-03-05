<?php

namespace Panda5\CarRent\Infrastructure;

use Panda5\CarRent\Domain\Car;
use Panda5\CarRent\Domain\Transaction;
use Panda5\CarRent\Domain\Payment;
use Panda5\CarRent\Domain\CarsForRent;


class mySQLCars implements CarsForRent
{
	private $link;

    private function openDb(){
		$this->link = mysql_connect('10.254.94.2','s182704','Ncf3kCHY');
		mysql_select_db('s182704',$this->link);
	}

    public function getCars(){
		$this->openDb();
		
		$result = mysql_query('select id,name,segment,price from cars where czas_rezerwacji is null and data_wypozyczenia is null ',$this->link);
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$cars[] = new Car($row['id'],$row['name'],$row['segment'],$row['price']);
		}
		return isset($cars) ? $cars : null ;
    }

    public function getCar($id){
		$this->openDb();

		$result = mysql_query('select id,name,segment,price from cars where id = ' . $id,$this->link);
		if ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$car = new Car($row['id'],$row['name'],$row['segment'],$row['price']);
		}

        return $car;
    }

    public function isAvailableCar($id){
		$this->openDb();

		$result = mysql_query("select id from cars where id =  $id and czas_rezerwacji is null and data_wypozyczenia is null ",$this->link);

		return ($row = mysql_fetch_array($result, MYSQL_ASSOC)) ? true : false ;
    }

	public function saveTransaction(Transaction $tra){
		$id = 0;
		$this->openDb();
		mysql_query("INSERT INTO transakcje (car_id, days, price, status,czas) VALUES ("
			.$tra->getCarId() .",". $tra->getDays().",".$tra->getPrice().",'".$tra->getStatus()."','" .date("Y-m-d H:i:s",$tra->getCzas()) ."')" );

		$result = mysql_query(" select max(id) as nr from transakcje ",$this->link);
		if ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$id = $row['nr'];
		}
        if ( $id >0 )
			mysql_query("UPDATE cars set  czas_rezerwacji ='" . date("Y-m-d H:i:s",$tra->getCzas()) ."' where id =".$tra->getCarId());


		$tra->setId($id);
		return ;

	}

	public function getTransaction(){
		$this->openDb();

		$result = mysql_query('select tr.id,tr.czas,tr.price,ca.name, tr.status,days,car_id, pl.numer,pl.mail from transakcje tr 
		join cars ca on tr.car_id = ca.id left join platnosci pl on pl.tr_id= tr.id order by tr.id desc ' ,$this->link);
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$trans[] = new Transaction($row['id'],$row['czas'],$row['name'],$row['status'],$row['price'],$row['days'],$row['car_id'],$row['numer'],$row['mail']);
		}
        return $trans;


	}

	public function savePayment(Payment $payment){
		$this->openDb();
		mysql_query("INSERT INTO platnosci (tr_id,numer,kwota,status,mail) VALUES (".$payment->getControl().",'".$payment->getNumber()."',".$payment->getAmount().",'".$payment->getStatus()."','".$payment->getMail()."' ) ");

		$result = mysql_query("select  days  ,car_id from transakcje where id=".$payment->getControl() ,$this->link);
		if ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			mysql_query("UPDATE transakcje set status ='Rozliczona' where id = ".$payment->getControl());
			$datar = time() + ($row['days'] * 24 * 60 * 60);
			mysql_query("UPDATE cars set czas_rezerwacji=null ,data_wypozyczenia ='" .  date("Y-m-d",$datar) . "' where id = " . $row['car_id']  );
		}

	}

	public function checkTimeReservation(){
		$this->openDb();
		mysql_query("UPDATE cars set czas_rezerwacji=null  where czas_rezerwacji  < '" . date("Y-m-d H:i:s") . "' "  );
        mysql_query("UPDATE transakcje set status='Anulowana'  where czas  < '" .  date("Y-m-d H:i:s") . "'  and status<>'Rozliczona' "  );
		mysql_query("UPDATE cars set data_wypozyczenia=null  where data_wypozyczenia  < '" . date("Y-m-d") . "' "  );
    }

}