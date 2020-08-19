<?php

namespace App\Form\Subscriber;

use App\Entity\GradoEnsenanza;
use App\Entity\Escuela;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Description of AddCargoFieldSubscriber
 *
 * @author eduardo
 */
class AddGradoEscuelaFieldSubscriber implements EventSubscriberInterface
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
        $escuela = is_array($data) ? $data['escuela'] : $data->getEscuela();
        $this->addElements($event->getForm(), $escuela);
    }

    protected function addElements($form, $escuela)
    {
        $form->add($this->factory->createNamed('grado', EntityType::class, null, array(
            'auto_initialize' => false,
            'class' => GradoEnsenanza::class,
            'query_builder' => function (EntityRepository $repository) use ($escuela) {
                $res = $repository->createQueryBuilder('escuela');
                $res->select('e')->from(Escuela::class, 'e');
                $res->join('e.tipoensenanza', 'te');
                $res->where('e.id = :id')->setParameter('id', $escuela);
                $escuelaObj = $res->getQuery()->getOneOrNullResult();

                $tipo_ensenanza_list = [1];
                foreach ($escuelaObj->getTipoensenanza() as $te)
                    $tipo_ensenanza_list[] = $te->getId();

                $qb = $repository->createQueryBuilder('grado')
                    ->innerJoin('grado.tipoensenanza', 'te')
                    ->where('te.id IN (:list)')->setParameter('list', $tipo_ensenanza_list);

                return $qb;
            },
            'group_by' => function ($choiceValue, $key, $value) {
                return $choiceValue->getTipoensenanza()->getNombre();
            },
        )));
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        if (null == $data->getId())
            $form->add('grado', null, ['required' => true,'placeholder'=>'Seleccione un grado', 'choices' => []]);
         else {
            $escuela = is_array($data) ? $data['escuela'] : $data->getEscuela();
            $this->addElements($event->getForm(), $escuela);
        }

    }


}
