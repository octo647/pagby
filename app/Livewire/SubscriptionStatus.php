<?php

namespace App\Livewire;

use Livewire\Component;

class SubscriptionStatus extends Component
{
    public $tenant;
    public $daysRemaining;
    public $statusColor;
    public $statusMessage;

    public function mount()
    {
        $this->tenant = tenant();
        $this->calculateStatus();
    }

    public function calculateStatus()
    {
        if (!$this->tenant) {
            return;
        }

        if ($this->tenant->isInTrial()) {
            $this->daysRemaining = $this->tenant->trial_ends_at->diffInDays(now());
            $this->statusColor = $this->daysRemaining <= 7 ? 'text-red-600 bg-red-100' : 'text-blue-600 bg-blue-100';
            $this->statusMessage = "Período de teste - {$this->daysRemaining} dias restantes";
        } elseif ($this->tenant->hasActiveSubscription()) {
            $this->daysRemaining = $this->tenant->subscription_ends_at->diffInDays(now());
            $this->statusColor = $this->daysRemaining <= 7 ? 'text-orange-600 bg-orange-100' : 'text-green-600 bg-green-100';
            $this->statusMessage = "Plano {$this->tenant->current_plan} - {$this->daysRemaining} dias restantes";
        } elseif ($this->tenant->isTrialExpired() || $this->tenant->isSubscriptionExpired()) {
            $this->statusColor = 'text-red-600 bg-red-100';
            $this->statusMessage = 'Assinatura expirada - Escolha um plano';
        } else {
            $this->statusColor = 'text-gray-600 bg-gray-100';
            $this->statusMessage = 'Status indefinido';
        }
    }

    public function render()
    {
        return view('livewire.subscription-status');
    }
}
