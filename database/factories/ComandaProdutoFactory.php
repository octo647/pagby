<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ComandaProduto;
use App\Models\Comanda;
use App\Models\Estoque;

class ComandaProdutoFactory extends Factory
{
    protected $model = ComandaProduto::class;
    public function definition(): array
    {
        $comanda = Comanda::inRandomOrder()->first();
        $estoque = Estoque::inRandomOrder()->first();
        $quantidade = $this->faker->numberBetween(1, 5);
        $preco = $estoque ? $estoque->preco_unitario : $this->faker->randomFloat(2, 5, 100);
        return [
            'comanda_id' => $comanda ? $comanda->id : 1,
            'estoque_id' => $estoque ? $estoque->id : 1,
            'quantidade' => $quantidade,
            'preco_unitario' => $preco,
            'subtotal' => $preco * $quantidade,
            'observacoes' => $this->faker->sentence(),
        ];
    }
}
