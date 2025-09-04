<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class SalonUsers extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $editingUserId = null;
    public $editingRole = '';
    public $editingStatus = '';
    public $showModal = false;
    public $selectedUser = null;
    public $userDetails = [];

    public function showUserDetails($userId)
    {
    $user = User::with('roles')->find($userId);

    // Exemplo de busca dos dados extras:
    $phone = $user->phone ?? '';
    $agendamentos = $user->clientAppointments()->count();
    $ultimoAgendamento = $user->clientAppointments()->latest('appointment_date')->first();

    $temAgendamento = $user->clientAppointments()->where('appointment_date', '>=', now())->exists();
    $currentSubscription = $user->currentSubscription();

    $this->userDetails = [
        'nome' => $user->name,
        'email' => $user->email,
        'phone' => $phone,
        'funcao' => $user->roles->first()->role ?? '',
        'agendamentos' => $agendamentos,
        'ultimo_agendamento' => $ultimoAgendamento ? $ultimoAgendamento->appointment_date->format('d/m/Y H:i') : 'Nunca',
        'tem_agendamento' => $temAgendamento ? 'Sim' : 'Não',
        'plano' => $currentSubscription ? $currentSubscription->plan->name : 'Nenhum plano ativo',
        'plano_inicio' => $currentSubscription ? $currentSubscription->start_date->format('d/m/Y') : 'N/A',
        'plano_fim' => $currentSubscription ? $currentSubscription->end_date->format('d/m/Y') : 'N/A',
      
    ];

    $this->showModal = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
    }

    // Resetar a página ao buscar
    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function editUser($userId)
    {
        $this->editingUserId = $userId;
        $user = User::with('roles')->find($userId);
        $this->editingRole = $user->roles->first()->role ?? '';
        $this->editingStatus = $user->status;
        
    }

    public function saveUser()
    {
        $user = User::find($this->editingUserId);
        if ($user) {
            // Atualiza status
            $user->status = $this->editingStatus;
           
            $user->save();

            // Atualiza função (role)
            $role = Role::where('role', $this->editingRole)->first();
            if ($role) {
                // Remove todas as funções e adiciona a nova
                $user->roles()->sync([$role->id]);
            }
            // Se a função for 'Funcionário', adiciona o usuário na tabela branch_users
            if ($this->editingRole === 'Funcionário') {
                DB::table('branch_user')->updateOrInsert(
                    ['user_id' => $user->id],
                    []                    
                );
            } else {
                // Remove o usuário da tabela branch_users se não for 'Funcionário'
                DB::table('branch_user')->where('user_id', $user->id)->delete();
            }
        }
        $this->editingUserId = null;
        session()->flash('message', 'Usuário atualizado com sucesso!');
    }

    public function cancelEdit()
    {
        $this->editingUserId = null;
    }

    public function render()
    {
        $salon_users = User::query()
            ->when($this->searchTerm, function($query) {
                $query->where('name', 'like', '%'.$this->searchTerm.'%');
            })
            ->orderBy('name', 'asc')
            ->with('roles')
            ->paginate(10);
          

        $roles = Role::pluck('role');

        return view('livewire.salon-users', [
            'salon_users' => $salon_users,
            'roles' => $roles,
        ]);
    }
}