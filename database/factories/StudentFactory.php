<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\Centre;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        $regNo = 'DCE/2026/101/' . fake()->unique()->numberBetween(1000, 9999);
        $rollNo = 'DCE260' . fake()->unique()->numberBetween(1000, 9999);

        return [
            'centre_id' => Centre::first()?->id ?? 1,
            'registration_no' => $regNo,
            'roll_no' => $rollNo,
            'full_name' => fake()->name(),
            'father_name' => fake()->name('male'),
            'mother_name' => fake()->name('female'),
            'dob' => fake()->date('Y-m-d', '2005-01-01'),
            'gender' => fake()->randomElement(['male', 'female']),
            'mobile' => fake()->unique()->numerify('97########'),
            'email' => fake()->unique()->safeEmail(),
            'state_id' => 1,
            'district_id' => 1,
            'city' => fake()->city(),
            'address' => fake()->streetAddress(),
            'pincode' => '110001',
            'highest_qualification' => '12th Pass',
            'status' => 'enrolled',
        ];
    }
}
