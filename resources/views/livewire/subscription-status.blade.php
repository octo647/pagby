<div class="bg-white overflow-hidden shadow-sm rounded-lg">
    <div class="p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="h-8 w-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Status da Assinatura</h3>
                    <div class="flex items-center mt-1">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                            {{ $statusMessage }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="text-right">
                @if($tenant && ($tenant->isInTrial() || $tenant->hasActiveSubscription()))
                    <div class="text-2xl font-bold text-gray-900">{{ $daysRemaining }}</div>
                    <div class="text-sm text-gray-500">dias restantes</div>
                @endif
            </div>
        </div>

        @if($tenant)
            <div class="mt-4 pt-4 border-t border-gray-200">
                @if($tenant->isInTrial())
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">
                                Período de teste válido até: <strong>{{ $tenant->trial_ends_at->format('d/m/Y H:i') }}</strong>
                            </p>
                            @if($daysRemaining <= 7)
                                <p class="text-sm text-red-600 mt-1">
                                    ⚠️ Seu período de teste está acabando! Escolha um plano para continuar.
                                </p>
                            @endif
                        </div>
                        <a href="{{ route('tenant.subscription.plans') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition duration-200">
                            Ver Planos
                        </a>
                    </div>
                @elseif($tenant->hasActiveSubscription())
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">
                                Plano <strong>{{ $tenant->current_plan }}</strong> válido até: <strong>{{ $tenant->subscription_ends_at->format('d/m/Y H:i') }}</strong>
                            </p>
                            @if($daysRemaining <= 7)
                                <p class="text-sm text-orange-600 mt-1">
                                    ⚠️ Sua assinatura está prestes a expirar. Renove para continuar usando a plataforma.
                                </p>
                            @endif
                        </div>
                        <a href="{{ route('tenant.subscription.plans') }}" 
                           class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition duration-200">
                            Gerenciar
                        </a>
                    </div>
                @else
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-red-600">
                                Sua assinatura expirou. Escolha um plano para reativar seu acesso.
                            </p>
                        </div>
                        <a href="{{ route('tenant.subscription.plans') }}" 
                           class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition duration-200">
                            Escolher Plano
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
