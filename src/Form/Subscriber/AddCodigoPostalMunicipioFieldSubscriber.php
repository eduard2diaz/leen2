<?php

namespace App\Form\Subscriber;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityRepository;
use App\Entity\Municipio;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Description of AddCargoFieldSubscriber
 *
 * @author eduardo
 */
class AddCodigoPostalMunicipioFieldSubscriber implements EventSubscriberInterface
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
        $municipio = is_array($data) ? $data['municipio'] : $data->getMunicipio();
        $this->addElements($event->getForm(), $municipio);
    }

    protected function addElements($form, $municipio)
    {
        $form->add($this->factory->createNamed('d_codigo', EntityType::class, null, array(
            'label'=>'Código postal',
            'auto_initialize' => false,
            'class' => 'App:CodigoPostal',
            'choice_label' => function ($elemento) {
                return $elemento->getDCp();
            },
            'query_builder' => function (EntityRepository $repository) use ($municipio) {
                $qb = $repository->createQueryBuilder('cp')
                    ->innerJoin('cp.municipio', 'p');
                if ($municipio instanceof Municipio) {
                    $qb->where('p.id = :id')
                        ->setParameter('id', $municipio);
                } elseif (is_numeric($municipio)) {
                    $qb->where('p.id = :id')
                        ->setParameter('id', $municipio);
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
            $form->add('d_codigo', null, ['label'=>'Código postal' ,'required' => true,'placeholder'=>'Seleccione un código postal', 'choices' => []]);
         else {
            $municipio = is_array($data) ? $data['municipio'] : $data->getMunicipio();
            $this->addElements($event->getForm(), $municipio);
        }

    }


}
