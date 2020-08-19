<?php

namespace App\Repository;

use App\Entity\ControlGastos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ControlGastos|null find($id, $lockMode = null, $lockVersion = null)
 * @method ControlGastos|null findOneBy(array $criteria, array $orderBy = null)
 * @method ControlGastos[]    findAll()
 * @method ControlGastos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ControlGastosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ControlGastos::class);
    }



    
    public function nextNumber($plantrabajo_id)
    {
        $cadena = "SELECT MAX(cg.numerocomprobante) FROM App:ControlGastos cg JOIN cg.plantrabajo p WHERE p.id= :plantrabajo ";
        $consulta = $this->getEntityManager()->createQuery($cadena);
        $consulta->setParameter('plantrabajo', $plantrabajo_id);
        return $consulta->getResult()[0][1]==null ? 1 : $consulta->getResult()[0][1]+1;
    }

}
