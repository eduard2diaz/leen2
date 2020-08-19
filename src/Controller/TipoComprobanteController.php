<?php

namespace App\Controller;

use App\Entity\ControlGastos;
use App\Entity\TipoComprobante;
use App\Form\TipoComprobanteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tipocomprobante")
 */
class TipoComprobanteController extends AbstractController
{
    /**
     * @Route("/", name="tipo_comprobante_index", methods={"GET"})
     */
    public function index(): Response
    {
        $tipocomprobante = $this->getDoctrine()->getRepository(TipoComprobante::class)->findAll();

        return $this->render('tipo_comprobante/index.html.twig', [
            'tipo_comprobantes' => $tipocomprobante,
        ]);
    }

    /**
     * @Route("/new", name="tipo_comprobante_new", methods={"GET","POST"},options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $tipocomprobante = new TipoComprobante();
        $form = $this->createForm(TipoComprobanteType::class, $tipocomprobante, ['action' => $this->generateUrl('tipo_comprobante_new')]);
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($tipocomprobante);
                $entityManager->flush();
                return $this->json(['mensaje' => 'El tipo de comprobante fue registrado satisfactoriamente',
                    'comprobante' => $tipocomprobante->getComprobante(),
                    'id' => $tipocomprobante->getId(),
                ]);
            } else {
                $page = $this->renderView('tipo_comprobante/_form.html.twig', [
                    'form' => $form->createView(),
                ]);
                return $this->json(['form' => $page, 'error' => true,]);
            }

        return $this->render('tipo_comprobante/new.html.twig', [
            'tipocomprobante' => $tipocomprobante,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="tipo_comprobante_show", methods={"GET","POST"},options={"expose"=true})
     */
    public function show(Request $request, TipoComprobante $tipocomprobante): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('tipo_comprobante/show.html.twig', [
            'tipocomprobante' => $tipocomprobante,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tipo_comprobante_edit", methods={"GET","POST"},options={"expose"=true})
     */
    public function edit(Request $request, TipoComprobante $tipocomprobante): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(TipoComprobanteType::class, $tipocomprobante, ['action' => $this->generateUrl('tipo_comprobante_edit', ['id' => $tipocomprobante->getId()])]);
        $form->handleRequest($request);

        $eliminable=$this->esEliminable($tipocomprobante);
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($tipocomprobante);
                $em->flush();
                return $this->json(['mensaje' => 'El tipo de comprobante fue actualizado satisfactoriamente',
                    'comprobante' => $tipocomprobante->getComprobante(),
                ]);
            } else {
                $page = $this->renderView('tipo_comprobante/_form.html.twig', [
                    'tipocomprobante' => $tipocomprobante,
                    'eliminable'=>$eliminable,
                    'form' => $form->createView(),
                    'form_id' => 'tipocomprobante_edit',
                    'action' => 'Actualizar',
                ]);
                return $this->json(['form' => $page, 'error' => true]);
            }

        return $this->render('tipo_comprobante/new.html.twig', [
            'tipocomprobante' => $tipocomprobante,
            'eliminable'=>$eliminable,
            'title' => 'Editar tipo de comprobante',
            'action' => 'Actualizar',
            'form_id' => 'tipocomprobante_edit',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="tipo_comprobante_delete")
     */
    public function delete(Request $request, TipoComprobante $tipocomprobante): Response
    {
        if (!$request->isXmlHttpRequest() || !$this->isCsrfTokenValid('delete' . $tipocomprobante->getId(), $request->query->get('_token')) || false==$this->esEliminable($tipocomprobante))
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($tipocomprobante);
        $em->flush();
        return $this->json(['mensaje' => 'El tipo de comprobante fue eliminado satisfactoriamente']);
    }

    private function esEliminable(TipoComprobante $tipocomprobante)
    {
        $em = $this->getDoctrine()->getManager();
        $controlGastos=$em->getRepository(ControlGastos::class)->findOneByTipoComprobante($tipocomprobante);
        return $controlGastos==null;
    }

}