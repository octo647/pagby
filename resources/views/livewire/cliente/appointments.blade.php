<div class="">
    @if(count($agendamentos)>0)
    <div>
        <h1 class="text-2xl font-semibold">Meus Agendamentos</h1>
    </div>
    <div class="text-sm text-gray-500">
        Você pode cancelar um agendamento clicando no botão "Cancelar".
    </div>

    
        @foreach($agendamentos as $index=>$schedule)
        <div class="card bg-white border-2 p-4 w-full max-w-2xl mx-auto">
            <div class="text-sm ">Filial: <span class="text-sm text-gray-500"> {{$schedule['branch']}}</span></div>
            <div class="text-sm">Profissional: <span class="text-sm text-gray-500"> {{$schedule['professional']}}</span></div>
            <div class="text-sm">Serviço(s): <span class="text-sm text-gray-500"> {{$schedule['services']}}</span></div>
            <div class="text-sm">Preço: <span class="text-sm text-gray-500"> {{$schedule['total']}}</span></div>
            <div class="text-sm">Dia: <span class="text-sm text-gray-500"> {{$schedule['date']}}</span></div>
            <div class="text-sm">Hora: <span class="text-sm text-gray-500"> {{$schedule['start_time']}}</span></div>
            <div class="text-sm">
                <a wire:click="deleteSchedule({{$schedule['id']}})">
                    <button class="bg-red-300 p-1 rounded text-xs">Cancelar</button>
                </a>
            </div> 
                 
        </div>
        @endforeach
        @if (session('success'))
                <div class="card bg-blue-300 border-2 p-4 w-full max-w-2xl mx-auto alert alert-success">
                {{ session('success') }}
                </div>
        @endif    
    
    @endif

    <div class="text-center  p-4 w-full max-w-2xl mx-auto">
        
        <a href="/agendamento">
          <button class="bg-black p-1 rounded text-xl text-white">Novo agendamento<button>
      </a>
      </div>
</div>
    



