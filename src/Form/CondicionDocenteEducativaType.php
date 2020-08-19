<?php

namespace App\Form;

use App\Entity\CondicionDocenteEducativa;
use App\Entity\Escuela;
use App\Entity\EscuelaCCTS;
use App\Entity\GradoEnsenanza;
use App\Form\Subscriber\AddGradoEscuelaFieldSubscriber;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CondicionDocenteEducativaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $plantel=$options['data']->getDiagnostico()->getPlantel()->getId();
        $builder
            ->add('curp',TextType::class,[
                'attr'=>['class'=>'form-control','maxlength'=>'18']])
            ->add('nombre',TextType::class,['attr'=>['class'=>'form-control']])
            ->add('escuela', EntityType::class, array(
            'placeholder'=>'Seleccione una escuela',
            'class' => Escuela::class,
            'required' => true,
            'query_builder' => function (EntityRepository $repository) use ($plantel) {
                $qb = $repository->createQueryBuilder('escuela')
                    ->innerJoin('escuela.plantel', 'p');
                    $qb->where('p.id = :id')
                        ->setParameter('id', $plantel);
                return $qb;
            }

        , 'attr' => array('class' => 'form-control input-medium')));

        $factory = $builder->getFormFactory();
        $builder->addEventSubscriber(new AddGradoEscuelaFieldSubscriber($factory));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CondicionDocenteEducativa::class,
        ]);
    }
}
