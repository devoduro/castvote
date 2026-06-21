<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    private static int $codeCounter = 1;

    public function definition(): array
    {
        return [
            'event_id'      => Event::factory(),
            'name'          => $this->faker->randomElement([
                'Artiste of the Year',
                'Song of the Year',
                'Album of the Year',
                'New Artiste of the Year',
                'Afrobeats/Afropop Song of the Year',
                'Highlife Song of the Year',
                'Gospel Song of the Year',
                'Hip Hop Song of the Year',
                'Reggae/Dancehall Artiste of the Year',
                'R&B/Soul Song of the Year',
                'Best Collaboration',
                'Most Popular Song',
                'Songwriter of the Year',
                'Producer of the Year',
                'Music Video of the Year',
            ]),
            'code'          => str_pad(self::$codeCounter++, 2, '0', STR_PAD_LEFT),
            'display_order' => self::$codeCounter,
        ];
    }
}
