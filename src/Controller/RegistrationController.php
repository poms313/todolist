<?php
// src/Controller/RegistrationController.php
namespace App\Controller;

/**
 * RegistrationController
 * 
 * @package    Abstract
 * @subpackage Controller
 * @author     Pommine Fillatre <pommine@free.fr>
 */

use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\FileUploader;


class RegistrationController extends AbstractController
{

    /**
     * Create new user
     * 
     * @Route("/inscription", name="register"))
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer, ValidatorInterface $validator, FileUploader $fileUploader): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the password
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // configure the user
            $user->setIsActive(true);
            $user->setAccountCreatedDate(new \DateTime('now'));
            $user->setRoles(['ROLE_USER']);

            // if profil image change name and save it
            $profilImage = $form['image']->getData();
            if ($profilImage) {
                $imageFileName = $fileUploader->upload($profilImage);
                $user->setUserPhoto($imageFileName);
            }

            // chek if no errors
            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                return new Response((string) $errors, 400);
            }

            // Save the User!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // Send a email to the User and add flash message!
            $this->sendEmailConfirmRegistration($user->getUserName(), $user->getEmail(), $mailer);
            $this->addFlash('success', 'Votre compte a bien été enregistré.');

            return $this->redirectToRoute('members');
        }
        return $this->render('members/userData.html.twig', [
            'user_form' => $form->createView(),
            'mainNavRegistration' => true,
            'title' => 'Inscription'
        ]);
    }


    /**
     * Send welcome message to the user
     *
     * @param string $userName name of user to send message
     * @param string $userEmail nemail of user to send message
     */
    public function sendEmailConfirmRegistration($userName, $userEmail, $mailer)
    {
        $message = (new \Swift_Message('Bienvenue sur notre site'))
            ->setFrom($this->getParameter('app.myEmail'))
            ->setTo($userEmail)
            ->setBody(
                $this->renderView(
                    'emails/registration.html.twig',
                    ['name' => $userName]
                ),
                'text/html'
            );

        $mailer->send($message);
    }
}
