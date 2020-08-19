<?php

namespace App\Controller;

use App\Entity\ControlGastos;
use App\Entity\Escuela;
use App\Entity\Estatus;
use App\Entity\Plantel;
use App\Entity\PlanTrabajo;
use App\Entity\Proyecto;
use App\Entity\RendicionCuentas;
use App\Form\PlanTrabajoType;
use App\Repository\PlanTrabajoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Tool\FileStorageManager;

/**
 * @Route("/plantrabajo")
 */
class PlanTrabajoController extends AbstractController
{
    /**
     * @Route("/{id}/findbyplantel", name="plantrabajo_findby_plantel", methods={"GET"})
     */
    public function findByPlantel(Plantel $plantel): Response
    {
        $planestrabajo = $this->getDoctrine()->getRepository(PlanTrabajo::class)->findByPlantel($plantel);

        return $this->render('plan_trabajo/findbyplantel.html.twig', [
            'planestrabajo' => $planestrabajo,
            'plantel' => $plantel,
        ]);
    }

    /**
     * @Route("/{id}/index", name="plan_trabajo_index", methods={"GET"})
     */
    public function index(): Response
    {
        $em=$this->getDoctrine()->getManager();
        $planTrabajos=null;

        return $this->render('plan_trabajo/index.html.twig', [
            'plan_trabajos' => $planTrabajos,
        ]);
    }

    /**
     * @Route("/{id}/new", name="plan_trabajo_new", methods={"GET","POST"})
     */
    public function new(Request $request,Plantel $plantel): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $entityManager = $this->getDoctrine()->getManager();
        $planTrabajo = new PlanTrabajo();
        $planTrabajo->setPlantel($plantel);
        $numero=$entityManager->getRepository(PlanTrabajo::class)->nextNumber($planTrabajo->getPlantel()->getId());
        $planTrabajo->setNumero($numero);

        $form = $this->createForm(PlanTrabajoType::class, $planTrabajo, ['action' => $this->generateUrl('plan_trabajo_new',['id'=>$plantel->getId()])]);
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $entityManager->persist($planTrabajo);
                $entityManager->flush();
                return $this->json(['mensaje' => 'El plan de trabajo fue registrado satisfactoriamente',
                    'numero' => $planTrabajo->getNumero(),
                    'fechainicio' => $planTrabajo->getFechainicio()->format('Y-m-d'),
                    'id' => $planTrabajo->getId(),
                ]);
            } else {
                $page = $this->renderView('plan_trabajo/_form.html.twig', [
                    'form' => $form->createView(),
                    'plan_trabajo' => $planTrabajo,
                ]);
                return $this->json(['form' => $page, 'error' => true,]);
            }

        return $this->render('plan_trabajo/new.html.twig', [
            'plan_trabajo' => $planTrabajo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="plan_trabajo_show", methods={"GET"}, options={"expose"=true})
     */
    public function show(PlanTrabajo $planTrabajo): Response
    {
        $gastos=$this->getDoctrine()->getRepository(PlanTrabajo::class)->findSumaGastos($planTrabajo->getId());

        return $this->render('plan_trabajo/show.html.twig', [
            'plan_trabajo' => $planTrabajo,
            'montogastado' => $gastos,
            'saldofinal' => $planTrabajo->getMontoasignado()-$gastos,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="plan_trabajo_edit", methods={"GET","POST"},options={"expose"=true})
     */
    public function edit(Request $request, PlanTrabajo $planTrabajo): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(PlanTrabajoType::class, $planTrabajo, ['action' => $this->generateUrl('plan_trabajo_edit', ['id' => $planTrabajo->getId()])]);
        $form->handleRequest($request);
        $eliminable=$this->esEliminable($planTrabajo);
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                if ($planTrabajo->getFile() != null) {
                    $ruta = $this->getParameter('storage_directory');
                    $rutaArchivo = $ruta . DIRECTORY_SEPARATOR . $planTrabajo->getPlanarchivo();
                    FileStorageManager::removeUpload($rutaArchivo);
                    $planTrabajo->setPlanarchivo(FileStorageManager::Upload($ruta, $planTrabajo->getFile()));
                    $planTrabajo->setFile(null);
                }
                
                $em->persist($planTrabajo);
                $em->flush();
                return $this->json(['mensaje' => 'El plan de trabajo fue actualizado satisfactoriamente',
                    'numero' => $planTrabajo->getNumero(),
                    'fechainicio' => $planTrabajo->getFechainicio()->format('Y-m-d'),
                ]);
            } else {
                $page = $this->renderView('plan_trabajo/_form.html.twig', [
                    'plan_trabajo' => $planTrabajo,
                    'form' => $form->createView(),
                    'form_id' => 'plan_trabajo_edit',
                    'action' => 'Actualizar',
                    'eliminable' => $eliminable,
                ]);
                return $this->json(['form' => $page, 'error' => true]);
            }

        return $this->render('plan_trabajo/new.html.twig', [
            'plan_trabajo' => $planTrabajo,
            'title' => 'Editar plan de trabajo',
            'action' => 'Actualizar',
            'form_id' => 'plan_trabajo_edit',
            'form' => $form->createView(),
            'eliminable' => $eliminable,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="plan_trabajo_delete")
     */
    public function delete(Request $request, PlanTrabajo $planTrabajo): Response
    {
        if (!$request->isXmlHttpRequest() || !$this->isCsrfTokenValid('delete' . $planTrabajo->getId(), $request->query->get('_token')) || !$this->esEliminable($planTrabajo))
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($planTrabajo);
        $em->flush();
        return $this->json(['mensaje' => 'El plan de trabajo fue eliminado satisfactoriamente']);
    }

    private function esEliminable(PlanTrabajo $planTrabajo)
    {
        $em = $this->getDoctrine()->getManager();
        $control_gasto=$em->getRepository(ControlGastos::class)->findOneByPlantrabajo($planTrabajo);
        $rendicion_cuenta=$em->getRepository(RendicionCuentas::class)->findOneByPlantrabajo($planTrabajo);
        return $control_gasto==null && $rendicion_cuenta==null;
    }

    /**
     * @Route("/{id}/descargar", name="plan_trabajo_descargar")
     */
    public function descargar(PlanTrabajo $plantrabajo): Response
    {
        $ruta = $this->getParameter('storage_directory') . DIRECTORY_SEPARATOR . $plantrabajo->getPlanarchivo();
        return FileStorageManager::Download($ruta);
    }
}
