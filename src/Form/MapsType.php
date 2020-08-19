<?php

namespace App\Form;

use App\Entity\Estado;
use App\Form\Subscriber\AddEscuelaEstadoFieldSubscriber;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $estados=[];
        foreach ($options['estados'] as $value)
            $estados[$value['nombre']]=$value['nombre'];
        $builder
            ->add('estado',ChoiceType::class,['choices'=>$estados])
            ->add('municipio',ChoiceType::class,['required'=>false,'choices'=>[]])
            ->add('escuela', TextType::class, ['required'=>false,'attr' => ['class' => 'form-control','autocomplete'=>'off']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
        $resolver->setRequired(['estados']);
    }
}
