<?php

namespace App\Livewire\Proprietario;

use Livewire\Component;
use App\Models\User;

class EmployeeSelector extends Component
{
    public $showOnlyActive = true;

    public function render()
    {
        $employees = User::whereHas('roles', function($query) {
            $query->where('role', 'Funcionário');
        });

        if ($this->showOnlyActive) {
            $employees->where('status', 'Ativo');
        }

        $employees = $employees->get();

        return view('livewire.proprietario.employee-selector', [
            'employees' => $employees
        ]);
    }
}
