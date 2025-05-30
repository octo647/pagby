<?php

namespace App\Livewire;
use Livewire\Component;
use App\Models\OfficeHour;
use App\Models\User;
class SalonTimes extends Component
{

    public $editedIndex = null;
    public $editedField = null;
    public array $officehours = [];

    public function mount()
    {
        $times = OfficeHour::all()->toArray();
        foreach ($times as $index=>$available){
            $available['funcionario']=
                User::where('id',$times[$index]['employee_id'])
                    ->first()
                    ->name;
            $times[$index]= $available;
        }
       $this->officehours = $times;
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
        $officehour = $this->officehours[$mtIndex] ?? null;
        if (!is_null($officehour)) {
            $mt = OfficeHour::find($officehour['id']);
            $mt->employee_id = $officehour['employee_id'];
            $mt->seg_ini = $officehour['seg_ini'];
            $mt->seg_fim = $officehour['seg_fim'];
            $mt->ter_ini = $officehour['ter_ini'];
            $mt->ter_fim = $officehour['ter_fim'];
            $mt->qua_ini = $officehour['qua_ini'];
            $mt->qua_fim = $officehour['qua_fim'];
            $mt->qui_ini = $officehour['qui_ini'];
            $mt->qui_fim = $officehour['qui_fim'];
            $mt->sex_ini = $officehour['sex_ini'];
            $mt->sex_fim = $officehour['sex_fim'];
            $mt->sab_ini = $officehour['sab_ini'];
            $mt->sab_fim = $officehour['sab_fim'];
            $mt->dom_ini = $officehour['dom_ini'];
            $mt->dom_fim = $officehour['dom_fim'];
            $mt->save();
        }

        $this->editedIndex = null;
        $this->editedField = null;

    }
    public function deleteMT($mtIndex)
    {
        $officehour = $this->officehours[$mtIndex] ?? null;
        $mt = OfficeHour::find($officehour['id']);
        $mt->delete();
        redirect('/dashboard');

    }
    public function render()
    {
        return view('livewire.salon-times');
    }

}
