<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    <div class="p-6 bg-white border-b border-gray-200">
        
        @if($serviços->isEmpty())
            <p class="text-gray-500">Nenhum serviço encontrado para este funcionário.</p>
        @else
            <ul class="list-disc pl-5">
                @foreach($serviços as $servico)
                    <li class="mb-2">{{ $servico->service }} - {{ $servico->time }} min</li>
                @endforeach
            </ul>
        @endif
</div>
