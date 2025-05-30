<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Role;
use App\Models\Salon;
use App\Models\MorningTime;
use Illuminate\Support\Facades\DB;


class RoleUser extends Component
{
    public $editedUserIndex = null;
    public $editedUserField = null;
    public $usuarios = [];
    public function mount(){
        $users = User::all();
        $roles = Role::all();
        $salons = Salon::all();
        $role_users = DB::table("role_user")->get();
    
        $i=0;
        foreach ($role_users as $role_user) {
            $salon_id = $users->where('id', $role_user->user_id)->first()->salon_id;               
            $usuarios[$i]["user_id"] = $role_user->user_id;
            $usuarios[$i]["nome"] = User::where("id", $role_user->user_id)->first()->name;
            $usuarios[$i]["email"] = User::where("id", $role_user->user_id)->first()->email;
            $usuarios[$i]["salao"] = Salon::where("id", $salon_id)->first()->salon;
            $usuarios[$i]["funcao"] = Role::where('id', $role_user->role_id)->first()->role;
            $i++;
        }
        $this->usuarios = $usuarios;
    }

    public function render(){
        return view('livewire.role-user',['usuarios'=> $this->usuarios]);
    }
    
    
    public function editUser($userIndex){
        $this->editedUserIndex = $userIndex;
    }
    public function editUserField($userIndex, $fieldName)
    {
        $this->editedUserField = $userIndex.'.'.$fieldName;
    }
    public function updateRole($userIndex)
    {  
        
        $usuario = $this->usuarios[$userIndex] ?? null;
       
        if (!is_null($usuario)) {   
                           
            $role = DB::table('role_user')->where('user_id','=', $usuario)->get();  
                      
            $funcao = $usuario['funcao'];
            
            $id_funcao = Role::where('role', $funcao)->first();
            
            $id = $id_funcao['id'];
            
            $role[0]->role_id = $id;           
            
            $role_atualizado = DB::table('role_user')
            ->where('user_id','=', $usuario['user_id'])
            ->update(['role_id'=> $id]);
            if($funcao == 'Funcionário'){
                DB::table('morning_times')->insert([
                    'employee_id'=> $usuario['user_id'],
                ]);
            }
            if($funcao != 'Funcionário'){
                DB::table('morning_times')->where(
                    'employee_id', $usuario['user_id'])
                    ->delete();
            }
            redirect('/dashboard');
        }
    }
    public function deleteUser($userIndex)
    {
        $usuario = $this->usuarios[$userIndex] ?? null;
       
        if (!is_null($usuario)) {   
                           
        $role = DB::table('role_user')->where('user_id','=', $usuario)->delete();  
        redirect('/dashboard');
              
        }

    }


}
