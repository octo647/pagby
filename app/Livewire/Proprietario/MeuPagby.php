<?php


namespace App\Livewire\Proprietario;


use Livewire\Component;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use App\Models\PagByPayment;
use Illuminate\Support\Facades\Log;

class MeuPagby extends Component {
    public $planoAtual;
    public $statusPagamento;
    public $proximoVencimento;
    public $isBlocked;
    public $criado_em;
    public $employeeCount;

    public function mount()
    {
      
        // Buscar tenant pelo domínio atual usando a tabela domains
        $host = request()->getHost();
        $domain = DB::connection('mysql')->table('domains')->where('domain', $host)->first();
       
        
        $tenant = null;
        if ($domain && $domain->tenant_id) {
            $tenant = PagByPayment::on('mysql')->where('tenant_id', $domain->tenant_id)->latest()->first();  
           
                
        }
        
        

        if ($tenant) {
            $this->planoAtual = $tenant->plan;
            $this->employeeCount = $tenant->employee_count;
            $this->statusPagamento = $tenant->status;
            $this->criado_em = $tenant->created_at;
            if($this->planoAtual === 'mensal'){
            $this->proximoVencimento = $this->criado_em ->addMonth();
            } elseif ($this->planoAtual === 'trimestral'){
                $this->proximoVencimento = $this->criado_em ->addMonths(3);
            } elseif ($this->planoAtual === 'semestral'){
                $this->proximoVencimento = $this->criado_em ->addMonths(6);
            } elseif ($this->planoAtual === 'anual'){
                $this->proximoVencimento = $this->criado_em ->addYear();
            } else {
                $this->proximoVencimento = null;
            }
            $this->isBlocked = null;
        } else {
            $this->planoAtual = null;
            $this->employeeCount = null;
            $this->statusPagamento = null;
            $this->proximoVencimento = null;
            $this->isBlocked = null;
        }
        
    }
    public function cancelarAssinatura()
    {
        // Lógica para cancelar a assinatura no MercadoPago e localmente
   
        $host = request()->getHost();
      
        $domain = DB::connection('mysql')->table('domains')->where('domain', $host)->first();
        $tenant = null;
        if ($domain && $domain->tenant_id) {
            $tenant = Tenant::find($domain->tenant_id);
        }
     

        if ($tenant) {
            // Buscar pagamento ativo do tenant
            $pagamento = PagByPayment::on('mysql')
                ->where('tenant_id', $tenant->id)
                ->whereIn('status', ['RECEIVED', 'approved', 'pending'])
                ->orderByDesc('id')
                ->first();

            if ($pagamento && $pagamento->external_id) {
                // Cancelar assinatura no MercadoPago
                $accessToken = config('services.pagby.access_token');
                $preapprovalId = $pagamento->external_id;
                $url = 'https://api.mercadopago.com/preapproval/' . $preapprovalId;
                $data = [
                    'status' => 'cancelled'
                ];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json'
                ]);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                curl_close($ch);

                if ($httpCode === 200) {
                    // Atualiza status do pagamento local
                    $pagamento->status = 'cancelled';
                    $pagamento->save();

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
                    Log::info('Assinatura cancelada com sucesso no MercadoPago e localmente para o tenant ID: ' . $tenant->id);
                } else {
                    // Falha ao cancelar no MercadoPago
                    // Opcional: logar erro ou exibir mensagem
                    Log::error('Erro ao cancelar assinatura no MercadoPago', [
                        'http_code' => $httpCode,
                        'response' => $response,
                        'curl_error' => $curlError,
                    ]);
                    // $curlError, $response
                }
            } else {
                // Não encontrou pagamento ativo ou preapproval_id
                // Opcional: logar erro ou exibir mensagem
            }
        } else {
            $this->planoAtual = null;
            $this->statusPagamento = null;
            $this->proximoVencimento = null;
            $this->isBlocked = null;
        }
    }
     public function verOutrosPlanos()
    {
        return redirect()->route('pagby-subscription.escolher-plano', ['plan' => 'trimestral']);
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
