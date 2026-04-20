<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\AgentFactory;
use App\Factory\ProjectFactory;
use App\Factory\TeamFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        ProjectFactory::createMany(50);
        ProjectFactory::createOne([
            'name' => 'Mon projet CNRS',
        ]);
        AgentFactory::new()->many(50)->create([
            'projects' => ProjectFactory::randomSet(2),
        ]);
        TeamFactory::new()->many(100)->create(function () {
            return ['manager' => AgentFactory::new()->create()];
        });

        $manager->flush();
    }
}
