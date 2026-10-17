<?php

namespace Database\Factories;

use App\Models\FeeStructure;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeeStructureFactory extends Factory
{
    protected $model = FeeStructure::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::first()?->id ?? 1,
            'centre_id' => null,
            'name' => 'Standard 3-Installment Plan',
            'total_fee' => 9000.00,
            'admission_fee' => 1500.00,
            'tuition_fee' => 6500.00,
            'exam_fee' => 500.00,
            'certificate_fee' => 500.00,
            'installments_count' => 3,
            'terms' => 'Fees once paid are non-refundable.',
            'is_active' => true,
        ];
    }
}
