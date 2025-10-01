<?php


namespace App\Livewire\Proprietario;


use Livewire\Component;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;


class MeuPagby extends Component {
    public $planoAtual;
    public $statusPagamento;
    public $proximoVencimento;
    public $isBlocked;

    public function mount()
    {
        // Buscar tenant pelo domínio atual usando a tabela domains
        $host = request()->getHost();
    $domain = DB::connection('mysql')->table('domains')->where('domain', $host)->first();
        $tenant = null;
        if ($domain && $domain->tenant_id) {
            $tenant = Tenant::find($domain->tenant_id);
        }

        if ($tenant) {
            $this->planoAtual = $tenant->current_plan;
            $this->statusPagamento = $tenant->subscription_status;
            $this->proximoVencimento = $tenant->subscription_ends_at;
            $this->isBlocked = $tenant->is_blocked;
        } else {
            $this->planoAtual = null;
            $this->statusPagamento = null;
            $this->proximoVencimento = null;
            $this->isBlocked = null;
        }
    }
    public function cancelarAssinatura()
    {
        // Lógica para cancelar a assinatura
        $host = request()->getHost();
        $domain = DB::connection('mysql')->table('domains')->where('domain', $host)->first();
        $tenant = null;
        if ($domain && $domain->tenant_id) {
            $tenant = Tenant::find($domain->tenant_id);
        }

        if ($tenant) {
            // Atualiza os campos relacionados à assinatura
            $tenant->subscription_status = 'suspended';
            $tenant->current_plan = null;
            $tenant->subscription_started_at = null;
            $tenant->subscription_ends_at = null;
            $tenant->is_blocked = true; // Bloqueia o tenant após o cancelamento
            $tenant->save();

            // Atualiza as propriedades do componente
            $this->planoAtual = $tenant->current_plan;
            $this->statusPagamento = $tenant->subscription_status;
            $this->proximoVencimento = $tenant->subscription_ends_at;
            $this->isBlocked = $tenant->is_blocked;
        } else {
            $this->planoAtual = null;
            $this->statusPagamento = null;
            $this->proximoVencimento = null;
            $this->isBlocked = null;
        }
    }

    public function render()
    {
        return view('livewire.proprietario.meu-pagby', [
            'planoAtual' => $this->planoAtual,
            'statusPagamento' => $this->statusPagamento,
            'proximoVencimento' => $this->proximoVencimento,
            'isBlocked' => $this->isBlocked,
        ]);
    }
}
