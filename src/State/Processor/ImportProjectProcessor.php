<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class ImportProjectProcessor implements ProcessorInterface
{
    public function __construct(
        private ProjectRepository $repository,
        private SerializerInterface $serializer,
        #[Autowire(param: 'kernel.project_dir')]
        private string $projectDirectory,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Project
    {
        $fs = new Filesystem();
        $data = $fs->readFile($this->projectDirectory.'/data/project.json');

        $project = $this->serializer->deserialize($data, Project::class, 'json');
        $this->repository->add($project);

        return $project;
    }
}
