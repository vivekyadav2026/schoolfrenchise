<?php

namespace Database\Factories;

use App\Models\AdmissionApplication;
use App\Models\Centre;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdmissionApplicationFactory extends Factory
{
    protected $model = AdmissionApplication::class;

    public function definition(): array
    {
        return [
            'application_no' => AdmissionApplication::generateApplicationNumber(),
            'centre_id' => Centre::first()?->id ?? 1,
            'course_id' => Course::first()?->id ?? 1,
            'applicant_name' => fake()->name(),
            'father_name' => fake()->name('male'),
            'mother_name' => fake()->name('female'),
            'dob' => fake()->date('Y-m-d', '2005-01-01'),
            'gender' => fake()->randomElement(['male', 'female']),
            'mobile' => fake()->unique()->numerify('96########'),
            'email' => fake()->unique()->safeEmail(),
            'state_id' => 1,
            'district_id' => 1,
            'city' => fake()->city(),
            'address' => fake()->address(),
            'pincode' => '110001',
            'highest_qualification' => '12th Pass',
            'status' => 'pending',
        ];
    }
}
