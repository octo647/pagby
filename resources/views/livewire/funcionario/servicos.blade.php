
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">💇‍♂️ Serviços de {{ $funcionario->name ?? Auth::user()->name }}</h1>
        @if($serviços->isEmpty())
            <div class="bg-white rounded-xl shadow p-6 text-center text-gray-500">Nenhum serviço encontrado para este funcionário.</div>
        @else
            {{-- Lista para desktop --}}
            <ul class="hidden md:block list-disc pl-5">
                @foreach($serviços as $servico)
                    <li class="mb-2 flex items-center gap-2">
                        <span class="font-medium text-gray-900">{{ $servico->service }}</span>
                        <span class="inline-block bg-blue-100 text-blue-700 rounded px-2 py-1 text-xs">{{ $servico->time }} min</span>
                    </li>
                @endforeach
            </ul>

            {{-- Cards para mobile --}}
            <div class="md:hidden space-y-4">
                @foreach($serviços as $servico)
                    <div class="bg-white rounded-xl shadow p-4 flex items-center justify-between">
                        <span class="font-medium text-gray-900">{{ $servico->service }}</span>
                        <span class="inline-block bg-blue-100 text-blue-700 rounded px-2 py-1 text-xs">{{ $servico->time }} min</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
