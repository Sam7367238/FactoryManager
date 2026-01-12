<?php

namespace App\Service;

use App\Entity\Factory;
use App\Entity\Machine;
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

    public function persist(Machine $machine): void {
        $this -> entityManager -> persist($machine);
        $this -> entityManager -> flush();
    }

    public function setStatus(Machine $machine, string $status): void {
        $machine -> setStatus($status);
    }
}
