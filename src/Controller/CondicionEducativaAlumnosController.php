<?php

namespace App\Controller;

use App\Entity\DiagnosticoPlantel;
use App\Entity\Estatus;
use App\Entity\CondicionEducativaAlumnos;
use App\Form\CondicionEducativaAlumnosType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/condicion_educativa_alumno")
 */
class CondicionEducativaAlumnosController extends AbstractController
{

    /**
     * @Route("/{id}/new", name="condicion_educativa_alumno_new", methods={"GET","POST"},options={"expose"=true})
     */
    public function new(Request $request, DiagnosticoPlantel $diagnostico): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $condicion_educativa_alumno = new CondicionEducativaAlumnos();
        $condicion_educativa_alumno->setDiagnostico($diagnostico);
        $form = $this->createForm(CondicionEducativaAlumnosType::class, $condicion_educativa_alumno, ['action' => $this->generateUrl('condicion_educativa_alumno_new',['id'=>$diagnostico->getId()])]);
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($condicion_educativa_alumno);
                $entityManager->flush();
                return $this->json(['mensaje' => 'La condición educativa de los alumnos fue registrada satisfactoriamente',
                    'ccts' => $condicion_educativa_alumno->getEscuela()->getNombre(),
                    'numalumnas' => $condicion_educativa_alumno->getNumalumnas(),
                    'numalumnos' => $condicion_educativa_alumno->getNumalumnos(),
                    'grado' => $condicion_educativa_alumno->getGrado()->getNombre(),
                    'id' => $condicion_educativa_alumno->getId(),
                ]);
            } else {
                $page = $this->renderView('condicion_educativa_alumnos/_form.html.twig', [
                    'form' => $form->createView(),
                    'condicion_educativa_alumno' => $condicion_educativa_alumno,
                ]);
                return $this->json(['form' => $page, 'error' => true,]);
            }

        return $this->render('condicion_educativa_alumnos/new.html.twig', [
            'condicion_educativa_alumno' => $condicion_educativa_alumno,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="condicion_educativa_alumno_edit", methods={"GET","POST"},options={"expose"=true})
     */
    public function edit(Request $request, CondicionEducativaAlumnos $condicion_educativa_alumno): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(CondicionEducativaAlumnosType::class, $condicion_educativa_alumno, ['action' => $this->generateUrl('condicion_educativa_alumno_edit', ['id' => $condicion_educativa_alumno->getId()])]);
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($condicion_educativa_alumno);
                $em->flush();
                return $this->json(['mensaje' => 'La condición educativa de los alumnos fue actualizada satisfactoriamente',
                    'ccts' => $condicion_educativa_alumno->getEscuela()->getNombre(),
                    'numalumnas' => $condicion_educativa_alumno->getNumalumnas(),
                    'numalumnos' => $condicion_educativa_alumno->getNumalumnos(),
                    'grado' => $condicion_educativa_alumno->getGrado()->getNombre(),
                ]);
            } else {
                $page = $this->renderView('condicion_educativa_alumnos/_form.html.twig', [
                    'condicion_educativa_alumno' => $condicion_educativa_alumno,
                    'form' => $form->createView(),
                    'form_id' => 'condicion_educativa_alumno_edit',
                    'action' => 'Actualizar',
                ]);
                return $this->json(['form' => $page, 'error' => true]);
            }

        return $this->render('condicion_educativa_alumnos/new.html.twig', [
            'condicion_educativa_alumno' => $condicion_educativa_alumno,
            'title' => 'Editar condición educativa alumno',
            'action' => 'Actualizar',
            'form_id' => 'condicion_educativa_alumno_edit',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="condicion_educativa_alumno_delete")
     */
    public function delete(Request $request, CondicionEducativaAlumnos $condicion_educativa_alumno): Response
    {
        if (!$request->isXmlHttpRequest() || !$this->isCsrfTokenValid('delete' . $condicion_educativa_alumno->getId(), $request->query->get('_token')))
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($condicion_educativa_alumno);
        $em->flush();
        return $this->json(['mensaje' => 'La condición educativa de los alumnos fue eliminada satisfactoriamente']);
    }


}