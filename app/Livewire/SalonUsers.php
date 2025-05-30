<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Role;
use App\Models\RoleUser;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class SalonUsers extends Component
{
        public $editedUserIndex = null;
        public $editedUserField = null;
        public $usuarios = [];
        public function mount(){

            $salon_users = User::all();
            $roles = Role::all();
            $role_users = DB::table("role_user")->get();


            $i=0;
            foreach ($salon_users as $salon_user) {

                $role_id = DB::table("role_user")->where('user_id', $salon_user->id)->first()->role_id;

                $usuarios[$i]["user_id"] = $salon_user->id;
                $usuarios[$i]["nome"] = $salon_user->name;
                $usuarios[$i]["email"] = $salon_user->email;
                $usuarios[$i]["funcao"] = Role::where('id', $role_id)->first()->role;
                $i++;
            }
            $this->usuarios = $usuarios;

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
                    DB::table('office_hours')->insert([
                        'employee_id'=> $usuario['user_id'],
                    ]);
                    DB::table('intervals')->insert([
                        'employee_id'=> $usuario['user_id'],
                    ]);
                }
                if($funcao != 'Funcionário'){
                    DB::table('office_hours')->where(
                        'employee_id', $usuario['user_id'])
                        ->delete();
                    DB::table('intervals')->where(
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



    public function render()
    {

        return view('livewire.salon-users', ['usuarios'=> $this->usuarios]);
    }
}
