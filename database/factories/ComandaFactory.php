<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Comanda;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Branch;

class ComandaFactory extends Factory
{
    protected $model = Comanda::class;
    public function definition(): array
    {
        $branch = Branch::inRandomOrder()->first();
        $employee = User::inRandomOrder()->first();
        $customer = User::inRandomOrder()->first();
        $appointment = Appointment::inRandomOrder()->first();
        return [
            'branch_id' => $branch ? $branch->id : 1,
            'appointment_id' => $appointment ? $appointment->id : null,
            'numero_comanda' => $this->faker->unique()->numerify('COM-#####'),
            'cliente_nome' => $customer ? $customer->name : $this->faker->name(),
            'cliente_telefone' => $customer ? $customer->phone : $this->faker->phoneNumber(),
            'funcionario_id' => $employee ? $employee->id : 1,
            'status' => $this->faker->randomElement(['Aberta','Finalizada','Cancelada']),
            'data_abertura' => $this->faker->dateTimeBetween('-2 months','now'),
            'data_fechamento' => $this->faker->dateTimeBetween('-2 months','now'),
            'subtotal_servicos' => $this->faker->randomFloat(2, 30, 300),
            'subtotal_produtos' => $this->faker->randomFloat(2, 10, 200),
            'desconto' => $this->faker->randomFloat(2, 0, 50),
            'total_geral' => $this->faker->randomFloat(2, 50, 500),
            'observacoes' => $this->faker->sentence(),
        ];
    }
}
