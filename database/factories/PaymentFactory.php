<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentFactory extends Factory
{
    private static array $ghanaPhonePrefixes = [
        'mtn'       => ['024', '054', '055', '059'],
        'vodafone'  => ['020', '050'],
        'airteltigo'=> ['026', '056', '027', '057'],
    ];

    public function definition(): array
    {
        $network = $this->faker->randomElement(['mtn', 'vodafone', 'airteltigo']);
        $prefix  = $this->faker->randomElement(self::$ghanaPhonePrefixes[$network]);
        $phone   = $prefix . $this->faker->numerify('#######');
        $qty     = $this->faker->numberBetween(1, 10);

        return [
            'event_id'            => Event::factory(),
            'provider'            => 'paystack',
            'provider_reference'  => 'cv_' . Str::random(20),
            'amount_pesewas'      => $qty * 100,
            'currency'            => 'GHS',
            'phone_number'        => $phone,
            'momo_network'        => $network,
            'status'              => 'success',
            'raw_webhook_payload' => null,
            'metadata'            => [
                'quantity'    => $qty,
                'nominee_id'  => null,
                'category_id' => null,
            ],
            'verified_at'         => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state([
            'status'      => 'pending',
            'verified_at' => null,
        ]);
    }

    public function failed(): static
    {
        return $this->state([
            'status'      => 'failed',
            'verified_at' => null,
        ]);
    }
}
