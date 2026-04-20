<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\AgentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AgentRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['agent:get']],
)]
#[ApiFilter(SearchFilter::class, properties: [
    'firstName' => SearchFilterInterface::STRATEGY_PARTIAL,
    'lastName' => SearchFilterInterface::STRATEGY_EXACT,
])]
class Agent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('agent:get')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['agent:get', 'project:get'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['agent:get', 'project:get'])]
    private ?string $lastName = null;

    #[ORM\Column(type: 'simple_array')]
    #[Groups('agent:get')]
    private array $roles = [];

    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'agents', cascade: ['all'])]
    #[Groups('agent:get')]
    private Collection $projects;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'agents')]
    #[Groups('agent:get')]
    private Team $team;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function setProjects(Collection $projects): void
    {
        $this->projects = $projects;

        /** @var Project $project */
        foreach ($projects as $project) {
            $project->addAgent($this);
        }
    }

    public function addProject(Project $project): void
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->addAgent($this);
        }
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function setTeam(Team $team): void
    {
        $this->team = $team;
    }
}
