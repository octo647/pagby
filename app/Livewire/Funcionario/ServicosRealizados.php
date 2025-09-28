
<?php
namespace App\Livewire\Funcionario;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Appointment;
use Livewire\WithPagination;


class ServicosFuncionarioRealizados extends Component
{   
    use WithPagination;
    public $filtroPeriodo = '1D';
    public $servicosRealizados = [];
    public function setFiltroPeriodo($periodo)
    {
        $this->filtroPeriodo = $periodo;
        $this->resetPage(); // se usar paginação
                
    }
       
    public function getAppointmentsProperty()
{
    $query = \App\Models\Appointment::with('customer')
    ->where('employee_id', auth()->id())
    ->where('services', '!=', 'Bloqueio')
    ->where('status', '!=', 'bloqueio'); // Excluir agendamentos de bloqueio por services e status
 

    switch ($this->filtroPeriodo) {
        case '1D':
            $query->whereDate('appointment_date', now());
            break;
        case '5D':
            $query->whereBetween('appointment_date', [now()->subDays(4), now()]);
            break;
         case '1M':
            $query->whereBetween('appointment_date', [now()->subMonths(1), now()]);
            break;   
        case '6M':
            $query->whereBetween('appointment_date', [now()->subMonths(6), now()]);
            break;
        case '1A':
            $query->whereBetween('appointment_date', [now()->subYear(), now()]);
            break;
        case 'Tudo':
        default:
            // sem filtro de data
            break;
    }

    return $query->orderByDesc('appointment_date')->paginate(10);
}
    
    public function atualizarStatus($id, $novoStatus)
    {
        $servico = \App\Models\Appointment::find($id);
        if ($servico) {
            $servico->status = $novoStatus;
            if ($novoStatus === 'Cancelado') {
                $servico->cancellation_reason = 'Cancelado por '. Auth::user()->name;
                $servico->cancellation_time = now(); // Define o horário de cancelamento
                $servico->cancellation_by = Auth::id(); // Define o usuário que cancelou
                $servico->cancellation_date = now(); // Define a data de cancelamento

            } else {
                $servico->cancellation_reason = null; // Limpa o motivo de cancelamento se não for cancelado
            }
            $servico->updated_at = now(); // Atualiza o timestamp de atualização
            $servico->updated_by = Auth::id(); // Define o usuário que atualizou o status
            $servico->save();
            $this->render(); // Atualiza a lista
        }
    }
    
    public function render()
    {   
        
    /*    $usuario = User::with(['employeeAppointments.customer', 'employeeAppointments.branch'])->find(Auth::id());
           // Eager load the employeeAppointments relationship        
        $usuario = Auth::user();

    $appointments = $usuario->employeeAppointments()
        ->with(['customer', 'branch'])
        ->orderByDesc('appointment_date')
        ->paginate(10); */
        

    return view('livewire.funcionario.servicos-realizados', [
        'appointments' => $this->appointments,
    ]);
  

       
    }
}
