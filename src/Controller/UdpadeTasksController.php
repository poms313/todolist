<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\UserTask;
use App\Entity\User;

class UdpadeTasksController extends AbstractController
{
    /**
     * @Route("admin/udpade/tasks", name="udpade_tasks")
     */
    public function index(\Swift_Mailer $mailer)
    {
        $markAsToDo = $this->markAsToDo();
        $sendRememberEmail = $this->sendRememberEmail($mailer);
        
        return $this->render('udpade_tasks/index.html.twig', [
            'markAsToDo' => $markAsToDo,
            'sendRememberEmail' => $sendRememberEmail,
        ]);
    }

    public function markAsToDo()
    {
        // add 1 day to the current date to select tasks to do soon
        $actualDateTime = new \DateTime('now');
        $futureDateTime = $actualDateTime;
        $futureDateTime->modify('+1 day');

        // All tasks who start today or before
        $tasks = $this->getDoctrine()
            ->getRepository(UserTask::class)
            ->findAllSmallerOrEgualThanActualDate($futureDateTime);

        foreach($tasks as $task) {
            // if the statut is not 'A faire' or 'En retard'           
            if (strcmp($task->getTaskStatut(), 'A faire') == 1 || strcmp($task->getTaskStatut(), 'En retard') == 1) {

                $entityManager = $this->getDoctrine()->getManager();
                $taskToModify = $entityManager->getRepository(UserTask::class)->find($task->getTaskId());

                // check if the task start date is today
                $actualDateTime = new \DateTime('now');
                $firstDate = date_format($task->getTaskStartDate(), 'Y-m-d');
                $secondDate = date_format($actualDateTime, 'Y-m-d');

                // modify statut
                if ($firstDate == $secondDate) {
                    $taskToModify->setTaskStatut('A faire');
                } else {
                    $taskToModify->setTaskStatut('En retard');
                }
                $entityManager->flush();
                
            }
        }
        return 'Les statuts des tâches ont bien été changés';
    }

    public function sendRememberEmail($mailer)
    {
        $allUsers = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();    
        
        // for each user
        foreach ($allUsers as $user) {
            $userId = $user->getId();
            $userName = $user->getUserName();
            $userEmail = $user->getEmail();

            // For each user, check list of tasks with statut "a faire" ou "en retard" and number max of alerts is not reachead
            $todo = 'A faire';
            $late = 'En retard';
            $numberAlertMax = $this->getParameter('app.numberOfRememberEmail');

            $entityManager = $this->getDoctrine()->getManager();
            $tasks = $entityManager
                ->getRepository(UserTask::class)
                ->findAllTaskbyUserWithPastDate($userId, $todo, $late, $numberAlertMax);

            // only if there are expired or today start tasks
            if (!empty($tasks)) {

                //change the number of remember mail send by task
                foreach ($tasks as $task) {
                    $newNumemberOfRememberEmail = $task->getTaskNumberOfRemberEmail();
                    $newNumemberOfRememberEmail ++;
                    $task->setTaskNumberOfRemberEmail($newNumemberOfRememberEmail);
                }
                $entityManager->flush();

                // send a remember mail to each user
                $message = (new \Swift_Message('Rappel de vos tâches à faire'))
                    ->setFrom($this->getParameter('app.myEmail'))
                    ->setTo($userEmail)
                    ->setBody(
                        $this->renderView(
                            'emails/remember.html.twig', [
                            'userName' => $userName,
                            'tasks' => $tasks,
                        ]),
                        'text/html'
                    );
                $mailer->send($message);
            }
        }
        return 'Les emails de notification ont bien été envoyés';        
       }


}
