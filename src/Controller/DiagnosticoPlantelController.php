<?php

namespace App\Controller;

use App\Entity\CondicionDocenteEducativa;
use App\Entity\CondicionEducativaAlumnos;
use App\Entity\DiagnosticoPlantel;
use App\Entity\Escuela;
use App\Entity\Estatus;
use App\Entity\GradoEnsenanza;
use App\Entity\Plantel;
use App\Form\DiagnosticoPlantelType;
use App\Repository\DiagnosticoPlantelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Tool\FileStorageManager;

/**
 * @Route("/diagnostico/plantel")
 */
class DiagnosticoPlantelController extends AbstractController
{
    /**
     * @Route("/{id}/index", name="diagnostico_plantel_index", methods={"GET"})
     */
    public function index(Plantel $plantel): Response
    {
        $em = $this->getDoctrine()->getManager();
        $diagnosticos=$em->getRepository(DiagnosticoPlantel::class)->findByPlantel($plantel->getId());

        return $this->render('diagnostico_plantel/index.html.twig', [
            'diagnosticos' => $diagnosticos,
            'plantel' => $plantel,
        ]);
    }

    /**
     * @Route("/{id}/new", name="diagnostico_plantel_new", methods={"GET","POST"})
     */
    public function new(Request $request, Plantel $plantel): Response
    {
        $diagnosticoPlantel = new DiagnosticoPlantel();
        $diagnosticoPlantel->setPlantel($plantel);

        $entityManager = $this->getDoctrine()->getManager();
        $numero=$entityManager->getRepository(DiagnosticoPlantel::class)->nextNumber($plantel->getId());
        $diagnosticoPlantel->setIddiagnosticoplantel($numero);

        $form = $this->createForm(DiagnosticoPlantelType::class, $diagnosticoPlantel);
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if (!$request->isXmlHttpRequest())
                throw $this->createAccessDeniedException();
            else
                if ($form->isValid()) {

                    $entityManager->persist($diagnosticoPlantel);
                    $entityManager->flush();
                    $this->addFlash('success', 'El diagnóstico del plantel fue registrado satisfactoriamente');
                    return $this->json(['url' => $this->generateUrl('diagnostico_plantel_edit', ['id' => $diagnosticoPlantel->getId()])]);
                } else {
                    $page = $this->renderView('diagnostico_plantel/_form.html.twig', [
                        'form' => $form->createView(),
                        'diagnostico_plantel' => $diagnosticoPlantel,
                        'plantel' => $plantel,
                    ]);
                    return $this->json(['form' => $page, 'error' => true,]);
                }

        return $this->render('diagnostico_plantel/new.html.twig', [
            'diagnostico_plantel' => $diagnosticoPlantel,
            'plantel' => $plantel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="diagnostico_plantel_show", methods={"GET"})
     */
    public function show(DiagnosticoPlantel $diagnosticoPlantel): Response
    {
        $entityManager=$this->getDoctrine()->getManager();

        $condicion_docente_educativas = $entityManager->getRepository(CondicionDocenteEducativa::class)->findByDiagnostico($diagnosticoPlantel);
        $condicion_educativa_alumnos = $entityManager->getRepository(CondicionEducativaAlumnos::class)->findByDiagnostico($diagnosticoPlantel);

        return $this->render('diagnostico_plantel/show.html.twig', [
            'diagnostico_plantel' => $diagnosticoPlantel,
            'condicion_docente_educativas' => $condicion_docente_educativas,
            'condicion_educativa_alumnos' => $condicion_educativa_alumnos,
            'eliminable'=>$this->esEliminable($diagnosticoPlantel)
        ]);
    }

    /**
     * @Route("/{id}/edit", name="diagnostico_plantel_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, DiagnosticoPlantel $diagnosticoPlantel): Response
    {
        $form = $this->createForm(DiagnosticoPlantelType::class, $diagnosticoPlantel);
        $form->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();

        $condicion_docente_educativas = $entityManager->getRepository(CondicionDocenteEducativa::class)->findByDiagnostico($diagnosticoPlantel);
        $condicion_educativa_alumnos = $entityManager->getRepository(CondicionEducativaAlumnos::class)->findByDiagnostico($diagnosticoPlantel);

        if ($form->isSubmitted())
            if (!$request->isXmlHttpRequest())
                throw $this->createAccessDeniedException();
            else
                if ($form->isValid()) {

                    if ($diagnosticoPlantel->getFile() != null) {
                        $ruta = $this->getParameter('storage_directory');
                        $rutaArchivo = $ruta . DIRECTORY_SEPARATOR . $diagnosticoPlantel->getDiagnosticoarchivo();
                        FileStorageManager::removeUpload($rutaArchivo);
                        $diagnosticoPlantel->setDiagnosticoarchivo(FileStorageManager::Upload($ruta, $diagnosticoPlantel->getFile()));
                        $diagnosticoPlantel->setFile(null);
                    }


                    $entityManager->persist($diagnosticoPlantel);
                    $entityManager->flush();
                    $this->addFlash('success', 'El diagnóstico del plantel fue actualizado satisfactoriamente');
                    return $this->json(['url' => $this->generateUrl('diagnostico_plantel_index', ['id' => $diagnosticoPlantel->getPlantel()->getId()], 1)]);
                } else {
                    $page = $this->renderView('diagnostico_plantel/_form.html.twig', [
                        'form' => $form->createView(),
                        'diagnostico_plantel' => $diagnosticoPlantel,
                    ]);
                    return $this->json(['form' => $page, 'error' => true,]);
                }

        return $this->render('diagnostico_plantel/edit.html.twig', [
            'diagnostico_plantel' => $diagnosticoPlantel,
            'condicion_docente_educativas' => $condicion_docente_educativas,
            'condicion_educativa_alumnos' => $condicion_educativa_alumnos,
            'action' => 'Actualizar',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="diagnostico_plantel_delete")
     */
    public function delete(Request $request, DiagnosticoPlantel $diagnosticoPlantel): Response
    {
        if (!$request->isXmlHttpRequest() || !$this->esEliminable($diagnosticoPlantel) ||!$this->isCsrfTokenValid('delete' . $diagnosticoPlantel->getId(), $request->query->get('_token')))
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $plantel=$diagnosticoPlantel->getPlantel()->getId();
        $em->remove($diagnosticoPlantel);
        $em->flush();
        $this->addFlash('success','El diagnóstico de plantel fue eliminado satisfactoriamente');
        return $this->json(['url' => $this->generateUrl('diagnostico_plantel_index',['id'=>$plantel])]);
    }

    /**
     * @Route("/{id}/descargar", name="diagnostico_plantel_descargar")
     */
    public function descargar(DiagnosticoPlantel $diagnosticoPlantel): Response
    {
        $ruta = $this->getParameter('storage_directory') . DIRECTORY_SEPARATOR . $diagnosticoPlantel->getDiagnosticoarchivo();
        return FileStorageManager::Download($ruta);
    }

    private function esEliminable(DiagnosticoPlantel $diagnostico)
    {
        $em = $this->getDoctrine()->getManager();
        $cde=$em->getRepository(CondicionDocenteEducativa::class)->findOneByDiagnostico($diagnostico);
        $cea=$em->getRepository(CondicionDocenteEducativa::class)->findOneByDiagnostico($diagnostico);
        return $cde==null && $cea==null;
    }


}
