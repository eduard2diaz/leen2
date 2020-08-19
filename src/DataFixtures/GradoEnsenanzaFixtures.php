<?php

namespace App\DataFixtures;

use App\Entity\GradoEnsenanza;
use App\Entity\TipoEnsenanza;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class GradoEnsenanzaFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $tipos=[
            ['nombre'=>'Preescolar','ensenanza'=>'kinder'],
            ['nombre'=>'1','ensenanza'=>'Primaria'],
            ['nombre'=>'2','ensenanza'=>'Primaria'],
            ['nombre'=>'3','ensenanza'=>'Primaria'],
            ['nombre'=>'4','ensenanza'=>'Primaria'],
            ['nombre'=>'5','ensenanza'=>'Primaria'],
            ['nombre'=>'6','ensenanza'=>'Primaria'],
            ['nombre'=>'7','ensenanza'=>'Secundaria'],
            ['nombre'=>'8','ensenanza'=>'Secundaria'],
            ['nombre'=>'9','ensenanza'=>'Secundaria'],
        ];
        foreach ($tipos as $value){
            $grado = new GradoEnsenanza();
            $grado->setNombre($value['nombre']);
            $ensenanza=$manager->getRepository(TipoEnsenanza::class)->findOneByNombre($value['ensenanza']);
            if(null!=$ensenanza){
                $grado->setTipoensenanza($ensenanza);
                $manager->persist($grado);
            }
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}
