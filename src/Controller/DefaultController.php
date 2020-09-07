<?php

namespace App\Controller;

use App\Form\ContactoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/estatica/{page}", name="estatica", requirements={"page" = "api|cookies|faq|nosotros|privacidad|terminos|guia"})
     */
    public function estatica($page)
    {
        return $this->render('default/estatica/'.$page.'.html.twig');
    }

    /**
     * @Route("/guia", name="guia")
     */
    public function guia()
    {
        return $this->render('default/estatica/guia.html.twig');
    }

}
