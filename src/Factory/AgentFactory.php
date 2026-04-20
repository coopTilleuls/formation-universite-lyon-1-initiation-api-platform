<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Agent;
use App\Entity\AgentRole;
use Doctrine\Common\Collections\ArrayCollection;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Agent>
 */
final class AgentFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Agent::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'firstName' => self::faker()->firstName(255),
            'lastName' => self::faker()->lastName(255),
            'roles' => self::faker()->randomElements(
                AgentRole::cases(),
                self::faker()->numberBetween(1, 3)
            ),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            ->beforeInstantiate(function (array $attributes): array {
                $attributes['projects'] = new ArrayCollection($attributes['projects'] ?? []);

                return $attributes;
            })
            ->afterInstantiate(function(Agent $agent): void {
                $agent->setRoles(array_map(
                    fn (AgentRole $agentRole) => $agentRole->value,
                    $agent->getRoles()
                ));

            })
        ;
    }
}
