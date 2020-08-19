<?php

namespace App\Repository;

use App\Entity\PlanTrabajo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PlanTrabajo|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanTrabajo|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanTrabajo[]    findAll()
 * @method PlanTrabajo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanTrabajoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanTrabajo::class);
    }


    public function findSumaGastos($plan_id)
    {
        $cadena = "SELECT SUM(cg.monto) FROM App:ControlGastos cg JOIN cg.plantrabajo p WHERE p.id= :plan ";
        $consulta = $this->getEntityManager()->createQuery($cadena);
        $consulta->setParameter('plan', $plan_id);
        return $consulta->getResult()[0][1]==null ? 0 : $consulta->getResult()[0][1];
    }
    
    public function nextNumber($plantel_id)
    {
        $cadena = "SELECT MAX(pt.numero) FROM App:PlanTrabajo pt JOIN pt.plantel p WHERE p.id= :plantel ";
        $consulta = $this->getEntityManager()->createQuery($cadena);
        $consulta->setParameter('plantel', $plantel_id);
        return $consulta->getResult()[0][1]==null ? 1 : $consulta->getResult()[0][1]+1;
    }

}
