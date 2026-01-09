<?php

namespace App\Entity;

use App\Enum\MachineStatus;
use App\Repository\MachineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MachineRepository::class)]
class Machine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, factory>
     */
    #[ORM\ManyToMany(targetEntity: factory::class, inversedBy: 'machines')]
    private Collection $factory;

    #[ORM\Column(length: 25)]
    private ?string $status = null;

    public function __construct()
    {
        $this->factory = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, factory>
     */
    public function getFactory(): Collection
    {
        return $this->factory;
    }

    public function addFactory(factory $factory): static
    {
        if (!$this->factory->contains($factory)) {
            $this->factory->add($factory);
        }

        return $this;
    }

    public function removeFactory(factory $factory): static
    {
        $this->factory->removeElement($factory);

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
