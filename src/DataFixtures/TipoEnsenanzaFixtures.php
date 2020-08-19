<?php

namespace App\DataFixtures;

use App\Entity\TipoEnsenanza;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TipoEnsenanzaFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $tipos=['kinder','Primaria','Secundaria'];
        foreach ($tipos as $value){
            $ensenanza = new TipoEnsenanza();
            $ensenanza->setNombre($value);
            $manager->persist($ensenanza);
        }

        $manager->flush();
    }

    public function getOrder()
    {
       return 2;
    }
}
