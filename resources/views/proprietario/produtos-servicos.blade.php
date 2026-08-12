@extends('layouts.proprietario')

@section('title', 'Gerenciar Produtos Recomendados')

@section('content')
<div class="container mx-auto px-4 py-8">
    @livewire('proprietario.gerenciar-produtos-servicos', [
        'serviceId' => $serviceId,
        'branchId' => auth()->user()->branches->first()?->id
    ])
</div>
@endsection
