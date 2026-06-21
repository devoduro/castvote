<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Event;
use App\Models\Nominee;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id'    => Event::factory(),
            'category_id' => Category::factory(),
            'nominee_id'  => Nominee::factory(),
            'quantity'    => $this->faker->numberBetween(1, 5),
            'channel'     => $this->faker->randomElement(['ussd', 'web']),
            'voter_phone' => '0244' . $this->faker->numerify('######'),
            'payment_id'  => null,
        ];
    }
}
