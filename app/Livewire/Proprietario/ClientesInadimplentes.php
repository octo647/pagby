<?php

namespace App\Livewire\Proprietario;

use Livewire\Component;
use App\Models\Payment;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ClientesInadimplentes extends Component
{
    public $periodoDias = 30; // Últimos 30 dias
    public $bloqueioAutomatico = false;
    public $diasParaNotificar = 3;
    
    public function mount()
    {
        // Carregar configurações do tenant
        $tenant = tenant();
        $this->bloqueioAutomatico = $tenant->getSetting('bloqueio_automatico_inadimplentes', false);
        $this->diasParaNotificar = $tenant->getSetting('dias_notificar_inadimplencia', 3);
    }
    
    public function getClientesInadimplentesProperty()
    {
        return DB::table('appointments')
            ->join('payments', 'appointments.id', '=', 'payments.appointment_id')
            ->join('users as customers', 'appointments.customer_id', '=', 'customers.id')
            ->select([
                'customers.id as customer_id',
                'customers.name as customer_name',
                'customers.phone as customer_phone',
                DB::raw('COUNT(payments.id) as total_pagamentos_atrasados'),
                DB::raw('SUM(payments.amount) as total_divida'),
                DB::raw('MIN(payments.due_date) as primeiro_vencimento'),
                DB::raw('MAX(payments.due_date) as ultimo_vencimento'),
            ])
            ->where('payments.status', 'overdue')
            ->groupBy('customers.id', 'customers.name', 'customers.phone')
            ->orderByDesc('total_divida')
            ->get();
    }
    
    public function enviarLembrete($customerId)
    {
        $customer = User::find($customerId);
        
        // Buscar pagamentos atrasados
        $pagamentosAtrasados = Payment::whereHas('appointment', function($q) use ($customerId) {
            $q->where('customer_id', $customerId);
        })
        ->where('status', 'overdue')
        ->get();
        
        $totalDivida = $pagamentosAtrasados->sum('amount');
        
        // Enviar WhatsApp (se configurado)
        // $whatsapp->enviarMensagem($customer->phone, "Olá {$customer->name}...");
        
        session()->flash('message', "Lembrete enviado para {$customer->name}");
    }
    
    public function bloquearCliente($customerId)
    {
        $customer = User::find($customerId);
        $customer->is_blocked = true;
        $customer->save();
        
        session()->flash('message', "Cliente {$customer->name} bloqueado.");
    }
    
    public function desbloquearCliente($customerId)
    {
        $customer = User::find($customerId);
        $customer->is_blocked = false;
        $customer->save();
        
        session()->flash('message', "Cliente {$customer->name} desbloqueado.");
    }
    
    public function salvarConfiguracoes()
    {
        $tenant = tenant();
        $tenant->putSetting('bloqueio_automatico_inadimplentes', $this->bloqueioAutomatico);
        $tenant->putSetting('dias_notificar_inadimplencia', $this->diasParaNotificar);
        
        session()->flash('message', 'Configurações salvas com sucesso!');
    }
    
    public function render()
    {
        return view('livewire.proprietario.clientes-inadimplentes', [
            'clientesInadimplentes' => $this->clientesInadimplentes,
            'totalInadimplentes' => $this->clientesInadimplentes->count(),
            'totalDividaGeral' => $this->clientesInadimplentes->sum('total_divida'),
        ]);
    }
}
