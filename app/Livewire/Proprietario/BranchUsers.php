<?php

namespace App\Livewire\Proprietario;

use App\Models\Branch;
use App\Models\BranchUser;
use App\Models\PlanAdjustment;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use App\Models\Role;

class BranchUsers extends Component
{
    public $branchUsers = [];
    public $branchName = null;
    public $branchId = null;
    public $selectedUser = null;
    public $editedUserIndex = null;
    public $search = '';
    public $showOnlyActive = true;
    public $showAddModal = false;
    public $employeeLimit = 1;
    public $currentEmployeeCount = 0;
    public $status = null;
    public $newEmployee = [
        'name' => '',
        'email' => '',
        'password' => '',
        'branch_id' => null,
    ];
    use WithFileUploads;
     public function openAddModal()
    {
        // Recalcula a contagem atual de funcionários ativos
        $activeEmployeeCount = User::whereHas('roles', function($q) {
            $q->where('role', 'Funcionário');
        })->where('status', 'Ativo')->count();
        
        // Verifica se o limite de funcionários foi atingido
        if ($activeEmployeeCount >= $this->employeeLimit) {
            session()->flash('error', 'Limite de funcionários atingido! Para adicionar mais funcionários, faça o ajuste do plano em <a href="/dashboard?tabelaAtiva=meu-pagby" class="font-semibold underline hover:text-red-900">Meu Pagby</a>.');
            return;
        }
        $this->showAddModal = true;
    }
    
