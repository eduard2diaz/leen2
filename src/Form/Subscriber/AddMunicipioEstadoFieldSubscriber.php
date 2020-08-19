<?php

namespace App\Form\Subscriber;

use App\Entity\Estado;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Entity\Municipio;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Description of AddCargoFieldSubscriber
 *
 * @author eduardo
 */
class AddMunicipioEstadoFieldSubscriber implements EventSubscriberInterface
{

    private $factory;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data)
            return;
        $estado = is_array($data) ? $data['estado'] : $data->getEstado();
        $this->addElements($event->getForm(), $estado);
    }

    protected function addElements($form, $estado)
    {
        $form->add($this->factory->createNamed('municipio', EntityType::class, null, array(
            'auto_initialize' => false,
            'class' => 'App:Municipio',
            'choice_label' => function ($elemento) {
                return $elemento->getNombre();
            },
            'query_builder' => function (EntityRepository $repository) use ($estado) {
                $qb = $repository->createQueryBuilder('municipio')
                    ->innerJoin('municipio.estado', 'p');
                if ($estado instanceof Estado) {
                    $qb->where('p.id = :id')
                        ->setParameter('id', $estado);
                } elseif (is_numeric($estado)) {
                    $qb->where('p.id = :id')
                        ->setParameter('id', $estado);
                } else {
                    $qb->where('p.id = :id')
                        ->setParameter('id', null);
                }
                return $qb;
            }
        )));
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        if (null == $data->getId())
            $form->add('municipio', null, ['required' => true,'placeholder'=>'Seleccione un municipio', 'choices' => []]);
         else {
            $estado = is_array($data) ? $data['estado'] : $data->getEstado();
            $this->addElements($event->getForm(), $estado);
        }

    }


}
