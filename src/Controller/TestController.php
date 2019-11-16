<?php

namespace App\Controller;

use stdClass;
use App\Entity\UserTask;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;


class TestController extends AbstractController
{
    /**
     * @Route("test", name="test")
     */
    public function index()
    {

        
        return $this->render('test/index.html.twig', [
           // 'markAsToDo' => $markAsToDo,
           
        ]);
    }

}
