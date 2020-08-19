<?php


namespace App\Controller;

use App\Entity\CondicionDocenteEducativa;
use App\Entity\CondicionEducativaAlumnos;
use App\Entity\Escuela;
use App\Entity\Plantel;
use App\Form\EscuelaType;
use App\Form\FiltroType;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/escuela")
 */
class EscuelaController extends AbstractController
{
    /**
     * @Route("/", name="escuela_index", methods={"GET","POST"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(FiltroType::class, [], ['action' => $this->generateUrl('escuela_index')]);
        $form->handleRequest($request);

        $dql = "SELECT e FROM App:Escuela e ";
        $data = $request->query->get('filtro');

        if ($form->isSubmitted() || $data != "") {
            if ($form->isSubmitted())
                $data = $form->getData()["filtro"];

            $dql = "SELECT e FROM App:Escuela e JOIN e.plantel p WHERE (e.nombre LIKE :value OR e.ccts LIKE :value OR p.nombre LIKE :value)";
            $query = $this->getDoctrine()->getManager()->createQuery($dql);
            $query->setParameter('value', "%" . $data . "%");
        } else {
            $query = $this->getDoctrine()->getManager()->createQuery($dql);
        }

        $escuelas = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            $this->getParameter('knp_num_items_per_page') /*limit per page*/
        );

        return $this->render('escuela/index.html.twig', [
            'escuelas' => $escuelas,
            'filtro' => $data,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/findbyplantel", name="escuela_findby_plantel", methods={"GET"})
     */
    public function findByPlantel(Plantel $plantel): Response
    {
        $escuelas = $this->getDoctrine()->getRepository(Escuela::class)->findByPlantel($plantel);

        return $this->render('escuela/findbyplantel.html.twig', [
            'escuelas' => $escuelas,
            'plantel' => $plantel,
        ]);
    }

    /**
     * @Route("/{id}/new", name="escuela_new", methods={"GET","POST"},options={"expose"=true})
     */
    public function new(Request $request,Plantel $plantel): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $escuela = new Escuela();
        $escuela->setPlantel($plantel);
        $form = $this->createForm(EscuelaType::class, $escuela, ['action' => $this->generateUrl('escuela_new',['id'=>$plantel->getId()])]);
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($escuela);
                $entityManager->flush();
                return $this->json(['mensaje' => 'La escuela fue registrada satisfactoriamente',
                    'id' => $escuela->getId(),
                    'nombre' => $escuela->getNombre(),
                    'ccts' => $escuela->getCcts()
                ]);
            } else {
                $page = $this->renderView('escuela/_form.html.twig', [
                    'escuela' => $escuela,
                    'form' => $form->createView(),
                ]);
                return $this->json(['form' => $page, 'error' => true,]);
            }

        return $this->render('escuela/new.html.twig', [
            'escuela' => $escuela,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="escuela_show", methods={"GET"},options={"expose"=true})
     */
    public function show(Escuela $escuela): Response
    {
        return $this->render('escuela/show.html.twig', [
            'escuela' => $escuela
        ]);
    }

    /**
     * @Route("/{id}/exportar", name="escuela_exportar", methods={"GET"})
     */
    public function exportar(Escuela $escuela, Pdf $snappy): Response
    {
        $proyectos = $this->getDoctrine()->getRepository(Proyecto::class)->findByEscuela($escuela);
        $html = $this->renderView('escuela/pdf.html.twig', [
            'escuela' => $escuela,
            'proyectos' => $proyectos
        ]);

        return new Response(
            $snappy->getOutputFromHtml($html),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', "detalles_escuela.pdf"),
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="escuela_edit", methods={"GET","POST"},options={"expose"=true})
     */
    public function edit(Request $request, Escuela $escuela): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $eliminable=$this->esEliminable($escuela);
        $form = $this->createForm(EscuelaType::class, $escuela, ['action' => $this->generateUrl('escuela_edit', ['id' => $escuela->getId()])]);
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($escuela);
                $em->flush();
                return $this->json(['mensaje' => 'La escuela fue actualizada satisfactoriamente',
                    'nombre' => $escuela->getNombre(),
                    'ccts' => $escuela->getCcts()
                ]);
            } else {
                $page = $this->renderView('escuela/_form.html.twig', [
                    'escuela' => $escuela,
                    'eliminable' => $eliminable,
                    'form' => $form->createView(),
                    'form_id' => 'escuela_edit',
                    'action' => 'Actualizar',
                ]);
                return $this->json(['form' => $page, 'error' => true]);
            }

        return $this->render('escuela/new.html.twig', [
            'escuela' => $escuela,
            'title' => 'Editar escuela',
            'action' => 'Actualizar',
            'form_id' => 'escuela_edit',
            'eliminable' => $eliminable,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="escuela_delete")
     */
    public function delete(Request $request, Escuela $escuela): Response
    {
        if (!$request->isXmlHttpRequest() ||
            !$this->isCsrfTokenValid('delete' . $escuela->getId(), $request->query->get('_token'))
            || !$this->esEliminable($escuela))
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($escuela);
        $em->flush();
        return $this->json(['mensaje' => 'La escuela fue eliminada satisfactoriamente']);
    }

    private function esEliminable(Escuela $escuela)
    {
        $em = $this->getDoctrine()->getManager();
        $cde=$em->getRepository(CondicionDocenteEducativa::class)->findOneByEscuela($escuela);
        $cea=$em->getRepository(CondicionEducativaAlumnos::class)->findOneByEscuela($escuela);
        return $cde==null && $cea==null;
    }

}
