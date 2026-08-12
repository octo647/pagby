{{-- resources/views/plans/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Planos de Assinatura') }}
        </h2>
    </x-slot>

    <div class="py-6">
    {{-- Adicione logo após o <div class="py-6"> --}}
    
    @can('Proprietário')
        @include('includes.messages')
    <div class="mb-4 flex justify-end">
        <a href="{{ route('plans.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            + Novo Plano
        </a>
    </div>
    @endcan
    
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="row">
                @foreach($plans as $plan)
                    <div class="col-md-4 py-2">
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-blue-500 text-white">
                                <h4 class="text-center">Plano {{ $plan->name }}</h4>
                            </div>
                            
                            <div class="card-body px-4 py-3">
                                <p><strong>Mensalidade:</strong> R$ {{ number_format($plan->price, 2, ',', '.') }}</p>                          
                                <p><strong>Serviços inclusos:</strong></p>
                                <ul class="list-disc pl-5">
                                @if(is_array($plan->services))
                                    @foreach($plan->services as $serviceId)
                                        <li>{{ \App\Models\Service::find($serviceId)->service ?? 'Serviço removido' }}</li>
                                    @endforeach
                                @else
                                    <li> Nenhum serviço incluído</li>
                                @endif
                                </ul>
                                <p><strong>Serviços adicionais:</strong></p>
                                <ul class="list-disc pl-5">
                                @if(is_array($plan->additional_services) && count($plan->additional_services) > 0)
                                    @foreach($plan->additional_services as $serviceName => $info)
                                        @php
                                            $service = \App\Models\Service::find($info['id']);
                                        @endphp
                                        <li>
                                            {{ $service->service ?? 'Serviço removido' }} 
                                            - Desconto: {{ $info['desconto'] ?? '-' }}
                                        </li>
                                    @endforeach
                                @else
                                    <li> Nenhum serviço adicional incluído</li>
                                @endif
</ul>
                                <form method="POST" action="{{ route('subscriptions.store') }}">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    <input type="hidden" name="branch_id" value="{{ $plan->branch_id }}">
                                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                                    @can('Cliente')
                                    <div class ="mb-4 flex justify-center py-4">                               
                                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:ring-red-300 font-medium
                                     rounded-lg text-base inline-flex items-center px-2 py-1.5 text-center mr-2">Assinar</button>
                                    </div>
                                    @endcan
                                </form>
                                @can('Proprietário')
                                <div class="mb-4 py-4 flex justify-center">
                                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:ring-red-300 font-medium
                                     rounded-lg text-base inline-flex items-center px-2 py-1.5 text-center mr-2"><a href="{{ route('plans.edit', $plan) }}" class="btn btn-warning">Editar</a></button>
                                    <form action="{{ route('plans.destroy', $plan) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium
                                     rounded-lg text-base inline-flex items-center px-2 py-1.5 text-center mr-2">Excluir</button>
                                </form>
                                </div>
                                @endcan
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</x-app-layout>