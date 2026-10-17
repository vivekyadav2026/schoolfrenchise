<?php

namespace Database\Factories;

use App\Models\Centre;
use App\Models\State;
use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;

class CentreFactory extends Factory
{
    protected $model = Centre::class;

    public function definition(): array
    {
        $code = 'DCE-' . fake()->unique()->numberBetween(2000, 9999);
        return [
            'centre_code' => $code,
            'centre_name' => fake()->company() . ' Computer Academy',
            'director_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'mobile' => fake()->unique()->numerify('98########'),
            'state_id' => 1,
            'district_id' => 1,
            'city' => fake()->city(),
            'address' => fake()->address(),
            'pincode' => '201301',
            'lab_capacity' => fake()->numberBetween(10, 40),
            'affiliation_date' => now()->subYears(1),
            'valid_upto' => now()->addYears(2),
            'status' => 'active',
        ];
    }
}
