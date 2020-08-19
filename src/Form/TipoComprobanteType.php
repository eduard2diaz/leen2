<?php

namespace App\Form;

use App\Entity\TipoComprobante;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TipoComprobanteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comprobante',TextType::class,['attr'=>['class'=>'form-control','autocomplete'=>'off']])
            ->add('descripcion',TextareaType::class,['label'=>'Descripción','attr'=>['class'=>'form-control']])
         //   ->add('fechacaptura')
      //      ->add('estatus')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TipoComprobante::class,
        ]);
    }
}
