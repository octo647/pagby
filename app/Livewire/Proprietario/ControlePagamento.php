<?php
namespace App\Livewire\Proprietario;

use Livewire\Component;
use App\Models\Branch;
use App\Models\User;
use App\Models\Comanda;
use App\Models\Estoque;
use Illuminate\Support\Carbon;

class ControlePagamento extends Component
{
    public $branches;
    public $selectedBranch = null;
    public $funcionarios = [];
    public $selectedFuncionario = null;
    public $periodoSelecionado = 'semanal'; // semanal, quinzenal, mensal
    public $dataInicio;
    public $dataFim;
    public $pagamentos = [];

    public function mount()
    {
        $this->branches = Branch::all();
        $this->selectedBranch = $this->branches->first()->id ?? null;
        $this->atualizarFuncionarios();
    $this->setPeriodo($this->periodoSelecionado);
    }

    public function atualizarFuncionarios()
    {
        $this->funcionarios = User::whereHas('branches', function($q){
            $q->where('branch_id', $this->selectedBranch);
        })->get();
        // Se o usuário selecionar "Todos", selectedFuncionario deve ser null
        $funcionariosCollection = collect($this->funcionarios);
        if ($this->selectedFuncionario && !in_array($this->selectedFuncionario, $funcionariosCollection->pluck('id')->toArray())) {
            $this->selectedFuncionario = null;
        }
     
    }

    public function setPeriodo($periodo)
    {
        
        
        $hoje = Carbon::today();
        if ($periodo === 'semanal') {
            $this->dataInicio = $hoje->copy()->startOfWeek();
            $this->dataFim = $hoje->copy()->endOfWeek();
        } elseif ($periodo === 'quinzenal') {
            $this->dataInicio = $hoje->copy()->subDays($hoje->day <= 15 ? $hoje->day - 1 : $hoje->day - 16);
            $this->dataFim = $this->dataInicio->copy()->addDays(14);
        } else {
            $this->dataInicio = $hoje->copy()->startOfMonth();
            $this->dataFim = $hoje->copy()->endOfMonth();
        }
        $this->calcularPagamentos();
    }

    public function updatedSelectedBranch()
    {
        $this->atualizarFuncionarios();
        $this->calcularPagamentos();
    }

    public function updatedSelectedFuncionario()
    {
        $this->calcularPagamentos();
    }
    public function updatedPeriodoSelecionado($value)
    {
        logger('updatedPeriodoSelecionado chamado: ' . $value);
        $this->setPeriodo($value);
    }
    public function calcularPagamentos()
    {
        $branch = Branch::find($this->selectedBranch);
        $commission_servicos = $branch->commission ?? 0;
        $pagamentos = [];
        $funcionarios = collect($this->funcionarios);
        // Se um funcionário específico foi selecionado, filtra apenas ele
        if ($this->selectedFuncionario) {
            $funcionarios = $funcionarios->filter(function($f) {
                return $f->id == $this->selectedFuncionario;
            });
        }
        foreach ($funcionarios as $func) {
            // Faturamento de serviços
            $comandas = Comanda::where('branch_id', $branch->id)
                ->where('funcionario_id', $func->id)
                ->whereBetween('data_fechamento', [$this->dataInicio, $this->dataFim])
                ->where('status', 'Finalizada')
                ->get();
            $total_servicos = $comandas->sum('subtotal_servicos');
            // Faturamento de produtos
            $total_produtos = $comandas->sum('subtotal_produtos');
            $percentual_produtos = 0;
            $produtos_vendidos = [];
            foreach ($comandas as $comanda) {
                foreach ($comanda->comandaProdutos as $cp) {
                    $estoque = Estoque::find($cp->estoque_id);
                    if ($estoque) {
                        $produtos_vendidos[] = $estoque->percentual_produtos;
                    }
                }
            }
            if (count($produtos_vendidos)) {
                $percentual_produtos = array_sum($produtos_vendidos) / count($produtos_vendidos);
            }
            $valor_servicos = $total_servicos * ($commission_servicos / 100);
            $valor_produtos = $total_produtos * ($percentual_produtos / 100);
            $pagamentos[] = [
                'funcionario' => $func,
                'total_servicos' => $total_servicos,
                'percentual_servicos' => $commission_servicos,
                'valor_servicos' => $valor_servicos,
                'total_produtos' => $total_produtos,
                'percentual_produtos' => $percentual_produtos,
                'valor_produtos' => $valor_produtos,
                'total' => $valor_servicos + $valor_produtos,
            ];
        }
        $this->pagamentos = $pagamentos;
    }

    public function render()
    {
        return view('livewire.proprietario.controle-pagamento', [
            'branches' => $this->branches,
            'funcionarios' => $this->funcionarios,
            'pagamentos' => $this->pagamentos,
            'periodoSelecionado' => $this->periodoSelecionado,
            'dataInicio' => $this->dataInicio,
            'dataFim' => $this->dataFim,
        ]);
    }
}

