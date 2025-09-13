<?php

namespace App\Livewire\Proprietario;

use Livewire\Component;
use App\Models\Branch;


class Filiais extends Component
{
   public $branches = [];
   public $branch = [
       'branch_name' => '',
       'address' => '',
       'phone' => '',
       'email' => '',
       'cnpj' => '',
       'whatsapp' => '',
       'city' => '',
       'state' => '',
       'complement' => '',
       'require_advance_payment' => false,
       'require_comission' => false,
       'comission' => 0
   ];

   public $isEditing = false;
   public $showForm = false;


   public function mount()
   {
       $this->loadBranches();
   }

   public function loadBranches()
   {
       $this->branches = \App\Models\Branch::all();
       
      
   }

   public function edit($id)
   {
    $branch = \App\Models\Branch::find($id);
    $branchArray = $branch->toArray();
    // Cast boolean fields for Livewire checkboxes
    $branchArray['require_advance_payment'] = (bool) $branchArray['require_advance_payment'];
    $branchArray['require_comission'] = (bool) $branchArray['require_comission'];
    $this->branch = $branchArray;
    $this->isEditing = true;
    $this->showForm = true;
   }

   public function save()
   {
       $this->validate([
           'branch.branch_name' => 'required|string|max:255',
           'branch.address' => 'nullable|string|max:255',
           'branch.phone' => 'nullable|string|max:20',
           'branch.email' => 'nullable|email|max:255',
           'branch.cnpj' => 'nullable|string|max:18',
           'branch.whatsapp' => 'nullable|string|max:20',
           'branch.city' => 'nullable|string|max:255',
           'branch.state' => 'nullable|string|max:255',
           'branch.complement' => 'nullable|string|max:255',
       ]);

       if ($this->isEditing) {
           $branch = \App\Models\Branch::find($this->branch['id']);
           $branch->update($this->branch);
           session()->flash('message', 'Filial atualizada com sucesso!');
       } else {
           \App\Models\Branch::create($this->branch);
           session()->flash('message', 'Filial criada com sucesso!');
       }
       
       $this->resetForm();
       $this->showForm = false;
       $this->loadBranches();
   }

   public function delete($id)
   {
       \App\Models\Branch::destroy($id);
       session()->flash('message', 'Filial excluída com sucesso!');
       $this->resetForm();
       $this->loadBranches();
   }

   public function resetForm()
   {
       $this->branch = [
           'branch_name' => '',
           'address' => '',
           'phone' => '',
           'email' => '',
           'cnpj' => '',
           'whatsapp' => '',
           'city' => '',
           'state' => '',
           'complement' => '',
           'require_advance_payment' => false,
           'require_comission' => false,
           'comission' => 0
       ];
       $this->isEditing = false;
   }

   public function cancelForm()
   {
       $this->resetForm();
       $this->showForm = false;
   }

   public function render()
   {
       return view('livewire.proprietario.filiais');
   }
}
