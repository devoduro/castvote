<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class AdminFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name'            => $this->faker->name(),
            'email'           => $this->faker->unique()->safeEmail(),
            'password'        => Hash::make('password'),
            'role'            => 'manager',
        ];
    }

    public function owner(): static
    {
        return $this->state(['role' => 'owner']);
    }

    public function viewer(): static
    {
        return $this->state(['role' => 'viewer']);
    }
}
