<?php

namespace App\Livewire;

use Livewire\Component;
use \App\Models\Branch;

class Branches extends Component
{
    public $branches = [];
    public $chosen_branch=null;
    
    public function mount(){
        $this->branches = Branch::all();
    }
    public function render()
    {
        return view('livewire.branches');
    }
    public function chosenBranch($branch){
        
        $this->chosen_branch = $branch;
        $this->dispatch('chosen_branch', $this->chosen_branch);
    }
}
