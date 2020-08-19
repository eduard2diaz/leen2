<?php

namespace App\Controller;

use App\Entity\Estatus;
use App\Entity\PlanTrabajo;
use App\Entity\RendicionCuentas;
use App\Entity\TipoAccion;
use App\Form\TipoAccionType;
use App\Twig\EstatusExtension;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tipoaccion")
 */
class TipoAccionController extends AbstractController
{
    /**
     * @Route("/", name="tipo_accion_index", methods={"GET"})
     */
    public function index(): Response
    {
        $tipoaccions = $this->getDoctrine()->getRepository(TipoAccion::class)->findAll();

        return $this->render('tipo_accion/index.html.twig', [
            'tipo_accions' => $tipoaccions,
        ]);
    }

    /**
     * @Route("/new", name="tipo_accion_new", methods={"GET","POST"},options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $tipoaccion = new TipoAccion();
        $form = $this->createForm(TipoAccionType::class, $tipoaccion, ['action' => $this->generateUrl('tipo_accion_new')]);
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($tipoaccion);
                $entityManager->flush();
                return $this->json(['mensaje' => 'El tipo de acción fue registrado satisfactoriamente',
                    'accion' => $tipoaccion->getAccion(),
                    'id' => $tipoaccion->getId(),
                ]);
            } else {
                $page = $this->renderView('tipo_accion/_form.html.twig', [
                    'form' => $form->createView(),
                ]);
                return $this->json(['form' => $page, 'error' => true,]);
            }

        return $this->render('tipo_accion/new.html.twig', [
            'tipoaccion' => $tipoaccion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="tipo_accion_show", methods={"GET","POST"},options={"expose"=true})
     */
    public function show(Request $request, TipoAccion $tipoaccion): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('tipo_accion/show.html.twig', [
            'tipoaccion' => $tipoaccion,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tipo_accion_edit", methods={"GET","POST"},options={"expose"=true})
     */
    public function edit(Request $request, TipoAccion $tipoaccion): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(TipoAccionType::class, $tipoaccion, ['action' => $this->generateUrl('tipo_accion_edit', ['id' => $tipoaccion->getId()])]);
        $form->handleRequest($request);

        $eliminable=$this->esEliminable($tipoaccion);
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($tipoaccion);
                $em->flush();
                return $this->json(['mensaje' => 'El tipo de acción fue actualizado satisfactoriamente',
                    'accion' => $tipoaccion->getAccion(),
                ]);
            } else {
                $page = $this->renderView('tipo_accion/_form.html.twig', [
                    'tipoaccion' => $tipoaccion,
                    'eliminable'=>$eliminable,
                    'form' => $form->createView(),
                    'form_id' => 'tipoaccion_edit',
                    'action' => 'Actualizar',
                ]);
                return $this->json(['form' => $page, 'error' => true]);
            }

        return $this->render('tipo_accion/new.html.twig', [
            'tipoaccion' => $tipoaccion,
            'eliminable'=>$eliminable,
            'title' => 'Editar tipo de acción',
            'action' => 'Actualizar',
            'form_id' => 'tipoaccion_edit',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="tipo_accion_delete")
     */
    public function delete(Request $request, TipoAccion $tipoaccion): Response
    {
        if (!$request->isXmlHttpRequest() || !$this->isCsrfTokenValid('delete' . $tipoaccion->getId(), $request->query->get('_token')) || false==$this->esEliminable($tipoaccion))
            throw $this->createAccessDeniedException();


        $em = $this->getDoctrine()->getManager();
        $em->remove($tipoaccion);
        $em->flush();
        return $this->json(['mensaje' => 'El tipo de acción fue eliminado satisfactoriamente']);
    }

    private function esEliminable(TipoAccion $tipoaccion)
    {
        $em = $this->getDoctrine()->getManager();
        $rendicionCuenta=$em->getRepository(RendicionCuentas::class)->findOneByTipoAccion($tipoaccion);
        $planTrabajo=$em->getRepository(PlanTrabajo::class)->findOneByTipoAccion($tipoaccion);
        return $rendicionCuenta==null && $planTrabajo==null;
    }

}