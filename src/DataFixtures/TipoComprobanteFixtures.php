<?php

namespace App\DataFixtures;

use App\Entity\TipoComprobante;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TipoComprobanteFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $tipos=[
            ['nombre'=>'A. Factura','descripcion'=>'Adquisición de bienes y servicios en establecimientos que emiten factura con requisitos fiscales.'],
            ['nombre'=>'B. Nota simple','descripcion'=>'Adquisición de materiales de construcción, bienes y servicios, en locales que no emiten factura con requisitos fiscales.'],
            ['nombre'=>'C. Lista de raya','descripcion'=>'Pago de jornales y mano de obra']
            ];

        foreach ($tipos as $tipo){
            $value=new TipoComprobante();
            $value->setComprobante($tipo['nombre']);
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
        return 5;
    }
}
