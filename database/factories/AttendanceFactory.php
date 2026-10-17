<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Batch;
use App\Models\Centre;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        return [
            'centre_id' => Centre::first()?->id ?? 1,
            'batch_id' => Batch::first()?->id ?? 1,
            'student_id' => Student::first()?->id ?? 1,
            'attendance_date' => now()->toDateString(),
            'status' => 'present',
            'in_time' => '10:00:00',
            'out_time' => '11:30:00',
            'remarks' => null,
        ];
    }
}
