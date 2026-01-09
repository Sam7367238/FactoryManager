<?php

namespace App\Service;

use App\Repository\FactoryRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class FactoryService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FactoryRepository $repository
    ) {}

    public function findAll() {
        return $this -> repository -> findAll();
    }
}
