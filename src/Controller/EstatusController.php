<?php

namespace App\Controller;

use App\Entity\Estatus;
use App\Entity\TipoAccion;
use App\Form\EstatusType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/estatus")
 */
class EstatusController extends AbstractController
{
    /**
     * @Route("/", name="estatus_index", methods={"GET"})
     */
    public function index(): Response
    {
        $estatuss = $this->getDoctrine()
            ->getRepository(Estatus::class)
            ->findAll();

        return $this->render('estatus/index.html.twig', [
            'estatuses' => $estatuss,
        ]);
    }

    /**
     * @Route("/new", name="estatus_new", methods={"GET","POST"},options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $estatus = new Estatus();
        $form = $this->createForm(EstatusType::class, $estatus, ['action' => $this->generateUrl('estatus_new')]);
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($estatus);
                $entityManager->flush();
                return $this->json(['mensaje' => 'El estatus fue registrado satisfactoriamente',
                    'estatus' => $estatus->getEstatus(),
                    'id' => $estatus->getId(),
                ]);
            } else {
                $page = $this->renderView('estatus/_form.html.twig', [
                    'form' => $form->createView(),
                ]);
                return $this->json(['form' => $page, 'error' => true,]);
            }

        return $this->render('estatus/new.html.twig', [
            'estatus' => $estatus,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="estatus_edit", methods={"GET","POST"},options={"expose"=true})
     */
    public function edit(Request $request, Estatus $estatus): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(EstatusType::class, $estatus, ['action' => $this->generateUrl('estatus_edit', ['id' => $estatus->getId()])]);
        $form->handleRequest($request);

        $eliminable=$this->esEliminable($estatus);
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($estatus);
                $em->flush();
                return $this->json(['mensaje' => 'El estatus fue actualizado satisfactoriamente',
                    'estatus' => $estatus->getEstatus(),
                ]);
            } else {
                $page = $this->renderView('estatus/_form.html.twig', [
                    'estatus' => $estatus,
                    'eliminable'=>$eliminable,
                    'form' => $form->createView(),
                    'form_id' => 'estatus_edit',
                    'action' => 'Actualizar',
                ]);
                return $this->json(['form' => $page, 'error' => true]);
            }

        return $this->render('estatus/new.html.twig', [
            'estatus' => $estatus,
            'eliminable'=>$eliminable,
            'title' => 'Editar estatus',
            'action' => 'Actualizar',
            'form_id' => 'estatus_edit',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="estatus_delete")
     */
    public function delete(Request $request, Estatus $estatus): Response
    {
        if (!$request->isXmlHttpRequest() || !$this->isCsrfTokenValid('delete' . $estatus->getId(), $request->query->get('_token')) || false==$this->esEliminable($estatus))
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($estatus);
        $em->flush();
        return $this->json(['mensaje' => 'El estatus fue eliminado satisfactoriamente']);
    }

    private function esEliminable(Estatus $estatus)
    {
        $em = $this->getDoctrine()->getManager();
        $tipoaccion=$em->getRepository(TipoAccion::class)->findOneByEstatus($estatus);
        return $tipoaccion==null;
    }

}