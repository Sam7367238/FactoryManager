<?php

namespace App\Service;

use App\Entity\Factory;
use App\Entity\Machine;
use App\Repository\FactoryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

final readonly class FactoryService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FactoryRepository $repository
    ) {}

    public function findAll(): array {
        return $this -> repository -> findAll();
    }

    public function getMachines(Factory $factory): Collection {
        return $factory -> getMachines();
    }
}
