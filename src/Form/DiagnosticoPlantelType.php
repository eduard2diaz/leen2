<?php

namespace App\Form;

use App\Entity\DiagnosticoPlantel;
use App\Entity\Escuela;
use App\Entity\Proyecto;
use App\Form\ClasificacionDiagnosticoPlantelType;
use App\Form\ObservacionesType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Form\Transformer\DatetoStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\CondicionDocenteEducativaType;

class DiagnosticoPlantelType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $required=!$options['data']->getId() ? true : false;
        $builder
            ->add('numeroaulas',IntegerType::class,['label'=>'Número de aulas','attr'=>['class'=>'form-control']])
            ->add('idcondicionesAula',null,['label'=>'Condiciones de las aulas','attr'=>['class'=>'form-control']])
            ->add('numerosanitarios',IntegerType::class,['label'=>'Número de sanitarios','attr'=>['class'=>'form-control']])
            ->add('idcondicionessanitarios',null,['label'=>'Condiciones de los sanitarios','attr'=>['class'=>'form-control']])
            ->add('numerooficinas',IntegerType::class,['label'=>'Número de oficinas','attr'=>['class'=>'form-control']])
            ->add('idcondicionoficina',null,['label'=>'Condiciones de las oficinas','attr'=>['class'=>'form-control']])
            ->add('numerobibliotecas',IntegerType::class,['label'=>'Número de bibliotecas','attr'=>['class'=>'form-control']])
            ->add('idcondicionesbliblioteca',null,['label'=>'Condiciones de las bibliotecas','attr'=>['class'=>'form-control']])
            ->add('numeroaulasmedios',IntegerType::class,['label'=>'Número de aulas de medios','attr'=>['class'=>'form-control']])
            ->add('idcondicionaulamedios',null,['label'=>'Condiciones de las aulas de medios','attr'=>['class'=>'form-control']])
            ->add('numeropatio',IntegerType::class,['label'=>'Número de patios','attr'=>['class'=>'form-control']])
            ->add('idcondicionpatio',null,['label'=>'Cóndiciones de los patios','attr'=>['class'=>'form-control']])
            ->add('numerocanchasdeportivas',IntegerType::class,['label'=>'Número de canchas deportivas','attr'=>['class'=>'form-control']])
            ->add('idcondicioncanchasdeportivas',null,['label'=>'Condiciones de las canchas deportivas','attr'=>['class'=>'form-control']])
            ->add('numerobarda',IntegerType::class,['label'=>'Número de bardas','attr'=>['class'=>'form-control']])
            ->add('idcondicionbarda',null,['label'=>'Condiciones de las bardas','attr'=>['class'=>'form-control']])
            ->add('aguapotable',CheckboxType::class,['required'=>false,'label'=>'Agua potable'])
            ->add('idcondicionagua',null,['label'=>'Condiciones del agua','attr'=>['class'=>'form-control']])
            ->add('drenaje',CheckboxType::class,['required'=>false])
            ->add('idcondiciondrenaje',null,['label'=>'Condiciones del drenaje','attr'=>['class'=>'form-control']])
            ->add('energiaelectrica',CheckboxType::class,['required'=>false,'label'=>'Energía eléctrica'])
            ->add('idcondicionenergia',null,['label'=>'Condiciones de la energia eléctrica','attr'=>['class'=>'form-control']])
            ->add('telefono',CheckboxType::class,['required'=>false,'label'=>'Teléfono'])
            ->add('idcondiciontelefono',null,['label'=>'Condiciones de la telefonía','attr'=>['class'=>'form-control']])
            ->add('internet',CheckboxType::class,['required'=>false])
            ->add('idcondicioninternet',null,['label'=>'Condiciones del internet','attr'=>['class'=>'form-control']])
            ->add('fecha',TextType::class,['attr'=>['class'=>'form-control', 'pattern'=>'\d{4}-\d{2}-\d{2}','autocomplete' => 'off']])

            ->add('descrip_num_aulas',ObservacionesType::class)
            ->add('descrip_num_sanitarios',ObservacionesType::class)
            ->add('descrip_num_oficinas',ObservacionesType::class)
            ->add('descrip_num_bibliotecas',ObservacionesType::class)
            ->add('descrip_num_aulamedios',ObservacionesType::class)
            ->add('descrip_num_patios',ObservacionesType::class)
            ->add('descrip_num_canchas_deportivas',ObservacionesType::class)
            ->add('descrip_num_bardas',ObservacionesType::class)
            ->add('descrip_agua_potables',ObservacionesType::class)
            ->add('descrip_drenaje',ObservacionesType::class)
            ->add('descrip_energiaelectrica',ObservacionesType::class)
            ->add('descrip_telefonia',ObservacionesType::class)
            ->add('descrip_internet',ObservacionesType::class)
            ->add('file', FileType::class, array('label'=>' ','required' => $required,'attr'=>['style'=>'display: none']))
        ;
        $builder->get('fecha')->addModelTransformer(new DatetoStringTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DiagnosticoPlantel::class,
        ]);
    }
}
