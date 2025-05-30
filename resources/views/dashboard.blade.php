<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>


    </x-slot>


    <div class="py-1">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden bg-white shadow-xl sm:rounded-lg">

                
                @include('includes.messages')
                @if(session('chooseone'))
                {{session('chooseone')}}
                @endif

                @can('Admin')
                <br>
                <h1>Contatos</h1>
                @livewire('list-contacts')
                <br><br>
                <h1>Usuários</h1>
               {{-- @livewire('role-user')--}}


                @endcan


                @can('Proprietário')

                <h1>Usuários do Salão {{tenant('id')}}:</h1>

                @livewire('salon_users')
                <br>

                <h1>Neste painel você encontrará o quadro de horários dos funcionários </h1>
                <br>
                @livewire('salon-times')
                <br><br>
                <h1>Intervalos de trabalho dos funcionários </h1>
                <br>
                @livewire('intervals')
                <br><br>
                <h1>Serviços do Salão {{tenant('id')}}:</h1>
                @livewire('services')
                <br><br>

                <h1>Tabela Funcionários x Serviços do </h1>
                @livewire('employee-service')


                @endcan


                @can('Funcionário')

                @endcan
                @can('Cliente')
                @livewire('appointments')
                @endcan
               
               {{-- <x-welcome/> --}}

            </div>
        </div>
    </div>

</x-app-layout>
