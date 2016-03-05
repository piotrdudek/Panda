<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Panda5\CarRent\Domain\Car;
use Panda5\CarRent\Domain\CarsForRent;
use Panda5\CarRent\Domain\Transaction;
use Panda5\CarRent\Domain\Payment;
use Panda5\CarRent\Domain\Exception\StoreException;
use Panda5\CarRent\Infrastructure\DotpayCompletePayment;
use Panda5\CarRent\Application\AvailableCar;
use Panda5\CarRent\Application\MakeReservation;
use Panda5\CarRent\Application\MakePayment;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;


class DefaultController extends Controller
{
//    /**
//     * @Route("/", name="homepage")
//     */
    public function indexAction(Request $request){
        return $this->render('default/index.html.twig') ;
    }

	public function lista_autAction(Request $request){
		$cars = (new AvailableCar())->getCars();
        return $this->render(
            'lista_aut.html.twig',
            array('cars' => $cars)
        );
    }

    public function lista_transakcjiAction(Request $request){
        $carService = $this->get('car_reader');
        $trans = $carService->getTransaction();
        return $this->render(
            'lista_transakcji.html.twig',
            array('trans' => $trans)
        );
    }


    public function wykonanieAction(Request $request){
        return $this->render('wykonanie.html.twig');
    }

    public function dziekujemyAction(Request $request){
        return $this->render('dziekujemy.html.twig', array(
			'platnosc' => $request->request->all() ,
        ));
    }

    public function wypozyczautoAction($id, Request $request){
        $carService = $this->get('car_reader');
        $car = $carService->getCar($id);

		
    	$param = array('dni' => 1,'id' => $car->getId(),'price' => $car->getPrice(),'mail' => ' ');

    	$form = $this->createFormBuilder($param)
        	->add('dni', IntegerType::class)
        	->add('id', HiddenType::class)
			->add('price', HiddenType::class)
			->add('mail', EmailType::class)
        	->add('rezerwacja',SubmitType::class,array('label'=>'Zatwierdź rezerwację'))
        	->getForm();


    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) 
		{
			$data = $form->getData();
			$tra = (new MakeReservation())->newReservation($data["id"],$data["dni"],$data["price"]);
			
			$session = $this->get('session');
			$session->set('tr_kwota', $data["dni"] * $data["price"]);
			$session->set('tr_opis','Zapłata na wynajecie ' . $car->getName() . ' na okres ' . $data["dni"]);
			$session->set('tr_control', $tra->getId());
            $session->set('tr_mail', $data["mail"]);


        	if ($tra->getId() > 0 )
				return $this->redirectToRoute('res_potwierdzenie');
			else
				return $this->redirectToRoute('res_odmowa');

    	}


		return $this->render('wypozyczauto.html.twig',array(
			'form' => $form->createView(),'car' => $car,
		));

    }

	public function  resodmowaAction(Request $request){
        return $this->render('odmowa.html.twig');
	}


	public function  respotwierdzenieAction(Request $request){
        return $this->render('potwierdzenie.html.twig');
	}

 	public function doPaymentAction(Request $request){
    	$session = $this->get('session');     
        $params = array(
            'id' => $this->getParameter('dotpay_id'),
            'amount' =>  $session->get('tr_kwota'),
            'description' =>  $session->get('tr_opis'),
            'control' =>  $session->get('tr_control'),
            'firstname' => '',
            'lastname' => '',
            'email' => $session->get('tr_mail'),
            'type' => 3,
            'api_version' => 'dev',
			'url' => 'http://v-ie.uek.krakow.pl/~s182704/app_dev.php/dziekujemy',			
        );
        $url = sprintf(
            '%s?%s',
            'https://ssl.dotpay.pl/test_payment/',
            http_build_query($params)
        );
        return new RedirectResponse($url);

    }

    public function confirmPaymentAction(Request $request){
        try {
        	$dotpay = new  DotpayCompletePayment( $request->request->all(), $this->getParameter('dotpay_pin'));

			if ($dotpay->isSuccessful()) {                  
				$payment = (new MakePayment())->createPayment($dotpay);
				$payment->savePayment();

 				$message = \Swift_Message::newInstance()
        		->setSubject('Potwierdzenie rezerwacji')
        		->setFrom("panda5@opoczta.pl")
        		->setTo($payment->getMail())
        		->setBody("Wpłata za rezerwacje nr ". $payment->getControl() ."  została potwierdzona.\n\rZapraszamy po odbiór auta.\n\rZespół Panda5" );
    			$this->get('mailer')->send($message);

            	return new Response('OK');
			}
        } catch (StoreException $e) {
            return new Response('FAIL');
        }
		return new Response('OK');

    }

}
