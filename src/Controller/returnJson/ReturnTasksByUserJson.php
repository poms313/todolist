<?php

namespace App\Controller\returnJson;

use stdClass;
use App\Entity\UserTask;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;


class ReturnTasksByUserJson extends AbstractController
{
    /**
     * return a json array with the users'a tasks in a suitable format for calendar
     * 
     * @Route("/membres/ReturnTasks", name="userJson")
     */
    public function index()
    {
            header('Content-Type: application/json');

            $taskIdOwnerUser = $this->getUser()->getId();
            $repository=$this->getDoctrine()->getRepository(UserTask::class);
            $listTasks=$repository->findBy(
            ['taskIdOwnerUser' => $taskIdOwnerUser],
            );

            $forEncoding=array();

            foreach ($listTasks as $task) {

            // assign a color according to the status
            if ($task->getTaskStatut() == 'En retard') {
            $colorTask = 'rgb(0, 0, 0)';
            } elseif ($task->getTaskStatut() == 'En pause') {
            $colorTask = 'rgb(251, 255, 0)';
            } elseif ($task->getTaskStatut() == 'A faire') {
            $colorTask = 'rgb(255, 35, 35)';
            } elseif ($task->getTaskStatut() == 'En attente') {
            $colorTask = 'rgb(72, 255, 0)';
            } else {$colorTask = 'rgb(7, 5, 0)';}

            // create a new std objet with the same attributes as the tasks
            $object=new \stdClass();
            $object->title=$task->getTaskName();
            $object->start=$task->getTaskStartDate()->format('Y-m-d H:i');
            $object->end=$task->getTaskEndDate()->format('Y-m-d H:i');
            $object->allDay= true;
            $object->color= $colorTask;

            //push each task in array
            array_push($forEncoding, $object);
            }

            // translate into json
            return new JsonResponse($forEncoding);
    }

}