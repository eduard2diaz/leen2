<?php

namespace App\Repository;

use App\Entity\DiagnosticoPlantel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DiagnosticoPlantel|null find($id, $lockMode = null, $lockVersion = null)
 * @method DiagnosticoPlantel|null findOneBy(array $criteria, array $orderBy = null)
 * @method DiagnosticoPlantel[]    findAll()
 * @method DiagnosticoPlantel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiagnosticoPlantelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DiagnosticoPlantel::class);
    }

    public function findOneByTipoCondicion($tipo_condicion): ?DiagnosticoPlantel
    {
        $query= 'Select dp FROM App:DiagnosticoPlantel dp 
        WHERE 
        dp.idcondicionesAula=:id OR
        dp.idcondicionessanitarios=:id OR
        dp.idcondicionoficina=:id OR
        dp.idcondicionesbliblioteca=:id OR
        dp.idcondicionaulamedios=:id OR
        dp.idcondicionpatio=:id OR
        dp.idcondicioncanchasdeportivas=:id OR
        dp.idcondicionbarda=:id OR
        dp.idcondicionagua=:id OR
        dp.idcondiciondrenaje=:id OR
        dp.idcondicionenergia=:id OR
        dp.idcondiciontelefono=:id OR
        dp.idcondicioninternet=:id         
        ';

        $consulta = $this->getEntityManager()->createQuery($query)->setParameter('id', $tipo_condicion);
        return $consulta->getOneOrNullResult();
    }

    public function nextNumber($plantel_id)
    {
        $cadena = "SELECT MAX(dp.iddiagnosticoplantel) FROM App:DiagnosticoPlantel dp JOIN dp.plantel p WHERE p.id= :plantel ";
        $consulta = $this->getEntityManager()->createQuery($cadena);
        $consulta->setParameter('plantel', $plantel_id);
        return $consulta->getResult()[0][1]==null ? 1 : $consulta->getResult()[0][1]+1;
    }

}
