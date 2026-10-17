<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true) . ' Course';
        return [
            'category_id' => 1,
            'course_code' => 'DCE-' . fake()->unique()->numberBetween(100, 999),
            'course_name' => ucwords($name),
            'slug' => Str::slug($name),
            'duration' => fake()->randomElement(['3 Months', '6 Months', '12 Months']),
            'eligibility' => '10th / 12th Pass',
            'default_course_fee' => fake()->randomElement([4500, 6500, 9500, 12000, 15000]),
            'registration_fee' => 500,
            'exam_fee' => 300,
            'certificate_fee' => 200,
            'syllabus' => fake()->paragraphs(3, true),
            'is_popular' => fake()->boolean(30),
            'status' => true,
        ];
    }
}
