<?php

namespace App\Controller;

/**
   * MembersController
   * 
   * @package    Abstract
   * @subpackage Controller
   * @author     Pommine Fillatre <pommine@free.fr>
   */

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\UserTask;
use App\Service\FileUploader;


class MembersController extends AbstractController
{
    /**
     * Show home page of members area
     * @Route("/membres", name="members")
     */
    public function index()
    {
        $userId = $this->getUser()->getId();
        $actualDateTime = new \DateTime('now');

        // Get list of all tasks
        $repository = $this->getDoctrine()->getRepository(UserTask::class);
        $oldTasks = $repository->findAllSmallerThanActualDateByUserId($actualDateTime, $userId);
        $futureTasks = $repository->findAllBiggerThanActualDateByUserId($actualDateTime, $userId);

        return $this->render('members/index.html.twig', [
            'userName' => $this->getUser()->getUserName(),
            'countOldTasks' => count($oldTasks),
            'countFutureTasks' => count($futureTasks),
            'photo' => $this->getUser()->getUserPhoto(),
        ]);
    }

    /**
     * Modify the connected user
     * 
     * @Route("/membres/modifier", name="members_modify")
     */
    public function modifyUserData(Request $request, FileUploader $fileUploader)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form ->remove('termsAccepted');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->find($user->getId());

            $profilImage = $form['image']->getData();
            if ($profilImage) {

                // delete old file
                $oldUserPhoto = $user->getUserPhoto();
                if (!empty($oldUserPhoto)) {
                    $oldUserPhotoUrl = 'uploads/images/' .$oldUserPhoto;
                    unlink($oldUserPhotoUrl);
                }

                $imageFileName = $fileUploader->upload($profilImage);
                $user->setUserPhoto($imageFileName);
            }
           
            $entityManager->flush();
            $this->addFlash('success', 'Votre compte a bien été modifié');
            return $this->redirectToRoute('members');
        }

        return $this->render('members/userData.html.twig', [
            'user_form' => $form->createView(),
            'mainNavRegistration' => true, 
            'title' => 'Modifier vos informations personnelles',
        ]);
    }
}
