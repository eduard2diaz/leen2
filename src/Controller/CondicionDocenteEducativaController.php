<?php

namespace App\Controller;

use App\Entity\DiagnosticoPlantel;
use App\Entity\Escuela;
use App\Entity\Estatus;
use App\Entity\PlanTrabajo;
use App\Entity\CondicionDocenteEducativa;
use App\Entity\RendicionCuentas;
use App\Entity\ControlGastos;
use App\Form\CondicionDocenteEducativaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/condicion_docente_educativa")
 */
class CondicionDocenteEducativaController extends AbstractController
{

    /**
     * @Route("/{id}/new", name="condicion_docente_educativa_new", methods={"GET","POST"},options={"expose"=true})
     */
    public function new(Request $request, DiagnosticoPlantel $diagnostico): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $condicion_docente_educativa = new CondicionDocenteEducativa();
        $condicion_docente_educativa->setDiagnostico($diagnostico);
        $form = $this->createForm(CondicionDocenteEducativaType::class, $condicion_docente_educativa, ['action' => $this->generateUrl('condicion_docente_educativa_new',['id'=>$diagnostico->getId()])]);
        $form->handleRequest($request);
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($condicion_docente_educativa);
                $entityManager->flush();
                return $this->json(['mensaje' => 'La condición docente educativa fue registrada satisfactoriamente',
                    'ccts' => $condicion_docente_educativa->getEscuela()->getNombre(),
                    'curp' => $condicion_docente_educativa->getCurp(),
                    'nombre' => $condicion_docente_educativa->getNombre(),
                    'grado' => $condicion_docente_educativa->getGrado()->getNombre(),
                    'id' => $condicion_docente_educativa->getId(),
                ]);
            } else {
                $page = $this->renderView('condicion_docente_educativa/_form.html.twig', [
                    'form' => $form->createView(),
                    'condicion_docente_educativa' => $condicion_docente_educativa,
                ]);
                return $this->json(['form' => $page, 'error' => true,]);
            }

        return $this->render('condicion_docente_educativa/new.html.twig', [
            'condicion_docente_educativa' => $condicion_docente_educativa,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="condicion_docente_educativa_edit", methods={"GET","POST"},options={"expose"=true})
     */
    public function edit(Request $request, CondicionDocenteEducativa $condicion_docente_educativa): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(CondicionDocenteEducativaType::class, $condicion_docente_educativa, ['action' => $this->generateUrl('condicion_docente_educativa_edit', ['id' => $condicion_docente_educativa->getId()])]);
        $form->handleRequest($request);
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($condicion_docente_educativa);
                $em->flush();
                return $this->json(['mensaje' => 'La condición docente educativa fue actualizada satisfactoriamente',
                    'ccts' => $condicion_docente_educativa->getEscuela()->getNombre(),
                    'curp' => $condicion_docente_educativa->getCurp(),
                    'nombre' => $condicion_docente_educativa->getNombre(),
                    'grado' => $condicion_docente_educativa->getGrado()->getNombre(),
                ]);
            } else {
                $page = $this->renderView('condicion_docente_educativa/_form.html.twig', [
                    'condicion_docente_educativa' => $condicion_docente_educativa,
                    'form' => $form->createView(),
                    'form_id' => 'condicion_docente_educativa_edit',
                    'action' => 'Actualizar',
                ]);
                return $this->json(['form' => $page, 'error' => true]);
            }

        return $this->render('condicion_docente_educativa/new.html.twig', [
            'condicion_docente_educativa' => $condicion_docente_educativa,
            'title' => 'Editar condición docente educativa',
            'action' => 'Actualizar',
            'form_id' => 'condicion_docente_educativa_edit',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="condicion_docente_educativa_delete")
     */
    public function delete(Request $request, CondicionDocenteEducativa $condicion_docente_educativa): Response
    {
        if (!$request->isXmlHttpRequest() || !$this->isCsrfTokenValid('delete' . $condicion_docente_educativa->getId(), $request->query->get('_token')))
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($condicion_docente_educativa);
        $em->flush();
        return $this->json(['mensaje' => 'La condición docente educativa fue eliminada satisfactoriamente']);
    }


}