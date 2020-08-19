<?php

namespace App\Form;

use App\Entity\Escuela;
use App\Entity\PlanTrabajo;
use App\Entity\Proyecto;
use App\Form\Transformer\DatetoStringTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanTrabajoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $required=!$options['data']->getId() ? true : false;
        $builder
            ->add('descripcionaccion',TextareaType::class,['label'=>'Descripción','attr'=>['class'=>'form-control']])
            ->add('tiempoestimado',TextType::class,['label'=>'Tiempo estimado','attr'=>['class'=>'form-control','autocomplete'=>'off']])
            ->add('costoestimado',NumberType::class,['label'=>'Costo estimado','attr'=>['class'=>'form-control','autocomplete'=>'off']])
            ->add('fechainicio',TextType::class,['label'=>'Fecha de Inicio','attr'=>['class'=>'form-control', 'pattern'=>'\d{4}-\d{2}-\d{2}',
                'autocomplete' => 'off']])
            ->add('fechafin',TextType::class,['required'=>false,'label'=>'Fecha de Fin','attr'=>['class'=>'form-control', 'pattern'=>'\d{4}-\d{2}-\d{2}',
                'autocomplete' => 'off',]])
            ->add('montoasignado',NumberType::class,['label'=>'Monto Asignado','attr'=>['class'=>'form-control','autocomplete'=>'off']])
            ->add('tipoAccion',null,['label'=>'Tipo de acción'])
            ->add('file', FileType::class, array('label'=>' ','required' => $required,'attr'=>['style'=>'display: none']))
        ;

        $builder->get('fechainicio')->addModelTransformer(new DatetoStringTransformer());
        $builder->get('fechafin')->addModelTransformer(new DatetoStringTransformer());

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlanTrabajo::class,
        ]);
    }
}
