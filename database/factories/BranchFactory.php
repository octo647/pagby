<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {   
        $bairros = ['Centro', 'Jardim América', 'Vila Nova', 'Copacabana', 'Moema', 'Pinheiros', 'Barra', 'Boa Viagem', 'S. Mateus', 'S. Pedro', 'Bairú', 'Glória'];
        return [
            'branch_name' => $this->faker->randomElement($bairros),
            'address' => $this->faker->streetAddress,        
            'phone' => $this->faker->phoneNumber,
            'whatsapp' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'cnpj' => $this->faker->unique()->numerify('##.###.###/####-##'),
            'require_advance_payment' => $this->faker->boolean(25), // 25% chance of requiring advance payment
            'commission' => $this->faker->numberBetween(40, 50), // Comissão entre 40% e 50%
        ];
    }
}
