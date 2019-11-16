<?php
// src/Controller/AdminController.php

namespace App\Controller;

/**
 * AdminController
 * 
 * @package    Abstract
 * @subpackage Controller
 * @author     Pommine Fillatre <pommine@free.fr>
 */

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ContactType;



class AdminController extends AbstractController
{
    function __construct()
    { }

    /**
     * Home page of admin area
     * 
     * @Route("admin", name="admin")
     */
    public function index()
    {

        // Get list of all users
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * Delete users
     * 
     * @Route("/admin/supprimer/{id}", methods={"GET","HEAD"})
     * @param int $ id id of user to delete
     */
    public function deleteUser(int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Le membre a bien été supprimé!');

        return $this->redirectToRoute('admin');
    }

    /**
     * Send message to the user
     * 
     * @Route("/admin/message/{id}", methods={"GET","HEAD", "POST"})
     * @param int $ id id of user to send message
     */
    public function sendEmailToUser(int $id, Request $request, \Swift_Mailer $mailer)
    {
        // reused contact form
        $form = $this->createForm(ContactType::class);
        $form->remove('from');
        $form->remove('name');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->find($id);
            $contactFormData = $form->getData();

            $message = (new \Swift_Message('Message de la part de votre gestionnaire de tâches'))
                ->setFrom($this->getParameter('app.myEmail'))
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/sendToMember.html.twig',
                        [
                            'userName' => $user->getUserName(),
                            'message' => $contactFormData['message'],
                        ]
                    ),
                    'text/html'
                );

            $mailer->send($message);

            $this->addFlash('success', 'Le message a bien été envoyé!');

            return $this->redirectToRoute('admin');
        }

        return $this->render('/admin/sendmessagetomembers.html.twig', [
            'contact_form' => $form->createView(),
        ]);
    }
}
