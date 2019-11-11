<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Validator\Constraints\DateTime;


class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index()
    {
      //  phpinfo();
      $actualDateTime = new \DateTime('now');
$d1 = new DateTime('2008-08-03 14:52:10');
$d2 = new DateTime('2008-01-03 11:11:10');
var_dump($d1 == $d2);
var_dump($d1 > $d2);
echo date_default_timezone_get();

        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

}
