<?php

namespace App\Controller;

/**
 * ContactController
 * 
 * @package    Abstract
 * @subpackage Controller
 * @author     Pommine Fillatre <pommine@free.fr>
 */

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class ContactController extends AbstractController
{
    /**
     * Create contact form and send a email 
     * 
     * @Route("contact", name="contact")
     */
    public function index(Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();

            $message = (new \Swift_Message('Message via le formulaire de contact'))
                ->setFrom($contactFormData['from'])
                ->setTo('pommine@free.fr')
                ->setBody(
                    $contactFormData['message'],
                    'text/html'
                );

            $mailer->send($message);

            $this->addFlash('success', 'Votre message a bien été envoyé');
            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            'contact_form' => $form->createView(),
        ]);
    }
}
