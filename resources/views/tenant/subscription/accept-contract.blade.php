@extends('layouts.app')
@section('content')
<div class="container mx-auto max-w-2xl py-10">
    <h1 class="text-3xl font-bold mb-6 text-center">Aceite do Contrato de Prestação de Serviços</h1>
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        @include('tenant.subscription.contract-template', ['tenant' => tenant()])
    </div>
    <form method="POST" action="{{ route('tenant.contract.accept') }}">
        @csrf
        <div class="flex items-center justify-center mt-6">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg text-lg shadow">Aceitar e Prosseguir</button>
        </div>
    </form>
</div>
@endsection
