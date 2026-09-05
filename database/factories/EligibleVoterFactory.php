<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EligibleVoterFactory extends Factory
{
    /**
     * Mirrors the eligible_voters schema: event_id, identifier, eligible,
     * voted_at. There is no `name` or `has_voted` column — a voter counts as
     * having voted once voted_at is set.
     */
    public function definition(): array
    {
        return [
            'event_id'   => Event::factory(),
            'identifier' => 'UG-'.$this->faker->numerify('########'),
            'eligible'   => true,
            'voted_at'   => null,
        ];
    }

    public function alreadyVoted(): static
    {
        return $this->state(fn () => ['voted_at' => now()]);
    }

    public function ineligible(): static
    {
        return $this->state(fn () => ['eligible' => false]);
    }
}
