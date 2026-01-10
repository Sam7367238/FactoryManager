<?php

namespace App\Service;

use App\Entity\Factory;
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

    public function saveFactory(Factory $factory, bool $persist = true): void {
        if ($persist) {
            $this -> entityManager -> persist($factory);
        }

        $this -> entityManager -> flush();
    }

    public function setFactoryImage(Factory $factory, string $imageFileName): void {
        $factory -> setImage($imageFileName);
    }

    public function deleteFactory(Factory $factory): void {
        $this -> entityManager -> remove($factory);
        $this -> entityManager -> flush();
    }
}
