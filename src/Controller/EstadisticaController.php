<?php

namespace App\Controller;

use App\Form\EstadisticaLocalidadType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/estadistica")
 */
class EstadisticaController extends AbstractController
{
    /**
     * @Route("/plantel/proyecto", name="estadistica_plantel_proyecto")
     */
    public function plantelesProyecto(Request $request, PaginatorInterface $paginator)
    {
        //PAGINANDO CONSULTAS SQL
        $query="Select * FROM planteles_con_proyecto_view";
        $query2="SELECT * FROM total_planteles_view";
        $total=$this->prepareDatabaseQuery($query2)[0]["count"];
        $planteles=$this->preparePaginateDatabaseQuery($query,$request,$paginator);
        $con_proyectos=$planteles->getTotalItemCount();

        return $this->render('estadistica/plantelproyectos.html.twig', [
            'plantels' => $planteles,
            'proyectos_count'=>$con_proyectos,
            'withoutproyectos_count'=>$total-$con_proyectos
        ]);
    }

    /**
     * @Route("/escuela/proyectos", name="estadistica_escuela_proyectos")
     */
    public function escuelasProyectos(Request $request, PaginatorInterface $paginator)
    {
        //PAGINANDO CONSULTAS SQL
        $query="Select * FROM escuelas_con_proyecto_view";
        $query2="SELECT * FROM total_escuelas_view";
        $total=$this->prepareDatabaseQuery($query2)[0]["count"];
        $escuelas=$this->preparePaginateDatabaseQuery($query,$request,$paginator);
        $con_proyectos=$escuelas->getTotalItemCount();

        return $this->render('estadistica/escuelaproyectos.html.twig', [
            'escuelas' => $escuelas,
            'proyectos_count'=>$con_proyectos,
            'withoutproyectos_count'=>$total-$con_proyectos
        ]);
    }


    /**
     * @Route("/plantel/sanitario", name="estadistica_plantel_sin_sanitarios", methods={"GET"})
     */
    public function plantelSinSanitario(Request $request, PaginatorInterface $paginator): Response
    {
        $query = "Select * from planteles_sin_sanitarios_view";

        return $this->render('estadistica/plantelsinsanitario.html.twig', [
            'plantels' => $this->preparePaginateDatabaseQuery($query,$request,$paginator),
        ]);
    }

    /**
     * @Route("/plantel/biblioteca", name="estadistica_plantel_sin_biblioteca", methods={"GET"})
     */
    public function plantelSinBiblioteca(Request $request, PaginatorInterface $paginator): Response
    {
        $query = "Select * from planteles_sin_bibliotecas_view";

        return $this->render('estadistica/plantelsinbiblioteca.html.twig', [
            'plantels' => $this->preparePaginateDatabaseQuery($query,$request,$paginator),
        ]);
    }

    /**
     * @Route("/plantel/aguapotable", name="estadistica_plantel_sin_aguapotable", methods={"GET"})
     */
    public function plantelSinAguapotable(Request $request, PaginatorInterface $paginator): Response
    {
        $query = "Select * from planteles_sin_aguapotable_view";

        return $this->render('estadistica/plantelsinaguapotable.html.twig', [
            'plantels' => $this->preparePaginateDatabaseQuery($query,$request,$paginator),
        ]);
    }

    /**
     * @Route("/plantel/drenaje", name="estadistica_plantel_sin_drenaje", methods={"GET"})
     */
    public function plantelSinDrenaje(Request $request, PaginatorInterface $paginator): Response
    {
        $query = "Select * from planteles_sin_drenaje_view";

        return $this->render('estadistica/plantelsindrenaje.html.twig', [
            'plantels' => $this->preparePaginateDatabaseQuery($query,$request,$paginator),
        ]);
    }

    /**
     * @Route("/plantel/electricidad", name="estadistica_plantel_sin_electricidad", methods={"GET"})
     */
    public function plantelSinElectricidad(Request $request, PaginatorInterface $paginator): Response
    {
        $query = "Select * from planteles_sin_electricidad_view";

        return $this->render('estadistica/plantelsinelectricidad.html.twig', [
            'plantels' => $this->preparePaginateDatabaseQuery($query,$request,$paginator),
        ]);
    }

    /**
     * @Route("/plantel/internet", name="estadistica_plantel_sin_internet", methods={"GET"})
     */
    public function plantelInternet(Request $request, PaginatorInterface $paginator): Response
    {
        $query = "Select * from planteles_sin_internet_view";

        return $this->render('estadistica/plantelsininternet.html.twig', [
            'plantels' => $this->preparePaginateDatabaseQuery($query,$request,$paginator),
        ]);
    }

    private function preparePaginateDatabaseQuery($query,$request,$paginator){
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();
        $stmt = $db->prepare($query);
        $params = array();
        $stmt->execute($params);
        $aux=$stmt->fetchAll();
        //$request=$this->get('request_stack')->getCurrentRequest();
        $result = $paginator->paginate(
            $aux,
            $request->query->getInt('page', 1),
            $this->getParameter('knp_num_items_per_page')
        );
        return $result;
    }

    private function prepareDatabaseQuery($query){
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();
        $stmt = $db->prepare($query);
        $params = array();
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

}
