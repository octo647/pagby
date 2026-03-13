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
        $valorBase = config('pricing.base_price_per_employee');
        $acrescimoFuncionario = 1; // 30% por funcionário adicional
        $descontos = [
            'mensal' => 0,
            'semestral' => 0.10,
            'anual' => 0.15,
        ];
        $meses = [
            'mensal' => 1,            
            'semestral' => 6,
            'anual' => 12,
        ];

        // Calcula valor base com acréscimo de funcionários
        $valor = $valorBase * (1 + $acrescimoFuncionario * ($numFuncionarios - 1));

        // Aplica desconto da periodicidade
        $desconto = $descontos[$periodicidade] ?? 0;
        $valorFinal = $valor * (1 - $desconto);

        // Multiplica pela quantidade de meses do plano
        $valorFinal *= $meses[$periodicidade] ?? 1;

        return round($valorFinal, 2);
    }

    /**
     * Calcula o ajuste proporcional ao modificar o número de funcionários durante a vigência do plano
     * 
     * @param int $funcionariosAtuais Número atual de funcionários
     * @param int $novoNumeroFuncionarios Novo número de funcionários desejado
     * @param string $periodicidade Periodicidade do plano (mensal, trimestral, etc)
     * @param \Carbon\Carbon $dataInicio Data de início do plano atual
     * @param \Carbon\Carbon $dataFim Data de término do plano atual
     * @return array ['ajuste' => float, 'tipo' => 'credito'|'debito', 'dias_restantes' => int, 'valor_proporcional' => float]
     */
    public function calcularAjusteProporcional(
        int $funcionariosAtuais,
        int $novoNumeroFuncionarios,
        string $periodicidade,
        \Carbon\Carbon $dataInicio,
        \Carbon\Carbon $dataFim
    ): array {
        // Calcula valores dos planos
        $valorPlanoAtual = $this->calcularValorPlano($funcionariosAtuais, $periodicidade);
        $valorNovoPlano = $this->calcularValorPlano($novoNumeroFuncionarios, $periodicidade);
        
        // Calcula dias do período total e dias restantes
        $diasTotais = $dataInicio->diffInDays($dataFim);
        $diasRestantes = now()->diffInDays($dataFim);
        
        // Evita divisão por zero
        if ($diasTotais <= 0) {
            return [
                'ajuste' => 0,
                'tipo' => 'neutro',
                'dias_restantes' => 0,
                'valor_proporcional' => 0,
                'percentual_restante' => 0,
            ];
        }
        
        // Calcula o percentual do tempo restante
        $percentualRestante = ($diasRestantes / $diasTotais) * 100;
        
        // Calcula o valor proporcional do tempo restante para ambos os planos
        $valorProporcionadoAtual = ($valorPlanoAtual * $diasRestantes) / $diasTotais;
        $valorProporcionadoNovo = ($valorNovoPlano * $diasRestantes) / $diasTotais;
        
        // Calcula o ajuste (positivo = cobrar mais, negativo = crédito)
        $ajuste = $valorProporcionadoNovo - $valorProporcionadoAtual;
        
        return [
            'ajuste' => round(abs($ajuste), 2),
            'tipo' => $ajuste > 0 ? 'debito' : ($ajuste < 0 ? 'credito' : 'neutro'),
            'dias_restantes' => $diasRestantes,
            'valor_proporcional_atual' => round($valorProporcionadoAtual, 2),
            'valor_proporcional_novo' => round($valorProporcionadoNovo, 2),
            'percentual_restante' => round($percentualRestante, 2),
            'valor_plano_atual' => round($valorPlanoAtual, 2),
            'valor_novo_plano' => round($valorNovoPlano, 2),
        ];
    }
}
