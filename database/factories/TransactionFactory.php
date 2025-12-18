<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_code' => $this->faker->unique()->uuid,
            'appointment_id' => \App\Models\Appointment::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'payment_method' => $this->faker->randomElement(['cartao', 'dinheiro', 'pix']),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
        ];
    }
}
