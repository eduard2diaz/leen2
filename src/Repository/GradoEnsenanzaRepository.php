<?php

namespace App\Repository;

use App\Entity\Escuela;
use App\Entity\GradoEnsenanza;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method GradoEnsenanza|null find($id, $lockMode = null, $lockVersion = null)
 * @method GradoEnsenanza|null findOneBy(array $criteria, array $orderBy = null)
 * @method GradoEnsenanza[]    findAll()
 * @method GradoEnsenanza[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradoEnsenanzaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GradoEnsenanza::class);
    }

    public function findByEscuelaJson($escuela)
    {
        $consulta = "Select array_to_json(array_agg(data)) from(
                     select g.id as id, g.nombre as nombre from grado_ensenanza as g join tipo_ensenanza te on g.tipoensenanza_id = te.id
                     join escuela_tipo_ensenanza ete on(ete.tipo_ensenanza_id=te.id) join escuela e on(ete.escuela_id=e.id)
                     where e.id=".$escuela.") as data
        ";
        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->query($consulta);
        $result = $statement->fetchAll();
        return $result[0]['array_to_json'];
    }

}
