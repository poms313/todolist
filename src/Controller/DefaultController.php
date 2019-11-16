<?php

namespace App\Controller;

/**
 * DefaultController
 * 
 * @package    Abstract
 * @subpackage Controller
 * @author     Pommine Fillatre <pommine@free.fr>
 */

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends AbstractController
{
    /**
     * Home page
     * 
     * @Route("", name="home")
     */
    public function index()
    {
        return $this->render('home.html.twig', [
            // 'number' => $number,
        ]);
    }
}
