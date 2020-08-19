<?php

namespace App\Form;

use App\Entity\Ciudad;
use App\Entity\Estado;
use App\Entity\Municipio;
use App\Form\Subscriber\AddCiudadEstadisticaFieldSubscriber;
use App\Form\Subscriber\AddMunicipioEstadisticaFieldSubscriber;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EstadisticaLocalidadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('estado',EntityType::class,['placeholder'=>'Seleccione un estado','class'=>Estado::class]);

        $factory = $builder->getFormFactory();
        $builder->addEventSubscriber(new AddMunicipioEstadisticaFieldSubscriber($factory));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
