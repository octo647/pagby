<?php


namespace App\Livewire\Proprietario;


use Livewire\Component;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use App\Models\PagByPayment;
use Illuminate\Support\Facades\Log;
use App\Services\PagbyService;
use App\Services\AsaasService;
use App\Models\PlanAdjustment;
use Carbon\Carbon;

class MeuPagby extends Component {
    public $planoAtual;
    public $statusPagamento;
    public $proximoVencimento;
    public $isBlocked;
    public $criado_em;
    public $employeeCount;
    public $showAjusteModal = false;
    public $novoNumeroFuncionarios;
    public $ajusteCalculado = null;
    public $ajustePendente = null;

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
            
            // Verificar se há ajuste pendente para mostrar o número correto
            $ajustePendenteAtual = PlanAdjustment::where('tenant_id', $domain->tenant_id)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->first();
            
            // Se houver ajuste pendente não pago, mostrar o número ANTES do ajuste
            if ($ajustePendenteAtual) {
                $this->employeeCount = $ajustePendenteAtual->employee_count_before;
            }
            
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

    public function abrirModalAjuste()
    {
        // Verificar se há ajuste pendente
        $host = request()->getHost();
        $domain = DB::connection('mysql')->table('domains')->where('domain', $host)->first();
        
        if ($domain && $domain->tenant_id) {
            $this->ajustePendente = PlanAdjustment::where('tenant_id', $domain->tenant_id)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->first();
            
            // Se houver ajuste pendente, usar o número de funcionários ANTES do ajuste pendente
            // porque o ajuste pendente não foi pago ainda
            if ($this->ajustePendente) {
                $this->employeeCount = $this->ajustePendente->employee_count_before;
            }
        }

        $this->novoNumeroFuncionarios = $this->employeeCount;
        $this->ajusteCalculado = null;
        $this->showAjusteModal = true;
    }

    public function fecharModalAjuste()
    {
        $this->showAjusteModal = false;
        $this->ajusteCalculado = null;
    }

