<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\TaskType;
use App\Entity\UserTask;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Knp\Component\Pager\PaginatorInterface;


class TaskController extends AbstractController
{

    /**
     * Show list all tasks filter by user id
     * 
     * @Route("membres/taches", name="task")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $taskIdOwnerUser = $this->getUser()->getId();
        $userName = $this->getUser()->getUserName();

        // get the entity manager
        $em = $this->getDoctrine()->getManager();
        $userTaskRepository = $em->getRepository(UserTask::class);

        // Find all the data on the UserTask table
        $listAllUserTasks = $userTaskRepository->findBy(
            ['taskIdOwnerUser' => $taskIdOwnerUser],
            ['taskStartDate' => 'ASC']
        );

        // Paginate the results of the query
        $tasks = $paginator->paginate(
            $listAllUserTasks,
            $request->query->getInt('page', 1),
            // Items per page
            $this->getParameter("app.numberTasksByPage")
        );

        return $this->render('task/listAllTasks.html.twig', [
            'tasks' => $tasks,
            'userName' => $userName,
        ]);
    }

    /**
     * Create new task
     * @Route("membres/tache/nouvelle", name="newTask")
     */
    public function createNewTask(Request $request, ValidatorInterface $validator)
    {
        $newUserTask = new UserTask();
        $form = $this->createForm(TaskType::class, $newUserTask);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newUserTask->setTaskIdOwnerUser($this->getUser());
            $newUserTask->setTaskNumberOfRemberEmail(0);
            $newUserTask->setTaskStatut("En attente");

            // check if no errors
            $errors = $validator->validate($newUserTask);
            if (count($errors) > 0) {
                return new Response((string) $errors, 400);
            }

            // Save the task!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newUserTask);
            $entityManager->flush();
            $this->addFlash('success', 'Votre tache a bien été enregistrée.');
            return $this->redirectToRoute('newTask');
        }

        return $this->render('task/newTask.html.twig', [
            'task_form' => $form->createView(),
        ]);
    }


    /**
     * Delete tasks
     * 
     * @Route("membres/taches/supprimer/{id}", methods={"GET","HEAD"})
     * @param int $ id id of task to delete
     */
    public function deleteTask(int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $taskToDelete = $entityManager->getRepository(UserTask::class)->find($id);
        $entityManager->remove($taskToDelete);
        $entityManager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée!');
        return $this->redirectToRoute('task');
    }

    /**
     * Accomplish a task and delete it
     * 
     * @Route("membres/taches/fait/{id}", methods={"GET","HEAD"})
     * @param int $ id id of task to finish
     */
    public function makeTask(int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $taskToDelete = $entityManager->getRepository(UserTask::class)->find($id);
        $entityManager->remove($taskToDelete);
        $entityManager->flush();

        $this->addFlash('success', 'Félicitations! La tâche a bien été marquée comme accomplie!');
        return $this->redirectToRoute('task');
    }


    /**
     * Paused a task
     * 
     * @Route("membres/taches/pause/{id}", methods={"GET","HEAD"})
     * @param int $ id id of task to paused
     */
    public function pausedTask(int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $taskTofinish = $entityManager->getRepository(UserTask::class)->find($id);
        $taskTofinish->setTaskStatut('En pause');
        $entityManager->flush();

        $this->addFlash('success', 'La tâche a bien été mise en pause!');
        return $this->redirectToRoute('task');
    }

    /**
     * Restart a task
     * 
     * @Route("membres/taches/reprendre/{id}", methods={"GET","HEAD"})
     * @param int $ id id of task to paused
     */
    public function restartTask(int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $taskTofinish = $entityManager->getRepository(UserTask::class)->find($id);
        $taskTofinish->setTaskStatut('En attente');
        $entityManager->flush();

        $this->addFlash('success', 'La tâche a bien été remise en cours!');
        return $this->redirectToRoute('task');
    }
}
