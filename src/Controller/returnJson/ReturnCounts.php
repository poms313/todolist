<?php

namespace App\Controller\returnJson;

use App\Entity\UserTask;
use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;


class ReturnCounts extends AbstractController
{
    /**
     * Return a json array witn the count of all users and the count of all tasks
     * 
     * @Route("/membres/ReturnCounts", name="counts")
     */
    public function index()
    {
            header('Content-Type: application/json');
            
            $repository=$this->getDoctrine()->getRepository(User::class);
            $listUsers=$repository->findAll();
            $countListTasks=count($listUsers);

            $repository=$this->getDoctrine()->getRepository(UserTask::class);
            $listTasks=$repository->findAll();
            $countListUsers=count($listTasks);

            $forEncoding=array();
            array_push($forEncoding, $countListUsers, $countListTasks);
        
            // translate into json
            return new JsonResponse($forEncoding);
    }

}