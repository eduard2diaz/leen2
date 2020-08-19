<?php

namespace App\Form;

use App\Entity\Plantel;
use App\Form\Subscriber\AddCodigoPostalMunicipioFieldSubscriber;
use App\Form\Subscriber\AddMunicipioEstadoFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlantelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class,['label'=>'Nombre del plantel','attr'=>['class'=>'form-control','autocomplete'=>'off']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Plantel::class,
        ]);
    }
}
