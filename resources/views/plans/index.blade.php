{{-- resources/views/plans/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Planos de Assinatura') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="row">
                @foreach($plans as $plan)
                    <div class="col-md-4 py-2">
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-blue-500 text-white">
                                <h4 class="text-center">Plano {{ $plan->name }}</h4>
                            </div>
                            
                            <div class="card-body">
                                <p><strong>Mensalidade:</strong> R$ {{ number_format($plan->price, 2, ',', '.') }}</p>                          
                                <p><strong>Serviços inclusos:</strong></p>
                                <ul class="list-disc pl-5">
                                    @foreach($plan->services as $serviceId)
                                        <li>{{ \App\Models\Service::find($serviceId)->service ?? 'Serviço removido' }}</li>
                                    @endforeach
                                </ul>
                                <p><strong>Serviços adicionais:</strong></p>
                                <ul class="list-disc pl-5">
                                    @foreach($plan->additional_services as $serviceName => $info)
                                        @php
                                            $service = \App\Models\Service::find($info['id']);
                                        @endphp
                                        <li>
                                            {{ $service->service ?? 'Serviço removido' }} 
                                            - Desconto: {{ $info['desconto'] ?? '-' }}
                                        </li>
                                    @endforeach
</ul>
                                <form method="POST" action="{{--{{ route('subscriptions.store') }}--}}">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:ring-red-300 font-medium
    rounded-lg text-base inline-flex items-center px-2 py-1.5 text-center mr-2">Assinar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>