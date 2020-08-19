<?php

namespace App\Controller;

use App\Entity\ControlGastos;
use App\Entity\Escuela;
use App\Entity\Estatus;
use App\Entity\PlanTrabajo;
use App\Form\ControlGastosType;
use App\Repository\ControlGastosRepository;
use App\Tool\FileStorageManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/control/gastos")
 */
class ControlGastosController extends AbstractController
{
    /**
     * @Route("/{id}/index", name="control_gastos_index", methods={"GET"})
     */
    public function index(PlanTrabajo $planTrabajo): Response
    {
        $em=$this->getDoctrine()->getManager();
        $gastos=$consulta = $em->getRepository(ControlGastos::class)->findByPlantrabajo($planTrabajo);

        return $this->render('control_gastos/index.html.twig', [
            'control_gastos' => $gastos,
            'plantrabajo' => $planTrabajo,
        ]);
    }

    /**
     * @Route("/{id}/new", name="control_gastos_new", methods={"GET","POST"})
     */
    public function new(PlanTrabajo $planTrabajo, Request $request): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $controlgasto = new ControlGastos();
        $controlgasto->setPlanTrabajo($planTrabajo);

        $entityManager = $this->getDoctrine()->getManager();
        $numero=$entityManager->getRepository(ControlGastos::class)->nextNumber($controlgasto->getPlanTrabajo()->getId());
        $controlgasto->setNumeroCOmprobante($numero);
        $form = $this->createForm(ControlGastosType::class, $controlgasto, ['action' => $this->generateUrl('control_gastos_new',['id'=>$planTrabajo->getId()])]);
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {

                $entityManager->persist($controlgasto);
                $entityManager->flush();
                return $this->json(['mensaje' => 'El control de gastos fue registrado satisfactoriamente',
                    'tipocomprobante' => $controlgasto->getTipoComprobante()->__toString(),
                    'fecha' => $controlgasto->getFechacaptura()->format('Y-m-d'),
                    'numero_comprobante'=>$numero,
                    'id' => $controlgasto->getId(),
                ]);
            } else {
                $page = $this->renderView('control_gastos/_form.html.twig', [
                    'form' => $form->createView(),
                    'control_gastos' => $controlgasto,
                    'plantrabajo' => $planTrabajo,
                ]);
                return $this->json(['form' => $page, 'error' => true,]);
            }

        return $this->render('control_gastos/new.html.twig', [
            'control_gastos' => $controlgasto,
            'plantrabajo' => $planTrabajo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="control_gastos_show", methods={"GET"}, options={"expose"=true})
     */
    public function show(ControlGastos $controlgastos, Request $request): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('control_gastos/show.html.twig', [
            'control_gastos' => $controlgastos,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="control_gastos_edit", methods={"GET","POST"},options={"expose"=true})
     */
    public function edit(Request $request, ControlGastos $controlgastos): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(ControlGastosType::class, $controlgastos, ['action' => $this->generateUrl('control_gastos_edit', ['id' => $controlgastos->getId()])]);
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                if ($controlgastos->getFile() != null) {
                    $ruta = $this->getParameter('storage_directory');
                    $rutaArchivo = $ruta . DIRECTORY_SEPARATOR . $controlgastos->getControlarchivos();
                    FileStorageManager::removeUpload($rutaArchivo);
                    $controlgastos->setControlarchivos(FileStorageManager::Upload($ruta, $controlgastos->getFile()));
                    $controlgastos->setFile(null);
                }

                $em->persist($controlgastos);
                $em->flush();
                return $this->json(['mensaje' => 'El control de gastos fue actualizado satisfactoriamente',
                    'tipocomprobante' => $controlgastos->getTipoComprobante()->__toString(),
                    'fecha' => $controlgastos->getFechacaptura()->format('Y-m-d'),
                ]);
            } else {
                $page = $this->renderView('control_gastos/_form.html.twig', [
                    'control_gastos' => $controlgastos,
                    'form' => $form->createView(),
                    'form_id' => 'control_gastos_edit',
                    'action' => 'Actualizar',
                ]);
                return $this->json(['form' => $page, 'error' => true]);
            }

        return $this->render('control_gastos/new.html.twig', [
            'control_gastos' => $controlgastos,
            'title' => 'Editar control de gastos',
            'action' => 'Actualizar',
            'form_id' => 'control_gastos_edit',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="control_gastos_delete")
     */
    public function delete(Request $request, ControlGastos $controlgastos): Response
    {
        if (!$request->isXmlHttpRequest() || !$this->isCsrfTokenValid('delete' . $controlgastos->getId(), $request->query->get('_token')))
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($controlgastos);
        $em->flush();
        return $this->json(['mensaje' => 'El control de gastos fue eliminado satisfactoriamente']);
    }

    /**
     * @Route("/{id}/descargar", name="control_gastos_descargar")
     */
    public function descargar(ControlGastos $controlGastos): Response
    {
        $ruta = $this->getParameter('storage_directory') . DIRECTORY_SEPARATOR . $controlGastos->getControlarchivos();
        return FileStorageManager::Download($ruta);
    }
}
