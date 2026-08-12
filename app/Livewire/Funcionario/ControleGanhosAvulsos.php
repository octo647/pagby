<?php

namespace App\Livewire\Funcionario;

use Livewire\Component;
use App\Models\Branch;
use App\Models\ComandaServico;
use App\Models\ComandaProduto;
use App\Models\Comanda;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class ControleGanhosAvulsos extends Component
{
    public $periodoInicio;
    public $periodoFim;
    public $servicos = [];
    public $produtos = [];
    public $comissao = 0;
    public $totalServicos = 0;
    public $totalProdutos = 0;
    public $totalReceber = 0;

    public function mount()
    {
        $this->periodoInicio = now()->startOfMonth()->format('Y-m-d');
        $this->periodoFim = now()->endOfMonth()->format('Y-m-d');
        $this->carregarDados();
    }

    public function updatedPeriodoInicio()
    {
        $this->carregarDados();
    }

    public function updatedPeriodoFim()
    {
        $this->carregarDados();
    }

    public function carregarDados()
    {
        $user = Auth::user();
        $userId = $user->id;
       
        // Buscar todos os branch_id do usuário na tabela branch_user
        $branchIds = DB::table('branch_user')->where('user_id', $userId)->pluck('branch_id');
        // Pega a comissão da primeira filial encontrada (ajuste se quiser média ou outra lógica)
        $branch = Branch::find($branchIds->first());
        $this->comissao = $branch ? ($branch->commission ?? 0) : 0;

        $comandasFinalizadas = Comanda::whereIn('branch_id', $branchIds)
            ->where('status', 'Finalizada')
            ->whereBetween('data_fechamento', [$this->periodoInicio, $this->periodoFim])
            ->pluck('id');
            

        $this->servicos = ComandaServico::whereIn('comanda_id', $comandasFinalizadas)
            ->where('funcionario_id', $userId)
            ->get();
            

        $this->produtos = ComandaProduto::whereIn('comanda_id', $comandasFinalizadas)
            ->where('funcionario_id', $userId)
            ->get();
            

    $this->totalServicos = collect($this->servicos)->sum('subtotal');
    $this->totalProdutos = collect($this->produtos)->sum('subtotal');
    $this->totalReceber = ($this->totalServicos + $this->totalProdutos) * ($this->comissao / 100);
    }

    public function render()
    {
        return view('livewire.funcionario.servicos-avulsos');
    }
}
