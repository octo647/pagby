<?php
namespace Database\Factories;

use App\Models\BooksyProspect;
use Illuminate\Database\Eloquent\Factories\Factory;

class BooksyProspectFactory extends Factory
{
    protected $model = BooksyProspect::class;

    public function definition(): array
    {
        return [
            'owner_name' => $this->faker->name(),
            'salon_name' => $this->faker->company(),
            'salon_type' => $this->faker->randomElement(['Barbearia', 'Salão de Beleza', 'Outro']),
            'employee_count' => $this->faker->numberBetween(1, 20),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'notes' => $this->faker->sentence(),
            'converted' => false,
        ];
    }
}
