<?php

namespace App\Livewire;

use Livewire\Component;

class CreatePost extends Component
{
    public $bookingUrl;
    public $whatsappText;
    public $facebookText;
    public $instagramText;
    public $twitterText;

    public function mount()
    {
        // Gera o URL de agendamento do tenant
        $tenant = tenant();
        $subdomain = $tenant->id ?? $tenant->getTenantKey();
        $domainSuffix = config('app.tenant_domain_suffix', '.pagby.com.br');
        // Remove ponto inicial se já tiver, para evitar duplicação
        $domainSuffix = ltrim($domainSuffix, '.');
        $this->bookingUrl = "https://{$subdomain}.{$domainSuffix}/agendar";
        
        // Textos pré-formatados para redes sociais
        $tenantName = $tenant->name ?? 'Nosso Salão';
        
        $this->whatsappText = urlencode(
            "🌟 Agende seu horário no {$tenantName}!\n\n" .
            "📅 Escolha o profissional, serviço e horário que preferir!\n" .
            "👉 {$this->bookingUrl}\n\n" .
            "#Beleza #Agendamento #BeautyTime"
        );
        
        $this->facebookText = urlencode(
            "Agende seu horário conosco! 💇‍♀️✨\n\n" .
            "Escolha seu profissional e horário preferido - 100% online!\n" .
            "{$this->bookingUrl}"
        );
        
        $this->instagramText = urlencode(
            "✨ Link de agendamento na bio! ✨\n\n" .
            "📱 Escolha seu profissional e horário: {$this->bookingUrl}\n\n" .
            "#Beauty #Hair #Nails #Agendamento"
        );
        
        $this->twitterText = urlencode(
            "📅 Agende seu horário - escolha profissional, serviço e data!\n{$this->bookingUrl}"
        );
    }

    public function copyToClipboard()
    {
        $this->dispatch('urlCopied');
    }

    public function render()
    {
        return view('livewire.create-post');
    }
}
