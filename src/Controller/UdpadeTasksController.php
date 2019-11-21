<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\UserTask;
use App\Entity\User;

class UdpadeTasksController extends AbstractController
{
    /**
     * @Route("udpade/tasks", name="udpade_tasks")
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

    // change status old task and today start date
    public function markAsToDo()
    {
        // add 1 day to the current date to select tasks to do soon
        $actualDateTime = new \DateTime('now');
        $futureDateTime = $actualDateTime;
        $futureDateTime->modify('+1 day');

        // get all tasks who start today or before
        $tasks = $this->getDoctrine()
            ->getRepository(UserTask::class)
            ->findAllSmallerOrEgualThanActualDate($futureDateTime);

        foreach ($tasks as $task) {
            // if the statut is not 'En retard'           
            if ($task->getTaskStatut() !== 'En retard') {
                $entityManager = $this->getDoctrine()->getManager();
                $taskToModify = $entityManager->getRepository(UserTask::class)->find($task->getTaskId());

                // check if the task start date is today
                $actualDateTime = new \DateTime('now');
                $taskDate = date_format($task->getTaskStartDate(), 'Y-m-d');
                $actualDate = date_format($actualDateTime, 'Y-m-d');

                // modify statut "a faire" if task start date is today
                if ($taskDate == $actualDate) {
                    $taskToModify->setTaskStatut('A faire');
                } else {
                    $taskToModify->setTaskStatut('En retard');
                }
                $entityManager->flush();
            }
        }
        return 'Les statuts des tâches ont bien été changés';
    }

    // send a mail to eash user who have tasks to do
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

            $entityManager = $this->getDoctrine()->getManager();
            $tasks = $entityManager
                ->getRepository(UserTask::class)
                ->findAllTaskbyUserWithPastDate($userId, $todo, $late);

            // only if there are expired or today start tasks 
            if (!empty($tasks)) {

                //change the number of remember mail send by task
                foreach ($tasks as $task) {
                    $newNumemberOfRememberEmail = $task->getTaskNumberOfRemberEmail();
                    $newNumemberOfRememberEmail++;
                    $task->setTaskNumberOfRemberEmail($newNumemberOfRememberEmail);

                    // if the number max of aletrs is reachead delete the tasks of the list for sending
                    if ($task->getTaskNumberOfRemberEmail() > $task->getTaskNumberMaxEmail()) {
                        unset($tasks[array_search($task, $tasks)]);
                    }
                }
                $entityManager->flush();
      
                //check if there are still spots to send after filtering to not send an empty email
                if (!empty($tasks)) {
                    $message = (new \Swift_Message('Rappel de vos tâches à faire'))
                    ->setFrom($this->getParameter('app.myEmail'))
                    ->setTo($userEmail)
                    ->setBody(
                        $this->renderView(
                            'emails/remember.html.twig',
                            [
                                'userName' => $userName,
                                'tasks' => $tasks,
                            ]
                        ),
                        'text/html'
                    );
                    // send a remember mail to each user with undoing tasks
                    $mailer->send($message);
                }
            }
        }
        return 'Les emails de notification ont bien été envoyés';
    }
}
