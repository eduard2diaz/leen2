<?php

namespace App\EventSubscriber;

use App\Entity\DiagnosticoPlantel;
use App\Entity\ControlGastos;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Tool\FileStorageManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ControlGastosSubscriber  implements EventSubscriber
{
    private $serviceContainer;

    function __construct(ContainerInterface $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * @return mixed
     */
    public function getServiceContainer()
    {
        return $this->serviceContainer;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof ControlGastos) {
            $ruta=$this->getServiceContainer()->getParameter('storage_directory');
            $file=$entity->getFile();
            $nombreArchivo=FileStorageManager::Upload($ruta,$file);
            if (null!=$nombreArchivo)
                $entity->setControlarchivos($nombreArchivo);
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof ControlGastos) {
            $directory = $this->getServiceContainer()->getParameter('storage_directory');
            $ruta=$directory . DIRECTORY_SEPARATOR . $entity->getControlarchivos();
            FileStorageManager::removeUpload($ruta);
        }
    }


    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'preRemove'
        ];
    }
}
