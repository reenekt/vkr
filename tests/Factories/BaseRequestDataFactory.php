<?php

namespace Tests\Factories;

use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;

abstract class BaseRequestDataFactory
{
    public Generator $faker;
    protected Collection $states;

    /**
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->faker = Container::getInstance()->make(Generator::class);
        $this->states = new Collection();
    }

    /** @return array<string,mixed> */
    abstract public function definition(): array;

    public static function new($attributes = []): static
    {
        return (new static)->state($attributes);
    }

    /**
     * @param (callable(array<string, mixed>): array<string, mixed>)|array $state
     * @return $this
     */
    public function state(callable|array $state): static
    {
        if (is_array($state)) {
            $state = fn() => $state;
        }

        $this->states->push($state);

        return $this;
    }

    /** @return array<string,mixed> */
    public function make(array $attributes = []): array
    {
        if (!empty($attributes)) {
            return $this->state($attributes)->make();
        }

        $definition = $this->definition();

        return $this->states->reduce(function ($carry, $state) {
            return array_merge($carry, $state($carry));
        }, $definition);
    }
}
