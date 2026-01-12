<?php

namespace App\Service;

use App\Entity\Factory;
use App\Repository\MachineRepository;
use Doctrine\ORM\EntityManagerInterface;

class MachineService {
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MachineRepository $repository
    ) {}

    public function findAll(): array {
        return $this -> repository -> findAll();
    }

    public function findOneBy(array $criteria): object|null {
        return $this -> repository -> findOneBy($criteria);
    }

    public function getOrphanedMachines(Factory $factory): mixed {
        return $this -> repository -> findOrphanedMachinesForFactory($factory);
    }
}
