<?php

namespace App\Controller;

/**
 * MembersController
 *
 * @package Abstract
 * @subpackage Controller
 * @author Pommine Fillatre <pommine@free.fr>
 */

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserType;
use App\Entity\User;
use App\Entity\UserTask;
use App\Service\FileUploader;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MembersController extends AbstractController
{
    /**
     * Show home page of members area
     * 
     * @Route("/membres", name="members")
     */
    public function index()
    {
        $userId = $this->getUser()->getId();
        $actualDateTime = new \DateTime('now');

        // Get count olds and futures tasks
        $repository = $this->getDoctrine()->getRepository(UserTask::class);
        $oldTasks = $repository->findAllSmallerThanActualDateByUserId($actualDateTime, $userId);
        $futureTasks = $repository->findAllBiggerThanActualDateByUserId($actualDateTime, $userId);
        $countOldTasks = count($oldTasks);
        $countFutureTasks = count($futureTasks);

        return $this->render('members/index.html.twig', [
            'userName' => $this->getUser()->getUserName(),
            'countOldTasks' => $countOldTasks,
            'countFutureTasks' => $countFutureTasks,
            'photo' => $this->getUser()->getUserPhoto(),
        ]);
    }

    /**
     * Modify the connected user
     * 
     * @Route("/membres/modifier", name="members_modify")
     */
    public function modifyUserData(Request $request, FileUploader $fileUploader, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        // reuse the create user form
        $form = $this->createForm(UserType::class, $user);
        $form->remove('termsAccepted');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->find($user->getId());

            // modify profil image
            $profilImage = $form['image']->getData();
            if ($profilImage) {
                // delete old file
                $oldUserPhoto = $user->getUserPhoto();
                if (!empty($oldUserPhoto)) {
                    $oldUserPhotoUrl = 'uploads/images/' . $oldUserPhoto;
                    unlink($oldUserPhotoUrl);
                }
                // change name and save new file 
                $imageFileName = $fileUploader->upload($profilImage);
                $user->setUserPhoto($imageFileName);
            }

            // modify password
            $password = $form['plainPassword']->getData();
            if ($password != $user->getPassword()) {
                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);
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
