<?php

namespace App\EventSubscriber;

use App\Entity\Estatus;
use App\Tool\FileStorageManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class EntidadesSubscriber  implements EventSubscriber
{

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em=$args->getEntityManager();
        $estatus=$em->getRepository(Estatus::class)->findOneByCode(Estatus::ACTIVE_CODE);

        if($estatus!=null && property_exists($entity,'estatus')){
            $entity->setEstatus($estatus);
            $em->flush();
        }

    }


    public function getSubscribedEvents()
    {
        return [
            'prePersist',
        ];
    }
}
