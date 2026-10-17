<?php

namespace Database\Factories;

use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Centre;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupportTicketFactory extends Factory
{
    protected $model = SupportTicket::class;

    public function definition(): array
    {
        return [
            'ticket_no' => SupportTicket::generateTicketNo(),
            'user_id' => User::first()?->id ?? 1,
            'centre_id' => Centre::first()?->id ?? null,
            'category' => 'technical',
            'priority' => 'medium',
            'subject' => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'status' => 'open',
        ];
    }
}
