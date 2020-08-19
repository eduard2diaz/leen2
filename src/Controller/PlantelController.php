<?php


namespace App\Controller;

use App\Entity\Plantel;
use App\Entity\PlanTrabajo;
use App\Entity\Proyecto;
use App\Entity\DiagnosticoPlantel;
use App\Entity\Escuela;
use App\Form\PlantelType;
use App\Form\FiltroType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/plantel")
 */
class PlantelController extends AbstractController
{
    /**
     * @Route("/", name="plantel_index", methods={"GET","POST"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(FiltroType::class, [], ['action' => $this->generateUrl('plantel_index')]);
        $form->handleRequest($request);

        $dql = "SELECT e FROM App:Plantel e";
        $data = $request->query->get('filtro');

        if ($form->isSubmitted() || $data != "") {
            if ($form->isSubmitted())
                $data = $form->getData()["filtro"];

            $dql = "SELECT e FROM App:Plantel e WHERE e.nombre LIKE :value";
            $query = $this->getDoctrine()->getManager()->createQuery($dql);
            $query->setParameter('value', "%" . $data . "%");
        } else {
            $query = $this->getDoctrine()->getManager()->createQuery($dql);
        }

        $plantels = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            $this->getParameter('knp_num_items_per_page') /*limit per page*/
        );

        return $this->render('plantel/index.html.twig', [
            'plantels' => $plantels,
            'filtro' => $data,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="plantel_new", methods={"GET","POST"},options={"expose"=true})
     */
    public function new(Request $request): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $plantel = new Plantel();
        $form = $this->createForm(PlantelType::class, $plantel, ['action' => $this->generateUrl('plantel_new')]);
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($plantel);
                $entityManager->flush();

                $this->addFlash('success', 'El plantel fue registrado satisfactoriamente');
                return $this->json([
                    'url' => $this->generateUrl('escuela_findby_plantel',['id'=>$plantel->getId()])
                ]);
            } else {
                $page = $this->renderView('plantel/_form.html.twig', [
                    'plantel' => $plantel,
                    'form' => $form->createView(),
                ]);
                return $this->json(['form' => $page, 'error' => true,]);
            }

        return $this->render('plantel/new.html.twig', [
            'plantel' => $plantel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="plantel_show", methods={"GET"},options={"expose"=true})
     */
    public function show(Plantel $plantel): Response
    {
        return $this->render('plantel/show.html.twig', [
            'plantel' => $plantel
        ]);
    }


    /**
     * @Route("/{id}/edit", name="plantel_edit", methods={"GET","POST"},options={"expose"=true})
     */
    public function edit(Request $request, Plantel $plantel): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $eliminable=$this->esEliminable($plantel);
        $form = $this->createForm(PlantelType::class, $plantel, ['action' => $this->generateUrl('plantel_edit', ['id' => $plantel->getId()])]);
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($plantel);
                $em->flush();
                $this->addFlash('success', 'El plantel fue actualizado satisfactoriamente');
                return $this->json([
                    'url' => $this->generateUrl('plantel_index')
                ]);
            } else {
                $page = $this->renderView('plantel/_form.html.twig', [
                    'plantel' => $plantel,
                    'eliminable' => $eliminable,
                    'form' => $form->createView(),
                    'form_id' => 'plantel_edit',
                    'action' => 'Actualizar',
                ]);
                return $this->json(['form' => $page, 'error' => true]);
            }

        return $this->render('plantel/new.html.twig', [
            'plantel' => $plantel,
            'title' => 'Editar plantel',
            'action' => 'Actualizar',
            'form_id' => 'plantel_edit',
            'eliminable' => $eliminable,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="plantel_delete")
     */
    public function delete(Request $request, Plantel $plantel): Response
    {
        if (!$request->isXmlHttpRequest() ||
            !$this->isCsrfTokenValid('delete' . $plantel->getId(), $request->query->get('_token'))
            || !$this->esEliminable($plantel))
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($plantel);
        $em->flush();

        $this->addFlash('success', 'El plantel fue eliminado satisfactoriamente');
        return $this->json([
            'url' => $this->generateUrl('plantel_index')
        ]);
    }

    private function esEliminable(Plantel $plantel)
    {
        $em = $this->getDoctrine()->getManager();
        $proyecto=$em->getRepository(PlanTrabajo::class)->findOneByPlantel($plantel);
        $escuela=$em->getRepository(Escuela::class)->findOneByPlantel($plantel);
        $diagnostico=$em->getRepository(DiagnosticoPlantel::class)->findOneByPlantel($plantel);
        return $proyecto==null && $escuela==null && $diagnostico==null;
    }

}
