<?php

namespace App\Observers;

use App\Models\ComandaProduto;
use App\Models\FidelidadeReward;
use Illuminate\Support\Facades\Log;

class ComandaProdutoObserver
{
    /**
     * Handle the ComandaProduto "created" event.
     * 
     * Quando um produto é vendido:
     * 1. Incrementa contador de vendas do produto
     * 2. Gera recompensa de fidelidade para o cliente
     */
    public function created(ComandaProduto $comandaProduto): void
    {
        try {
            // 1. Incrementar contador de vendas do produto
            if ($comandaProduto->estoque) {
                $comandaProduto->estoque->incrementarVendas($comandaProduto->quantidade);
            }

            // 2. Gerar recompensa de fidelidade
            $comanda = $comandaProduto->comanda;
            
            // Apenas se houver cliente identificado
            if ($comanda && $comanda->client_id) {
                $this->gerarRecompensa($comandaProduto, $comanda);
            }

            // 3. Recalcular totais da comanda (inclui produtos no total_geral)
            if ($comanda) {
                $comanda->recalcularTotais();
                
                Log::info('💰 Totais da comanda recalculados após adicionar produto', [
                    'comanda_id' => $comanda->id,
                    'subtotal_produtos' => $comanda->subtotal_produtos,
                    'total_geral' => $comanda->total_geral,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao processar venda de produto: ' . $e->getMessage(), [
                'comanda_produto_id' => $comandaProduto->id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Gera recompensa de fidelidade baseada na venda
     */
    private function gerarRecompensa(ComandaProduto $comandaProduto, $comanda): void
    {
        // Validar se o subtotal é válido
        if (!$comandaProduto->subtotal || $comandaProduto->subtotal <= 0) {
            Log::warning('ComandaProduto com subtotal inválido - recompensa não gerada', [
                'comanda_produto_id' => $comandaProduto->id,
                'subtotal' => $comandaProduto->subtotal,
            ]);
            return;
        }

        // Configurações de recompensa (podem vir de config ou tabela de configuração)
        $config = $this->getRecompensaConfig($comandaProduto);

        switch ($config['tipo']) {
            case 'credito':
                $valorRecompensa = $comandaProduto->subtotal * $config['percentual'];
                
                // Validar se o valor da recompensa é válido (mínimo R$ 0,01)
                if ($valorRecompensa < 0.01) {
                    Log::warning('Valor de recompensa muito baixo - não gerada', [
                        'cliente_id' => $comanda->client_id,
                        'valor_calculado' => $valorRecompensa,
                        'subtotal' => $comandaProduto->subtotal,
                        'percentual' => $config['percentual'],
                    ]);
                    return;
                }
                
                FidelidadeReward::criarCredito(
                    userId: $comanda->client_id,
                    valor: $valorRecompensa,
                    origem: 'compra_produto',
                    comandaProdutoId: $comandaProduto->id,
                    diasValidade: $config['dias_validade']
                );

                Log::info("Crédito de fidelidade gerado", [
                    'cliente_id' => $comanda->client_id,
                    'valor' => $valorRecompensa,
                    'produto' => $comandaProduto->estoque->produto_nome ?? 'N/A'
                ]);
                break;

            case 'cupom':
                FidelidadeReward::criarCupom(
                    userId: $comanda->client_id,
                    percentualDesconto: $config['percentual_desconto'],
                    origem: 'compra_produto',
                    comandaProdutoId: $comandaProduto->id,
                    diasValidade: $config['dias_validade']
                );

                Log::info("Cupom de fidelidade gerado", [
                    'cliente_id' => $comanda->client_id,
                    'desconto' => $config['percentual_desconto'] . '%',
                ]);
                break;

            case 'servico_gratis':
                if (isset($config['service_id'])) {
                    FidelidadeReward::criarServicoGratis(
                        userId: $comanda->client_id,
                        serviceId: $config['service_id'],
                        origem: 'compra_produto',
                        comandaProdutoId: $comandaProduto->id,
                        diasValidade: $config['dias_validade']
                    );

                    Log::info("Serviço grátis gerado", [
                        'cliente_id' => $comanda->client_id,
                        'service_id' => $config['service_id'],
                    ]);
                }
                break;
        }
    }

    /**
     * Determina configuração de recompensa baseada no produto/categoria/valor
     * 
     * Você pode customizar essa lógica conforme sua estratégia de negócio
     */
    private function getRecompensaConfig(ComandaProduto $comandaProduto): array
    {
        $produto = $comandaProduto->estoque;
        $valor = $comandaProduto->subtotal;

        // ESTRATÉGIA 1: Por valor da compra
        if ($valor >= 100) {
            return [
                'tipo' => 'credito',
                'percentual' => 0.25, // 25% para compras acima de R$ 100
                'dias_validade' => 30,
            ];
        } elseif ($valor >= 50) {
            return [
                'tipo' => 'credito',
                'percentual' => 0.20, // 20% para compras entre R$ 50-100
                'dias_validade' => 30,
            ];
        }

        // ESTRATÉGIA 2: Por categoria do produto (exemplo)
        if ($produto && $produto->categoria === 'Premium') {
            return [
                'tipo' => 'cupom',
                'percentual_desconto' => 15,
                'dias_validade' => 30,
            ];
        }

        // ESTRATÉGIA 3: Por produto específico (exemplo)
        // if ($produto && str_contains(strtolower($produto->produto_nome), 'kit')) {
        //     return [
        //         'tipo' => 'servico_gratis',
        //         'service_id' => 5, // ID do serviço de barba
        //         'dias_validade' => 60,
        //     ];
        // }

        // Padrão: 15% em créditos
        return [
            'tipo' => 'credito',
            'percentual' => 0.15,
            'dias_validade' => 30,
        ];
    }

    /**
     * Handle the ComandaProduto "updated" event.
     */
    public function updated(ComandaProduto $comandaProduto): void
    {
        // Recalcular totais se quantidade ou preço mudaram
        if ($comandaProduto->wasChanged(['quantidade', 'preco_unitario', 'subtotal'])) {
            if ($comandaProduto->comanda) {
                $comandaProduto->comanda->recalcularTotais();
                
                Log::info('💰 Totais da comanda recalculados após atualizar produto', [
                    'comanda_id' => $comandaProduto->comanda_id,
                    'produto_id' => $comandaProduto->estoque_id,
                    'total_geral' => $comandaProduto->comanda->total_geral,
                ]);
            }
        }
    }

    /**
     * Handle the ComandaProduto "deleted" event.
     */
    public function deleted(ComandaProduto $comandaProduto): void
    {
        // Recalcular totais quando produto é removido
        if ($comandaProduto->comanda) {
            $comandaProduto->comanda->recalcularTotais();
            
            Log::info('💰 Totais da comanda recalculados após remover produto', [
                'comanda_id' => $comandaProduto->comanda_id,
                'produto_id' => $comandaProduto->estoque_id,
                'total_geral' => $comandaProduto->comanda->total_geral,
            ]);
        }

        // Se produto foi removido da comanda, considerar reverter recompensa
        // (apenas se a comanda ainda não foi finalizada)
    }
}
