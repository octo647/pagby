<?php
namespace App\Livewire\Proprietario;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;

use App\Models\PagByPayment;
use App\Services\AsaasService;
use App\Services\PagbyService;

class PlanoPagbyModal extends Component
{
    public $employeeCount;
    public $planoAtual;
    public $statusPagamento;
    public $proximoVencimento;
    public $selectedFuncionarios = 1;
    public $selectedPeriodicidade = 'mensal';
    public $periodicidades = [
        'mensal' => ['label' => 'Mensal', 'meses' => 1],
        'trimestral' => ['label' => 'Trimestral', 'meses' => 3],
        'semestral' => ['label' => 'Semestral', 'meses' => 6],
        'anual' => ['label' => 'Anual', 'meses' => 12],
    ];
    public $precoBase;
    public $desconto = [
        'mensal' => 0,
        'trimestral' => 0.20, // 20% desconto
        'semestral' => 0.30, // 30% desconto
        'anual' => 0.40, // 40% desconto
    ];

    public function mount()
    {
        $tenant = tenant();
        $this->employeeCount = $tenant->employee_count ?? 1;
        $this->planoAtual = $tenant->plan ?? null;
        $this->statusPagamento = $tenant->subscription_status ?? null;
        $this->proximoVencimento = $tenant->subscription_ends_at ?? null;
        $this->precoBase = config('pricing.base_price_per_employee', 60.00);
        $this->selectedFuncionarios = $this->employeeCount;
    }

    // Nenhum listener necessário, Livewire já faz binding automático

    public function atualizarFuncionarios($qtd)
    {
        $this->selectedFuncionarios = max(1, (int)$qtd);
    }

    public function atualizarPeriodicidade($periodicidade)
    {
        if (array_key_exists($periodicidade, $this->periodicidades)) {
            $this->selectedPeriodicidade = $periodicidade;
        }
    }

    public function getValorTotalProperty()
    {
        $meses = $this->periodicidades[$this->selectedPeriodicidade]['meses'];
        $n = $this->selectedFuncionarios;
        $valor1 = $this->precoBase;
        // Cada funcionário adicional aumenta 30% do valor base
        $valorFuncionarios = $valor1 + ($n > 1 ? ($n - 1) * ($valor1 * 0.3) : 0);
        $valorSemDesconto = $valorFuncionarios * $meses;
        $valorComDesconto = $valorSemDesconto * (1 - ($this->desconto[$this->selectedPeriodicidade] ?? 0));
        return $valorComDesconto;
    }

    public function getValorMensalProperty()
    {
        $meses = $this->periodicidades[$this->selectedPeriodicidade]['meses'];
        return $this->valorTotal / $meses;
    }

    public function assinar()
    {
        $user = Auth::user();
        $tenant = tenant();
        $service = new AsaasService();
        $pagbyService = new PagbyService();

        $customerData = [
            'name' => $tenant->name,
            'email' => $user->email,
            'cpfCnpj' => $user->cpf ?? $tenant->cnpj,
            'phone' => $user->phone ?? $tenant->phone,
        ];
        $valor = $pagbyService->calcularValorPlano($this->selectedFuncionarios, $this->selectedPeriodicidade);
        $cycles = [
            'mensal' => 'MONTHLY',
            'trimestral' => 'QUARTERLY',
            'semestral' => 'SEMIANNUALLY',
            'anual' => 'YEARLY',
        ];
        $cycle = $cycles[$this->selectedPeriodicidade] ?? 'MONTHLY';
        $subscriptionData = [
            'cycle' => $cycle,
            'value' => $valor,
            'billingType' => 'UNDEFINED',
            'description' => 'Assinatura PagBy - ' . ucfirst($this->selectedPeriodicidade) . ' (' . $this->selectedFuncionarios . ' funcionário(s))',
            'nextDueDate' => now()->format('Y-m-d'),
            'externalReference' => 'pagby-tenant-' . $tenant->id,
        ];
        $asaasResult = $service->criarAssinatura($customerData, $subscriptionData, null);
        if ($asaasResult['success'] && !empty($asaasResult['data']['id'])) {
            $invoiceUrl = $asaasResult['data']['invoiceUrl'] ?? null;
            $desc = 'Assinatura Asaas: ' . ($asaasResult['data']['description'] ?? '');
            if ($invoiceUrl) {
                $desc .= ' ' . $invoiceUrl;
            }
            // Salvar PagByPayment (central)
            $payment = \App\Models\PagByPayment::on('mysql')->create([
                'tenant_id' => $tenant->id,
                'contact_id' => null,
                'mp_payment_id' => null,
                'external_id' => $asaasResult['data']['id'] ?? null,
                'asaas_payment_id' => null,
                'asaas_subscription_id' => $asaasResult['data']['id'] ?? null,
                'amount' => $valor,
                'status' => 'pending',
                'employee_count' => $this->selectedFuncionarios,
                'type' => 'subscription',
                'plan' => $this->selectedPeriodicidade,
                'description' => $desc
            ]);
            // Redirecionar para tela de espera do pagamento
            return $this->redirectRoute('pagby-subscription.wait', ['paymentId' => $payment->id]);
        }
        session()->flash('error', 'Não foi possível gerar a assinatura. Tente novamente ou contate o suporte.');
        return;
    }

    public function render()
    {
        return view('livewire.proprietario.plano-pagby-modal');
    }
}
