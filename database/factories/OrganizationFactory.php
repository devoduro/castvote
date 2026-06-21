<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    public function definition(): array
    {
        $orgs = [
            ['name' => 'Ghana Music Awards Ltd', 'email' => 'admin@ghanamusicawards.com'],
            ['name' => 'Charterhouse Productions', 'email' => 'info@charterhouse.com.gh'],
            ['name' => 'UTV Ghana', 'email' => 'events@utv.com.gh'],
            ['name' => 'University of Ghana SRC', 'email' => 'src@ug.edu.gh'],
            ['name' => 'KNUST Student Representative Council', 'email' => 'src@knust.edu.gh'],
            ['name' => 'Ashanti Gold SC', 'email' => 'agm@ashantigold.com'],
        ];

        $pick = $this->faker->randomElement($orgs);

        return [
            'name'                   => $pick['name'],
            'contact_email'          => $pick['email'],
            'momo_settlement_number' => '0' . $this->faker->numerify('2########'),
            'data_protection_reg_no' => 'DPC-' . $this->faker->numerify('####-####'),
        ];
    }
}
