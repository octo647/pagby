<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class TenantResetPassword extends ResetPassword
{
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('Redefinição de senha')
            ->greeting('Olá!')
            ->line('Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para sua conta.')
            ->action('Redefinir senha', $url)
            ->line('Se você não solicitou uma redefinição de senha, nenhuma ação é necessária.')
            ->line('Se você estiver com problemas para clicar no botão "Redefinir senha", copie e cole a URL abaixo em seu navegador:')
            ->line($url)
            ->salutation("Atenciosamente,\nPagBy");
    }

    public function toMail($notifiable)
    {
        $tenant = function_exists('tenant') ? tenant() : null;
        $host = request()->getHost();
        $token = $this->token;
        $email = urlencode($notifiable->getEmailForPasswordReset());

        // Detecta se está em subdomínio ou path
        if ($tenant && (str_starts_with($host, $tenant->id . '.') || str_contains($host, $tenant->id . '.'))) {
            // Subdomínio
            $url = url("/reset-password/$token?email=$email");
        } elseif ($tenant) {
            // Path
            $url = url("/$tenant->id/reset-password/$token?email=$email");
        } else {
            // Central
            $url = url("/reset-password/$token?email=$email");
        }

        return $this->buildMailMessage($url);
    }
    
}
