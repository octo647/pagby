<?php

namespace App\Livewire;
use App\Models\Appointment;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\User;


class Appointments extends Component
{
    public array $agendamentos = [];

    public function mount():void
    {
        $customer_id = Auth::user()->id;

        $appointments = Appointment::where('customer_id', $customer_id)->whereDate('appointment_date', '>=', date('Y-m-d'))
        ->get()
        ->toArray();
   
        $agendamentos = [];
       
        $i=0;
        $subtotal = 0;
        

        foreach ($appointments  as $appointment) {
         
            $agendamentos[$i]['id']= $appointment['id'];
            $agendamentos[$i]['date']= date('d/m', strtotime($appointment['appointment_date']));
            $agendamentos[$i]['start_time']= preg_replace('/(\d{2}:\d{2}):00/', '${1}',$appointment['start_time']);
            //$agendamentos[$i]['start_time']= $appointment['start_time'];
            $branch = Branch::where('id', $appointment['branch_id'])->first();
            $agendamentos[$i]['branch'] = $branch ? $branch->branch_name : 'N/A';
            $agendamentos[$i]['services'] = $appointment['services'];
            $agendamentos[$i]['total'] = number_format($appointment['total'], 2, ',', '.');
            $professional = User::where('id', $appointment['employee_id'])->first();            
            $agendamentos[$i]['professional'] = $professional ? $professional->name : 'N/A';   
               
            $agendamentos[$i]['status'] = $appointment['status'];
            $agendamentos[$i]['appointment_id'] = $appointment['id'];
            $agendamentos[$i]['notes'] = $appointment['notes'];
            $created_by = User::where('id', $appointment['created_by'])->first();
         
            $agendamentos[$i]['created_by'] = $created_by ? $created_by->name : 'N/A';
            
            $agendamentos[$i]['created_at'] = date('d/m/Y', strtotime($appointment['created_at']));
            if ($appointment['updated_by'] == null) {
                $agendamentos[$i]['updated_by'] = 'N/A';
            }
            else
            {
                $agendamentos[$i]['updated_by'] = User::where('id', $appointment['updated_by'])->first()->name;
            }

            $agendamentos[$i]['updated_at'] = date('d/m/Y', strtotime($appointment['updated_at']));
            $agendamentos[$i]['cancellation_reason'] = $appointment['cancellation_reason'];
            $agendamentos[$i]['cancellation_date'] = date('d/m/Y', strtotime($appointment['cancellation_date']));
            $agendamentos[$i]['cancellation_time'] = preg_replace('/(\d{2}:\d{2}):00/', '${1}', $appointment['cancellation_time']);
            if ($appointment['cancellation_by'] == null) {
                $agendamentos[$i]['cancellation_by'] = 'N/A';
            }
            else    
            {
                $agendamentos[$i]['cancellation_by'] = User::where('id', $appointment['cancellation_by'])->first()->name;
            }
                      
            
            $i++;
        }
        

       $this->agendamentos = $agendamentos;

    }
    public function deleteSchedule($schedule_id):void
    {
        $schedules = Appointment::where('id', $schedule_id)->delete();
         redirect("/dashboard");
    }
    public function newSchedule():void
    {

         redirect("/dashboard");
    }
    public function render()
    {
        return view('livewire.appointments');

    }
}