    public function mount()
    {
        $tenant = tenant();
        $latestAdjustment = PlanAdjustment::where('tenant_id', $tenant->id)
            ->latest()
            ->first();
        if ($latestAdjustment) {
            if ($latestAdjustment->status === 'aprovado' || $latestAdjustment->status === 'RECEIVED') {
                $this->employeeLimit = $latestAdjustment->employee_count_after;
            } elseif ($latestAdjustment->status === 'pendente' || $latestAdjustment->status === 'pending') {
                $this->employeeLimit = $latestAdjustment->employee_count_before;
            } else {
                $this->employeeLimit = $tenant->employee_count ?? 1;
            }
        } else {
            $this->employeeLimit = $tenant->employee_count ?? 1;
        }
        $this->currentEmployeeCount = User::whereHas('roles', function($q) {
            $q->where('role', 'Funcionário');
        })->where('status', 'Ativo')->count();
        // Não carrega branchUsers aqui!
    }
    // Método auxiliar para buscar dados dos usuários
    private function getBranchUsersData()
    {
        
        $funcionarios = User::with('roles')->whereHas('roles', function($q) {
            $q->where('role', 'Funcionário');
        })->get();
        $branchUsersArray = [];
        foreach ($funcionarios as $user) {
            $branchUser = BranchUser::where('user_id', $user->id)->first();
            $branch = $branchUser && $branchUser->branch_id ? Branch::find($branchUser->branch_id) : null;
            $branchUsersArray[] = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'branch_id' => $branchUser->branch_id ?? null,
                'branch_name' => $branch ? $branch->branch_name : 'Sem filial',
                'user_photo' => $user->photo,
                'status' => $user->status ?? 'Ativo',
            ];
        }
        return $branchUsersArray;
    }
    
    public function selectUser($userId)
    {
        $this->selectedUser = $userId;
    }
    
    // Removidos updatedShowOnlyActive e updatedSearch
    
    public function updateBranchUser($userId)
    {
        $user = User::find($userId);
       
        if (!$user) {
            session()->flash('error', 'Usuário não encontrado no banco de dados.');
            return;
        }

        // Recarrega os dados para garantir que o array está atualizado
        if (empty($this->branchUsers)) {
            $this->branchUsers = $this->getBranchUsersData();
        }

        // Primeiro, encontre o índice correto do usuário no array
        $index = null;
        foreach ($this->branchUsers as $key => $u) {
            if (isset($u['user_id']) && $u['user_id'] == $userId) {
                $index = $key;
                break;
            }
        }

        if ($index === null) {
            // Tenta obter o índice editado como fallback
            $index = $this->editedUserIndex;
        }

        // Agora processa o upload de foto se existir
        if ($index !== null && isset($this->branchUsers[$index]['photo'])) {
            $photo = $this->branchUsers[$index]['photo'];
            $tenant = tenant();
           
            if ($tenant) {
                // Usar o ID do tenant sem prefixo "tenant"
                $tenantId = $tenant->id;
                
                // Salvar no storage do tenant: storage/{tenantId}/app/public/profile-photos
                $path = $photo->store('profile-photos', 'public');
                $user->photo = "profile-photos/" . basename($path);
            } else {
                $path = $photo->store('profile-photos', 'public');
                $user->photo = $path;
            }
        }

        $user->save();

        $branchId = ($index !== null && isset($this->branchUsers[$index]['branch_id'])) 
            ? $this->branchUsers[$index]['branch_id'] 
            : null;
      

        if (empty($branchId)) {
            session()->flash('error', 'Selecione uma filial antes de salvar.');
                    // NÃO inicialize tenancy manualmente aqui! O contexto já está garantido pelo middleware nas rotas tenant
            return;
        }

        $branchUser = BranchUser::where('user_id', $userId)->first();
        if ($branchUser) {
            $branchUser->branch_id = $branchId;
            $branchUser->save();
        } else {
            BranchUser::create([
                'user_id' => $userId,
                'branch_id' => $branchId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->editedUserIndex = null;
        session()->flash('message', 'Funcionário atualizado com sucesso.');
        $this->mount(); // Refresh the branch users list
        $this->selectedUser = null; // Reset selected user after update
        $this->branchName = null; // Reset branch name after update
        $this->branchId = null; // Reset branch ID after update
    }


    public function deleteBranchUser($userId)
    {
        $branchUser = BranchUser::where('user_id', $userId)->first();
        if ($branchUser) {
            $branchUser->delete();
            $this->branchUsers = array_filter($this->branchUsers, function ($item) use ($userId) {
                return $item['user_id'] !== $userId;
            });
            // Reindex array to avoid gaps in keys
            $this->branchUsers = array_values($this->branchUsers);
        }
    }
    public function addBranchUser($userId, $branchId)
    {
        $branchUser = new BranchUser();
        $branchUser->user_id = $userId;
        $branchUser->branch_id = $branchId;
        $branchUser->save();

        // Refresh the branch users list
        $this->mount();
    }
    public function editUser($index)
{
    $this->editedUserIndex = $index;
}

    public function cancelEdit()
    {
        $this->editedUserIndex = null;
    }

    public function toggleUserStatus($userId)
    {
        $user = User::find($userId);
        if ($user) {
            // Se está tentando ativar (status atual é Inativo), verifica o limite
            if ($user->status === 'Inativo') {
                // Recalcula o número de funcionários ativos
                $activeCount = User::whereHas('roles', function($q) {
                    $q->where('role', 'Funcionário');
                })->where('status', 'Ativo')->count();
                
                if ($activeCount >= $this->employeeLimit) {
                    session()->flash('error', 'Limite de funcionários atingido! Para ativar mais funcionários, faça o ajuste do plano em <a href="/dashboard?tabelaAtiva=meu-pagby" class="font-semibold underline hover:text-red-900">Meu Pagby</a>.');
                    return;
                }
            }
            
            $user->status = ($user->status === 'Ativo') ? 'Inativo' : 'Ativo';
            $user->save();
            
            session()->flash('message', 'Status do funcionário alterado com sucesso.');
            $this->mount(); // Refresh list
        }
    }

    public function render()
    {
        // Sempre busca dados frescos do banco
        $branchUsers = $this->getBranchUsersData();
        $filteredUsers = collect($branchUsers);
      
     
  
        if (!empty($this->search)) {
            $filteredUsers = $filteredUsers->filter(function ($user) {
                return stripos($user['user_name'], $this->search) !== false ||
                    stripos($user['user_email'], $this->search) !== false ||
                    stripos($user['branch_name'] ?? '', $this->search) !== false ||
                    stripos($user['status'] ?? 'Ativo', $this->search) !== false;
            });
            
            
        }
        if ($this->showOnlyActive) {
            $filteredUsers = $filteredUsers->filter(function ($user) {
                return isset($user['status']) && $user['status'] === 'Ativo';
            });
        }
       
        return view('livewire.proprietario.branch-users', [
            'branchUsersList' => $filteredUsers->values()->all(),
            'branches' => Branch::all(),
            'employeeLimit' => $this->employeeLimit,
            'currentEmployeeCount' => $this->currentEmployeeCount,
        ]);
    }
   

    public function closeAddModal()
    {
        $this->showAddModal = false;
        $this->newEmployee = [
            'name' => '',
            'email' => '',
            'password' => '',
            'branch_id' => null,
        ];
    }

    public function addEmployee()
    {
        // Impede adicionar se limite atingido
        if ($this->currentEmployeeCount >= $this->employeeLimit) {
            return redirect()->route('meu-pagby');
        }

        $this->validate([
            'newEmployee.name' => 'required|string|max:255',
            'newEmployee.email' => 'required|email|unique:users,email',
            'newEmployee.password' => 'required|min:6',
            'newEmployee.branch_id' => 'nullable|exists:branches,id',
        ]);

        $user = User::create([
            'name' => $this->newEmployee['name'],
            'email' => $this->newEmployee['email'],
            'password' => bcrypt($this->newEmployee['password']),
            'status' => 'Ativo',
        ]);

        $employeeRole = \App\Models\Role::where('role', 'Funcionário')->first();
        if ($employeeRole) {
            $user->roles()->attach($employeeRole->id);
        }

        if ($this->newEmployee['branch_id']) {
            BranchUser::create([
                'user_id' => $user->id,
                'branch_id' => $this->newEmployee['branch_id'],
            ]);
        }
        session()->flash('message', 'Funcionário adicionado com sucesso!');
        $this->closeAddModal();
        $this->mount();
    }

    public function saveUserEdits()
    {
        $user = User::find($this->editingUserId);
        if ($user) {
            // Atualiza os dados do usuário
            $user->name = $this->editingName;
            $user->email = $this->editingEmail;
            $user->status = $this->editingStatus;
            $user->save();

            // Atualiza a função do usuário
            $role = Role::where('role', $this->editingRole)->first();
            if ($role) {
                // Verifica se o usuário já possui a função selecionada
                if ($user->roles->contains($role->id)) {
                    // Se já possuir, não faz nada e retorna
                    $this->editingUserId = null;
                    return;
                } else {
                    // Se a função for 'Funcionário' e o limite for atingido, não permite a alteração
                    if ($this->editingRole === 'Funcionário') {
                        $currentEmployeeCount = DB::table('branch_user')->count();
                        $tenant = tenancy()->tenant;
                        $employeeLimit = $tenant->employee_count ?? 1;

                        if ($currentEmployeeCount >= $employeeLimit) {
                            session()->flash('error', 'Limite de funcionários atingido. Não é possível atribuir a função de Funcionário.');
                            $this->editingUserId = null;
                            return;
                        }
                    }
                }

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
}

