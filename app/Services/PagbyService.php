<?php

namespace App\Services;

class PagbyService
{
    /**
     * Calcula o valor do plano conforme número de funcionários e periodicidade.
     *
     * @param int $numFuncionarios
     * @param string $periodicidade (mensal, trimestral, semestral, anual)
     * @return float
     */
    public function calcularValorPlano(int $numFuncionarios, string $periodicidade): float
    {
        $valorBase = 60.00;
        $acrescimoFuncionario = 0.20; // 20% por funcionário adicional
        $descontos = [
            'mensal' => 0,
            'trimestral' => 0.20,
            'semestral' => 0.30,
            'anual' => 0.40,
        ];
        $meses = [
            'mensal' => 1,
            'trimestral' => 3,
            'semestral' => 6,
            'anual' => 12,
        ];

        // Calcula valor base com acréscimo de funcionários
        $valor = $valorBase * pow(1 + $acrescimoFuncionario, $numFuncionarios - 1);

        // Aplica desconto da periodicidade
        $desconto = $descontos[$periodicidade] ?? 0;
        $valorFinal = $valor * (1 - $desconto);

        // Multiplica pela quantidade de meses do plano
        $valorFinal *= $meses[$periodicidade] ?? 1;

        return round($valorFinal, 2);
    }
}
