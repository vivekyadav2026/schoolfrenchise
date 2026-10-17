<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\Centre;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'centre_id' => Centre::first()?->id ?? 1,
            'category' => fake()->randomElement(['rent', 'electricity', 'office_supplies', 'internet']),
            'title' => fake()->words(3, true),
            'amount' => fake()->randomFloat(2, 500, 15000),
            'expense_date' => now()->subDays(fake()->numberBetween(1, 30))->toDateString(),
            'payment_mode' => 'cash',
            'notes' => fake()->sentence(),
            'recorded_by' => User::first()?->id ?? null,
        ];
    }
}
