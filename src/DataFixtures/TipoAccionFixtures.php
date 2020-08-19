<?php

namespace App\DataFixtures;

use App\Entity\TipoAccion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TipoAccionFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $tipos=[
            ['nombre'=>'A. Equipamiento, materiales y servicios','descripcion'=>'Adquisición de mobiliario y equipo escolar (libreros, equipo de laboratorio, computadoras, tabletas,
                                                                 televisores, impresoras, fotocopiadoras, proyectores, equipo de audio y video, equipo de seguridad y primeros auxilios (extintores, botiquín,
                                                                 señalamientos de áreas de seguridad, alarmas, protecciones para puertas y ventanas), material didáctico y educativo (papelería,
                                                                 musicales, libros, adquisición de insumos y materiales impresos y en línea, software educativo)'],
            ['nombre'=>'B. Acciones menores - Mantenimiento y Rehabilitación','descripcion'=>'Mantenimientos generales (reparación de fugas en instalaciones hidrosanitarias, repellos,
                                                                              fisuras, vidrios rotos, cancelería, pintura, Impermeabilización, red eléctrica, luminarias, Sistemas de agua potable, entre otros).  '],
            ['nombre'=>'C. Acciones mayores','descripcion'=>'Construcción y reconstrucción de aulas, talleres, laboratorios, sanitarios, instalaciones deportivas, de protección (barda
                                              o cerco perimetral), cisternas, techumbres, rampas, centros integrales de aprendizaje comunitario (para comunidades del CONAFE) entre otras. ']
            ];

        foreach ($tipos as $tipo){
            $value=new TipoAccion();
            $value->setAccion($tipo['nombre']);
            $value->setDescripcion($tipo['descripcion']);
            $manager->persist($value);
        }

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 4;
    }
}
