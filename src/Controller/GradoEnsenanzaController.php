<?php

namespace App\Controller;

use App\Entity\GradoEnsenanza;
use App\Entity\Escuela;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GradoEnsenanzaController extends AbstractController
{
    /**
     * @Route("/{id}/findbyescuela", name="grado_ensenanza_find_by_escuela", methods={"GET"},options={"expose"=true})
     */
    public function findByEscuela(Request $request, Escuela $escuela): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $grado_array=$em->getRepository(GradoEnsenanza::class)->findByEscuelaJson($escuela->getId());
        return $grado_array!=null ? $this->json($grado_array) : $this->json(['empty'=>true]);
    }
}
