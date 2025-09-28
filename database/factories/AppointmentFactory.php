<?php


namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Buscar IDs de usuários que são funcionários (role_id = 2)
    $funcionarioIds = DB::table('role_user')->where('role_id', 2)->pluck('user_id')->toArray();
        $employee = null;
        if (count($funcionarioIds)) {
            $employee = \App\Models\User::whereIn('id', $funcionarioIds)->inRandomOrder()->first();
        }
        $customer = \App\Models\User::inRandomOrder()->first();
        $branch = \App\Models\Branch::inRandomOrder()->first();
        return [
            'employee_id' => $employee ? $employee->id : 1,
            'branch_id' => $branch ? $branch->id : 1,
            'customer_id' => $customer ? $customer->id : 1,
            'services' => '1,2',
            'appointment_date' => $this->faker->dateTimeBetween('-2 months','now'),
            'start_time' => $this->faker->time('H:i'),
            'end_time' => $this->faker->time('H:i'),
            'total' => $this->faker->randomFloat(2, 30, 300),
            'notes' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['Pendente','Confirmado','Realizado','Cancelado','bloqueio']),
        ];
    }
}
