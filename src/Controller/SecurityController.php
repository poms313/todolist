<?php
// src/Controller/SecurityController.php

namespace App\Controller;

/**
   * SecurityController
   * 
   * @package    Abstract
   * @subpackage Controller
   * @author     Pommine Fillatre <pommine@free.fr>
   */

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class SecurityController extends AbstractController {

    /**
     * User login
     * 
     * @Route("/connexion", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils) 
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        //
        $form = $this->get('form.factory')
                ->createNamedBuilder(null)
                ->add('_username', null, ['label' => 'Votre email ou nom d\'utilisateur'])
                ->add('_password', PasswordType::class, ['label' => 'Mot de passe'])
                ->add('ok', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'button btn-block']])
                ->getForm();
                
        return $this->render('security/login.html.twig', [
                    'mainNavLogin' => true, 'title' => 'Connexion',
                    'login_form' => $form->createView(),
                    'last_username' => $lastUsername,
                    'error' => $error,
        ]);
    }

    /**
     * User logout
     * 
     * @Route("/logout", name="logout")
     */
    public function logout()
    {

    }

}