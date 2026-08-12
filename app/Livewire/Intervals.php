<?php

namespace App\Livewire;
use App\Models\Interval;
use App\Models\User;
use Livewire\Component;

class Intervals extends Component
{
    public $editedIndex = null;
    public $editedField = null;
    public array $intervals = [];

    public function mount()
    {
        $times = Interval::all()->toArray();
        foreach ($times as $index=>$available){
            $available['funcionario']=
                User::where('id',$times[$index]['employee_id'])
                    ->first()
                    ->name;
            $times[$index]= $available;
        }
        $this->intervals = $times;
    }

    public function editMT($mtIndex):void
    {
        $this->editedIndex = $mtIndex;
    }

    public function editField($Index, $fieldName):void
    {
        $this->editedField = $Index.'.'.$fieldName;
    }
    public function saveMT($mtIndex)
    {
        $interval = $this->intervals[$mtIndex] ?? null;
        if (!is_null($interval)) {
            $mt = Interval::find($interval['id']);
            $mt->employee_id = $interval['employee_id'];
            $mt->seg_int1 = $interval['seg_int1'];
            $mt->seg_int2 = $interval['seg_int2'];
            $mt->ter_int1 = $interval['ter_int1'];
            $mt->ter_int2 = $interval['ter_int2'];
            $mt->qua_int1 = $interval['qua_int1'];
            $mt->qua_int2 = $interval['qua_int2'];
            $mt->qui_int1 = $interval['qui_int1'];
            $mt->qui_int2 = $interval['qui_int2'];
            $mt->sex_int1 = $interval['sex_int1'];
            $mt->sex_int2 = $interval['sex_int2'];
            $mt->sab_int1 = $interval['sab_int1'];
            $mt->sab_int2 = $interval['sab_int2'];
            $mt->dom_int1 = $interval['dom_int1'];
            $mt->dom_int2 = $interval['dom_int2'];
            $mt->save();
        }

        $this->editedIndex = null;
        $this->editedField = null;

    }
    public function deleteMT($mtIndex)
    {
        $interval = $this->intervals[$mtIndex] ?? null;
        $mt = Interval::find($interval['id']);
        $mt->delete();
        redirect('/dashboard');

    }







    public function render()
    {
        return view('livewire.intervals');
    }
}
