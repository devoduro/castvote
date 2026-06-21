<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EligibleVoterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id'   => Event::factory(),
            'identifier' => 'UG-' . $this->faker->numerify('########'),
            'name'       => $this->faker->name(),
            'has_voted'  => false,
            'voted_at'   => null,
        ];
    }
}
