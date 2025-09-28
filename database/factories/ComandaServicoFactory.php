<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ComandaServico;
use App\Models\Comanda;
use App\Models\Service;
use App\Models\User;

class ComandaServicoFactory extends Factory
{
    protected $model = ComandaServico::class;
    public function definition(): array
    {
        $comanda = Comanda::inRandomOrder()->first();
        $service = Service::inRandomOrder()->first();
        $funcionario = User::inRandomOrder()->first();
        $preco = $service ? $service->price : $this->faker->randomFloat(2, 20, 150);
        $quantidade = $this->faker->numberBetween(1, 3);
        return [
            'comanda_id' => $comanda ? $comanda->id : 1,
            'service_id' => $service ? $service->id : 1,
            'funcionario_id' => $funcionario ? $funcionario->id : 1,
            'quantidade' => $quantidade,
            'preco_unitario' => $preco,
            'subtotal' => $preco * $quantidade,
            'status_servico' => $this->faker->randomElement(['Aguardando','Em Andamento','Concluído']),
            'observacoes' => $this->faker->sentence(),
        ];
    }
}
