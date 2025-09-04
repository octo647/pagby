@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto text-center">
        <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-8 rounded-lg">
            <svg class="mx-auto h-16 w-16 text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            
            <h1 class="text-3xl font-bold mb-4">Plano Ativado com Sucesso!</h1>
            
            <div class="bg-white rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-2">Detalhes da Assinatura</h2>
                <p><strong>Plano:</strong> {{ $tenant->current_plan }}</p>
                <p><strong>Status:</strong> Ativo</p>
                <p><strong>Válido até:</strong> {{ $tenant->subscription_ends_at->format('d/m/Y H:i') }}</p>
            </div>
            
            <p class="text-lg mb-6">
                Seu plano {{ $tenant->current_plan }} está ativo e você pode aproveitar todas as funcionalidades da plataforma.
            </p>
            
            <div class="space-y-3">
                <a href="{{ route('tenant.dashboard') }}" 
                   class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition duration-200">
                    Ir para o Dashboard
                </a>
                
                <br>
                
                <a href="{{ route('tenant.subscription.plans') }}" 
                   class="inline-block text-green-600 hover:text-green-700 font-semibold">
                    Gerenciar Assinatura
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
