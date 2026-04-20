<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\ProjectRepository;
use App\State\Processor\ImportProjectProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(security: 'is_granted("ROLE_USER") and is_granted("CREATE", object)'),
        new Post(
            uriTemplate: '/projects/import',
            processor: ImportProjectProcessor::class,
        ),
        new Put(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['project:get']],
    security: 'is_granted("ROLE_USER")',
)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['agent:get', 'project:get'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['agent:get', 'project:get'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups('project:get')]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Agent::class)]
    #[Groups('project:get')]
    private Agent $createdBy;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'projects')]
    private Team $team;

    #[ORM\ManyToMany(targetEntity: Agent::class, inversedBy: 'projects' , cascade: ['all'])]
    #[Groups('project:get')]
    private Collection $agents;

    public function __construct()
    {
        $this->agents = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAgents(): Collection
    {
        return $this->agents;
    }

    public function setAgents(Collection $agents): void
    {
        $this->agents = $agents;
    }

    public function addAgent(Agent $agent): void
    {
        if (!$this->agents->contains($agent)) {
            $this->agents->add($agent);
            $agent->addProject($this);
        }
    }
}
