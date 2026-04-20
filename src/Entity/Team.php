<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;

#[ApiResource]
#[Entity]
#[Table]
class Team
{
    #[Id]
    #[GeneratedValue(strategy: 'AUTO')]
    #[Column]
    private ?int $id = null;

    #[Column]
    private ?string $name = null;

    #[OneToMany(targetEntity: Agent::class, mappedBy: 'team')]
    private Collection $agents;

    #[OneToOne(targetEntity: Agent::class)]
    private Agent $manager;

    #[ManyToOne(targetEntity: Laboratory::class, inversedBy: 'teams')]
    private Laboratory $laboratory;

    #[OneToMany(targetEntity: Project::class, mappedBy: 'team')]
    private Collection $projects;

    public function __construct()
    {
        $this->agents = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getAgents(): Collection
    {
        return $this->agents;
    }

    public function setAgents(Collection $agents): void
    {
        $this->agents = $agents;
    }

    public function getManager(): Agent
    {
        return $this->manager;
    }

    public function setManager(Agent $manager): void
    {
        $this->manager = $manager;
    }

    public function getLaboratory(): Laboratory
    {
        return $this->laboratory;
    }

    public function setLaboratory(Laboratory $laboratory): void
    {
        $this->laboratory = $laboratory;
    }

    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function setProjects(Collection $projects): void
    {
        $this->projects = $projects;
    }
}
