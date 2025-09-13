<?php
// Este componente é usado para ajustar o balanço diário de uma filial específica. Ele permite que o proprietário selecione uma filial, e, para esta filial, mostre os dias em que o balanço diário não foi fechado, permitindo que o proprietário insira manualmente os valores de entrada, saída, saldo final e uma observação para cada dia não fechado. Isso é útil para corrigir ou ajustar os registros financeiros diários de uma filial específica. Compara as entradas do caixa com os registros financeiros para garantir que tudo esteja correto e atualizado. 
namespace App\Livewire\Proprietario;

use Livewire\Component;



class AjusteBalancoDiario extends Component
{
    // Propriedades para edição
    public $painelEdicaoAberto = false;
    public $dataEdicao;
    public $entradaEsperadaEdicao;
    public $entradaCaixaEdicao;
    public $saidaCaixaEdicao;
    public $saldoFinalEdicao;
    public $observacaoEdicao;

    public $branch_id;
    public $mes_selecionado;
    public $filiais = [];
    public $data;
    public $entrada;
    public $saida;
    public $saldo_final;
    public $observacao;
    public $diasNaoAjustados = [];
    public $data_inicio;
    public $data_fim;

    public function abrirPainelEdicao($data)
    {
        $this->dataEdicao = $data;
        $dia = collect($this->diasFilial)->where('data', $data)->first();
        $this->entradaEsperadaEdicao = $dia['entrada_esperada'] ?? 0;
        // Buscar registro do caixa
        $caixa = \App\Models\Caixa::where('branch_id', $this->branch_id)
            ->where('data', $data)
            ->first();
        $this->entradaCaixaEdicao = $caixa->total_entrada ?? 0;
        $this->saidaCaixaEdicao = $caixa->total_saida ?? 0;
        $this->saldoFinalEdicao = $caixa->saldo_final ?? 0;
        $this->observacaoEdicao = $caixa->observacao ?? '';
        $this->painelEdicaoAberto = true;
    }

    public function fecharPainelEdicao()
    {
        $this->painelEdicaoAberto = false;
        $this->dataEdicao = null;
        $this->entradaEsperadaEdicao = null;
        $this->entradaCaixaEdicao = null;
        $this->saidaCaixaEdicao = null;
        $this->saldoFinalEdicao = null;
        $this->observacaoEdicao = null;
    }

    public function salvarEdicao()
    {
        // Atualiza ou cria registro do caixa para o dia/filial
        $caixa = \App\Models\Caixa::updateOrCreate(
            [
                'branch_id' => $this->branch_id,
                'data' => $this->dataEdicao,
            ],
            [
                'total_entrada' => $this->entradaCaixaEdicao,
                'total_saida' => $this->saidaCaixaEdicao,
                'saldo_final' => $this->saldoFinalEdicao,
                'observacao' => $this->observacaoEdicao,
            ]
        );
        $this->painelEdicaoAberto = false;
        $this->atualizarDiasNaoAjustados();
        session()->flash('mensagem', 'Ajuste salvo com sucesso!');
    }

    public function mount()
    {
        $this->data = now()->format('Y-m-d');
        $this->filiais = \App\Models\Branch::all();
        $this->data_inicio = now()->subDays(90)->format('Y-m-d'); // Últimos 90 dias para ter mais dados
        $this->data_fim = now()->format('Y-m-d');
        $this->mes_selecionado = '';
        $this->atualizarDiasNaoAjustados();
    }
    public function updatedMesSelecionado()
    {
        // Não precisa atualizar dias, apenas filtra na view
    }

    public function atualizarDiasNaoAjustados()
    {
        $this->diasNaoAjustados = [];
        
        // Debug: verificar se há filiais
        if (empty($this->filiais) || count($this->filiais) == 0) {
            \Illuminate\Support\Facades\Log::info('Nenhuma filial encontrada no AjusteBalancoDiario');
            return;
        }

        $dias = \Carbon\CarbonPeriod::create($this->data_inicio, $this->data_fim);
        
        foreach ($this->filiais as $filial) {
            foreach ($dias as $dia) {
                $dataFormatada = $dia->format('Y-m-d');
                
                $caixa = \App\Models\Caixa::where('branch_id', $filial->id)
                    ->where('data', $dataFormatada)
                    ->first();

                $entradaEsperada = \App\Models\Appointment::where('branch_id', $filial->id)
                    ->whereDate('appointment_date', $dataFormatada)
                    ->whereIn('status', ['Confirmado', 'Realizado'])
                    ->sum('total');

                // Adiciona ao arranjo mesmo se não houver serviços pagos, para debug
                // Depois podemos filtrar apenas os que têm discrepância
                $entradaCaixa = $caixa->total_entrada ?? 0;
                
                // Considera como discrepância se:
                // 1. Há entrada esperada mas não há registro no caixa OU
                // 2. A entrada do caixa é diferente da esperada
                $temDiscrepancia = ($entradaEsperada > 0 && (!$caixa || $entradaCaixa != $entradaEsperada));
                
                if ($temDiscrepancia) {
                    $this->diasNaoAjustados[] = [
                        'data' => $dataFormatada,
                        'filial_id' => $filial->id,
                        'filial' => $filial->branch_name,
                        'entrada_esperada' => $entradaEsperada,
                        'entrada_caixa' => $entradaCaixa,
                        'ajustado' => false,
                    ];
                }
            }
        }
        
        // Debug
        \Illuminate\Support\Facades\Log::info('Dias não ajustados encontrados: ' . count($this->diasNaoAjustados));
    }

    public function updatedBranchId()
    {
        $this->atualizarDiasNaoAjustados();
    }

    public function updatedDataInicio()
    {
        $this->atualizarDiasNaoAjustados();
    }

    public function updatedDataFim()
    {
        $this->atualizarDiasNaoAjustados();
    }

    public function getDiasFilialProperty()
    {
        $result = collect($this->diasNaoAjustados);
        
        // Se uma filial está selecionada, filtra por ela
        if ($this->branch_id) {
            $result = $result->where('filial_id', $this->branch_id);
        }
        
        // Filtra por período se necessário
        $result = $result->filter(function($dia) {
            if ($this->data_inicio && $dia['data'] < $this->data_inicio) return false;
            if ($this->data_fim && $dia['data'] > $this->data_fim) return false;
            return true;
        });
        
        // Debug
        \Illuminate\Support\Facades\Log::info('getDiasFilialProperty - Total: ' . $result->count() . ', Branch ID: ' . $this->branch_id);
        
        return $result;
    }

    public function render()
    {
        return view('livewire.proprietario.ajuste-balanco-diario');
    }
}
