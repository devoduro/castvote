<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Ghana Music Awards 2025',
            'Vodafone Ghana Music Awards',
            'VGMA Nominees Concert Votes',
            '3Music Awards 2025',
        ]);

        $starts = now()->subDays(2);
        $ends   = now()->addDays(30);

        return [
            'organization_id' => Organization::factory(),
            'name'            => $name,
            'slug'            => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 999),
            'event_type'      => 'award',
            'voting_rules'    => [
                'pay_per_vote'              => true,
                'price_per_vote_pesewas'    => 100,
                'max_votes_per_voter'       => null,
                'requires_eligibility_list' => false,
                'anonymous_tally'           => false,
            ],
            'ussd_shortcode'  => '*928*' . $this->faker->numberBetween(10, 99) . '#',
            'ussd_short_id'   => (string) $this->faker->numberBetween(100, 999),
            'starts_at'       => $starts,
            'ends_at'         => $ends,
            'status'          => 'live',
        ];
    }

    public function agm(): static
    {
        return $this->state([
            'event_type' => 'agm',
            'voting_rules' => [
                'pay_per_vote'              => false,
                'price_per_vote_pesewas'    => 0,
                'max_votes_per_voter'       => 1,
                'requires_eligibility_list' => true,
                'anonymous_tally'           => false,
            ],
        ]);
    }

    public function election(): static
    {
        return $this->state([
            'event_type' => 'election',
            'voting_rules' => [
                'pay_per_vote'              => false,
                'price_per_vote_pesewas'    => 0,
                'max_votes_per_voter'       => 1,
                'requires_eligibility_list' => true,
                'anonymous_tally'           => true,
            ],
        ]);
    }

    public function draft(): static
    {
        return $this->state(['status' => 'draft']);
    }

    public function closed(): static
    {
        return $this->state([
            'status'   => 'closed',
            'ends_at'  => now()->subDay(),
        ]);
    }
}
