<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Subscription;

class UserObserver
{
    /**
     * Handle the User "deleting" event.
     * Remove todas as assinaturas associadas ao usuário antes de deletá-lo
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleting(User $user)
    {
        // Deletar todas as subscriptions (tabela obsoleta do tenant)
        $user->subscriptions()->delete();
        
        // Nota: Os pagamentos em tenants_plans_payments (banco central) 
        // são mantidos para histórico financeiro, mas poderiam ser marcados como cancelados
        // se necessário. Por enquanto, apenas removemos as subscriptions do tenant.
    }
}
