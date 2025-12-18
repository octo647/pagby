<?php

namespace App\Livewire\Proprietario;

use App\Models\Branch;
use App\Models\BranchUser;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

class BranchUsers extends Component
{
    public $branchUsers = [];
    public $branchName = null;
    public $branchId = null;
    public $selectedUser = null;
    public $editedUserIndex = null;
    use WithFileUploads;
    
    public function mount()
    {
        $branchUsers = BranchUser::all();  
        $branchUsersArray = [];  

        foreach ($branchUsers as $branchUser) {
            $user_id = $branchUser->user_id; 
            $user = User::find($user_id);
            if (!$user) {
                continue; // Skip if user not found
            }
            // Filtra apenas usuários que têm o papel "Funcionário"
        if (!$user->roles()->where('role', 'Funcionário')->exists()) {
            continue;
        }
            // Assuming you have a relationship defined in BranchUser model
            $branch_id = $branchUser->branch_id; // Assuming you have a relationship defined in BranchUser model
            
            if ($user_id && $branch_id) {
                $branchUsersArray[] = [
                    'user_id' => $user_id,
                    'user_name' => User::where('id', $user_id)->first()->name,
                    'user_email' => User::where('id', $user_id)->first()->email,
                    'branch_id' => $branch_id ?? '',
                    'branch_name' => Branch::where('id', $branch_id)->first()->branch_name,
                    'user_photo' => User::where('id', $user_id)->first()->photo,
                ];
            } 
            elseif($user_id) {
                $branchUsersArray[] = [
                    'user_id' => $user_id,
                    'user_name' => User::where('id', $user_id)->first()->name,
                    'user_email' => User::where('id', $user_id)->first()->email,
                    'branch_id' => null,
                    'branch_name' => null,
                    'user_photo' => User::where('id', $user_id)->first()->photo,
                ];
            }
            

    }
    $this->branchUsers = $branchUsersArray;
    }
    public function selectUser($userId)
    {
        $this->selectedUser = $userId;
    }
    
    public function updateBranchUser($userId)
    {
    $user = User::find($userId);
    if (isset($this->branchUsers[$this->editedUserIndex]['photo'])) {
        $photo = $this->branchUsers[$this->editedUserIndex]['photo'];
        $tenantId = tenant('id') ?? (auth()->user()->tenant_id ?? null);
       
        if ($tenantId) {
            $path = $photo->store('profile-photos', 'public');
            $user->photo = "profile-photos/" . basename($path);
        } else {
            // fallback para padrão antigo se não houver tenantId
            $path = $photo->store('profile-photos', 'public');
            $user->photo = $path;
        }
    }

    $user->save();
    session()->flash('message', 'Funcionário atualizado!');
    $this->editedUserIndex = null;






    // Encontre o índice do usuário no array branchUsers
    $index = collect($this->branchUsers)->search(fn($u) => $u['user_id'] == $userId);

    if ($index === false) {
        session()->flash('error', 'Usuário não encontrado.');
        return;
    }

    $branchId = $this->branchUsers[$index]['branch_id'] ?? null;
  

    if (empty($branchId)) {
        session()->flash('error', 'Selecione uma filial antes de salvar.');
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
    session()->flash('message', 'Usuário atualizado com sucesso.');
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

    public function render()
    {
        return view('livewire.proprietario.branch-users', [
            'branchUsers' => $this->branchUsers,
            
            'branches' => Branch::all(),
            //'users' => User::all(),
            
        ]);
    }
}