    public function cancelarAjustePendente()
    {
        if (!$this->ajustePendente) {
            session()->flash('error', 'Nenhum ajuste pendente encontrado.');
            return;
        }

        $host = request()->getHost();
        $domain = DB::connection('mysql')->table('domains')->where('domain', $host)->first();
        
        if (!$domain || !$domain->tenant_id) {
            session()->flash('error', 'Tenant não encontrado.');
            return;
        }

        $tenant = Tenant::find($domain->tenant_id);
        if (!$tenant) {
            session()->flash('error', 'Tenant não encontrado.');
            return;
        }

        try {
            // Se o ajuste pendente tinha cobrança no Asaas, cancelar
            if ($this->ajustePendente->asaas_payment_id) {
                $asaasService = new AsaasService();
                $result = $asaasService->cancelarCobranca($this->ajustePendente->asaas_payment_id);
                
                if (!$result['success']) {
                    Log::warning('Erro ao cancelar cobrança Asaas', [
                        'adjustment_id' => $this->ajustePendente->id,
                        'asaas_payment_id' => $this->ajustePendente->asaas_payment_id,
                        'error' => $result['message'] ?? 'Erro desconhecido',
                    ]);
                }
            }
            
            // Marcar ajuste como cancelado
            $this->ajustePendente->status = 'cancelled';
            $this->ajustePendente->notes = ($this->ajustePendente->notes ?? '') . ' | Cancelado pelo usuário.';
            $this->ajustePendente->save();

            // Reverter o employee_count do tenant
            $tenant->employee_count = $this->ajustePendente->employee_count_before;
            $tenant->save();

            // Reverter também no último pagamento
            $pagamento = PagByPayment::on('mysql')
                ->where('tenant_id', $tenant->id)
                ->latest()
                ->first();
            if ($pagamento) {
                $pagamento->employee_count = $this->ajustePendente->employee_count_before;
                $pagamento->save();
            }

            Log::info('Ajuste pendente cancelado pelo usuário', [
                'adjustment_id' => $this->ajustePendente->id,
                'tenant_id' => $tenant->id,
                'employee_count_revertido_para' => $this->ajustePendente->employee_count_before,
            ]);

            session()->flash('mensagem', 'Ajuste pendente cancelado com sucesso!');
            $this->mount(); // Recarrega os dados
            $this->fecharModalAjuste();

        } catch (\Exception $e) {
            Log::error('Erro ao cancelar ajuste pendente', [
                'adjustment_id' => $this->ajustePendente->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Erro ao cancelar ajuste. Tente novamente ou contate o suporte.');
        }
    }

    public function calcularAjuste()
    {
        if (!$this->novoNumeroFuncionarios || $this->novoNumeroFuncionarios < 1) {
            session()->flash('error', 'Número de funcionários inválido.');
            return;
        }

        // Buscar o número real de funcionários do tenant
        $host = request()->getHost();
        $domain = DB::connection('mysql')->table('domains')->where('domain', $host)->first();
        
        if (!$domain || !$domain->tenant_id) {
            session()->flash('error', 'Tenant não encontrado.');
            return;
        }

        // Verificar se há ajuste pendente para usar o número correto
        $ajustePendenteAtual = PlanAdjustment::where('tenant_id', $domain->tenant_id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->first();

        // Usar o employee_count antes do ajuste pendente, se existir
        $employeeCountReal = $ajustePendenteAtual 
            ? $ajustePendenteAtual->employee_count_before 
            : $this->employeeCount;

        if ($this->novoNumeroFuncionarios == $employeeCountReal) {
            session()->flash('error', 'O número de funcionários é o mesmo do plano atual.');
            return;
        }

        // Atualizar a propriedade para o cálculo correto
        $this->employeeCount = $employeeCountReal;

        // Buscar dados do plano atual
        $pagamento = PagByPayment::on('mysql')
            ->where('tenant_id', $domain->tenant_id)
            ->latest()
            ->first();

        if (!$pagamento) {
            session()->flash('error', 'Nenhum plano ativo encontrado.');
            return;
        }

        // Calcular ajuste proporcional
        $pagbyService = new PagbyService();
        $dataInicio = Carbon::parse($pagamento->created_at);
        $dataFim = Carbon::parse($this->proximoVencimento);

        $this->ajusteCalculado = $pagbyService->calcularAjusteProporcional(
            $this->employeeCount,
            $this->novoNumeroFuncionarios,
            $this->planoAtual,
            $dataInicio,
            $dataFim
        );
    }

    public function confirmarAjuste()
    {
        if (!$this->ajusteCalculado) {
            session()->flash('error', 'Calcule o ajuste antes de confirmar.');
            return;
        }

        $host = request()->getHost();
        $domain = DB::connection('mysql')->table('domains')->where('domain', $host)->first();
        
        if (!$domain || !$domain->tenant_id) {
            session()->flash('error', 'Tenant não encontrado.');
            return;
        }

        $tenant = Tenant::find($domain->tenant_id);
        if (!$tenant) {
            session()->flash('error', 'Tenant não encontrado.');
            return;
        }

        try {
            // 1. Se houver ajuste pendente, cancelar e reverter o employee_count
            if ($this->ajustePendente) {
                // Se o ajuste pendente tinha cobrança no Asaas, cancelar
                if ($this->ajustePendente->asaas_payment_id) {
                    $asaasService = new AsaasService();
                    $asaasService->cancelarCobranca($this->ajustePendente->asaas_payment_id);
                }
                
                // Marcar ajuste anterior como cancelado
                $this->ajustePendente->status = 'cancelled';
                $this->ajustePendente->notes = ($this->ajustePendente->notes ?? '') . ' | Cancelado e substituído por novo ajuste.';
                $this->ajustePendente->save();

                // Reverter o employee_count do tenant para o valor antes do ajuste pendente
                $tenant->employee_count = $this->ajustePendente->employee_count_before;
                $tenant->save();

                // Reverter também no último pagamento
                $pagamentoAnterior = PagByPayment::on('mysql')
                    ->where('tenant_id', $tenant->id)
                    ->latest()
                    ->first();
                if ($pagamentoAnterior) {
                    $pagamentoAnterior->employee_count = $this->ajustePendente->employee_count_before;
                    $pagamentoAnterior->save();
                }

                Log::info('Ajuste pendente cancelado e employee_count revertido', [
                    'adjustment_id' => $this->ajustePendente->id,
                    'tenant_id' => $tenant->id,
                    'employee_count_revertido_para' => $this->ajustePendente->employee_count_before,
                ]);
                
                // Atualizar a propriedade local para refletir a reversão
                $this->employeeCount = $this->ajustePendente->employee_count_before;
            }

            // 2. Criar registro do novo ajuste
            $adjustment = PlanAdjustment::create([
                'tenant_id' => $tenant->id,
                'type' => $this->ajusteCalculado['tipo'] === 'debito' ? 'debit' : 'credit',
                'amount' => $this->ajusteCalculado['ajuste'],
                'employee_count_before' => $this->employeeCount,
                'employee_count_after' => $this->novoNumeroFuncionarios,
                'plan_period' => $this->planoAtual,
                'days_remaining' => $this->ajusteCalculado['dias_restantes'],
                'percentage_remaining' => $this->ajusteCalculado['percentual_restante'],
                'status' => 'pending',
                'notes' => "Ajuste de {$this->employeeCount} para {$this->novoNumeroFuncionarios} funcionário(s)",
            ]);

            Log::info('Ajuste de plano criado', [
                'adjustment_id' => $adjustment->id,
                'tenant_id' => $tenant->id,
                'type' => $adjustment->type,
                'amount' => $adjustment->amount,
            ]);

            // 2. Se for DÉBITO (upgrade), criar cobrança no Asaas
            if ($this->ajusteCalculado['tipo'] === 'debito') {
                $asaasService = new AsaasService();
                
                // Buscar dados do contato
                $pagamento = PagByPayment::on('mysql')
                    ->where('tenant_id', $tenant->id)
                    ->latest()
                    ->first();

                $customerData = [
                    'name' => $tenant->name ?? 'Cliente',
                    'email' => $tenant->email,
                    'cpfCnpj' => $tenant->cnpj ?? '',
                    'phone' => $tenant->phone ?? '',
                ];

                $diferencaFuncionarios = $this->novoNumeroFuncionarios - $this->employeeCount;
                $diasRestantes = round($this->ajusteCalculado['dias_restantes']);
                $valorFormatado = number_format($this->ajusteCalculado['ajuste'], 2, ',', '.');

                $paymentData = [
                    'billingType' => 'UNDEFINED', // Permite PIX, boleto, cartão
                    'value' => $this->ajusteCalculado['ajuste'],
                    'dueDate' => now()->addDays(3)->format('Y-m-d'), // Vencimento em 3 dias
                    'description' => "Ajuste de plano: {$diferencaFuncionarios} funcionário(s) adicional(is); valor do ajuste: R$ {$valorFormatado}; dias restantes no plano ajustado: {$diasRestantes}",
                ];

                $result = $asaasService->criarCobranca($customerData, $paymentData);

                if ($result['success']) {
                    $adjustment->asaas_payment_id = $result['data']['id'] ?? null;
                    $adjustment->asaas_invoice_url = $result['data']['invoiceUrl'] ?? null;
                    $adjustment->save();

                    Log::info('Cobrança Asaas criada para ajuste', [
                        'adjustment_id' => $adjustment->id,
                        'asaas_payment_id' => $adjustment->asaas_payment_id,
                    ]);

                    // Atualizar tenant imediatamente (o pagamento será confirmado via webhook)
                    $tenant->employee_count = $this->novoNumeroFuncionarios;
                    $tenant->save();

                    if ($pagamento) {
                        $pagamento->employee_count = $this->novoNumeroFuncionarios;
                        $pagamento->save();
                    }

                    session()->flash('mensagem', 
                        'Plano ajustado com sucesso! Uma cobrança de R$ ' . 
                        number_format($this->ajusteCalculado['ajuste'], 2, ',', '.') . 
                        ' foi gerada. Você pode pagá-la via PIX, boleto ou cartão através do link que será enviado por email.'
                    );
                } else {
                    Log::error('Erro ao criar cobrança Asaas para ajuste', [
                        'adjustment_id' => $adjustment->id,
                        'error' => $result['message'] ?? 'Erro desconhecido',
                    ]);

                    session()->flash('error', 'Erro ao criar cobrança. Tente novamente ou contate o suporte.');
                    return;
                }
            } 
            // 3. Se for CRÉDITO (downgrade), apenas registrar
            else if ($this->ajusteCalculado['tipo'] === 'credito') {
                // Atualizar tenant imediatamente
                $tenant->employee_count = $this->novoNumeroFuncionarios;
                $tenant->save();

                $pagamento = PagByPayment::on('mysql')
                    ->where('tenant_id', $tenant->id)
                    ->latest()
                    ->first();
                
                if ($pagamento) {
                    $pagamento->employee_count = $this->novoNumeroFuncionarios;
                    $pagamento->save();
                }

                session()->flash('mensagem', 
                    'Plano ajustado com sucesso! Você receberá um crédito de R$ ' . 
                    number_format($this->ajusteCalculado['ajuste'], 2, ',', '.') . 
                    ' que será aplicado automaticamente na sua próxima renovação.'
                );
            }

            $this->mount(); // Recarrega os dados
            $this->fecharModalAjuste();

        } catch (\Exception $e) {
            Log::error('Erro ao confirmar ajuste de plano', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Erro ao processar ajuste. Tente novamente ou contate o suporte.');
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
