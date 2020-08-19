<?php

namespace App\Form;

use App\Entity\ControlGastos;
use App\Entity\Escuela;
use App\Entity\Proyecto;
use App\Form\Transformer\DatetoStringTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ControlGastosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $required=!$options['data']->getId() ? true : false;
        $builder
            ->add('fechacaptura',TextType::class,['label'=>'Fecha de captura','attr'=>['class'=>'form-control', 'pattern'=>'\d{4}-\d{2}-\d{2}','autocomplete' => 'off']])
            ->add('concepto',TextareaType::class,['label'=>'Descripción','attr'=>['class'=>'form-control']])
            ->add('monto',NumberType::class,['attr'=>['class'=>'form-control','autocomplete'=>'off']])
            ->add('tipoComprobante',null,['label'=>'Tipo de comprobante'])
            ->add('file', FileType::class, array('label'=>' ','required' => $required,'attr'=>['style'=>'display: none']))
        ;
        $builder->get('fechacaptura')->addModelTransformer(new DatetoStringTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ControlGastos::class,
        ]);
    }
}
