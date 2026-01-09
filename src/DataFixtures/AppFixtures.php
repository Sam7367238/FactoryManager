<?php

namespace App\DataFixtures;

use App\Entity\Factory;
use App\Entity\Machine;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $factory = new Factory();
        $factory -> setName("CPU Factory");
        $factory -> setImage("/images/IntelLogo.png");

        $manager->persist($factory);

        $machine = new Machine();
        $machine -> setName("CPU Constructor");
        $machine -> addFactory($factory);
        $machine -> setStatus("ON");

        $manager->persist($machine);
        
        $machine = new Machine();
        $machine -> setName(name: "Benchmarker");
        $machine -> addFactory($factory);
        $machine -> setStatus("ON");

        $manager->persist($machine);

        $manager->flush();
    }
}
