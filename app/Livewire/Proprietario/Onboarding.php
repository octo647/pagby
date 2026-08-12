<?php

namespace App\Livewire\Proprietario;

use Livewire\Component;
use App\Models\Branch;
use App\Models\User;
use App\Models\Service;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

class Onboarding extends Component
{
    public $steps = [];
    public $currentStep = 1;
    public $completedSteps = 0;
    public $totalSteps = 0;
    
    public function mount()
    {
        $this->checkProgress();
    }

    public function checkProgress()
    {
        $tenant = tenant();
        
        // Verificar cada passo
        $this->steps = [
            [
                'number' => 1,
                'title' => 'Criar Filial',
                'description' => 'Configure ao menos uma filial para sua empresa',
                'completed' => Branch::count() > 0,
                'route' => route('tenant.dashboard', ['tabelaAtiva' => 'filiais']),
                'icon' => '🏢'
            ],
            [
                'number' => 2,
                'title' => 'Cadastrar Funcionários',
                'description' => 'Adicione os funcionários que trabalham no salão',
                'completed' => User::whereHas('roles', function($q) {
                    $q->where('role', 'Funcionário');
                })->count() > 0,
                'route' => route('tenant.dashboard', ['tabelaAtiva' => 'usuarios']),
                'icon' => '👥'
            ],
            [
                'number' => 3,
                'title' => 'Atribuir Filiais aos Funcionários',
                'description' => 'Vincule os funcionários às filiais onde trabalham',
                'completed' => DB::table('branch_user')->count() > 0,
                'route' => route('tenant.dashboard', ['tabelaAtiva' => 'funcionarios']),
                'icon' => '🔗'
            ],
            [
                'number' => 4,
                'title' => 'Criar Serviços',
                'description' => 'Cadastre os serviços oferecidos (preferencialmente com imagens)',
                'completed' => Service::count() > 0,
                'route' => route('tenant.dashboard', ['tabelaAtiva' => 'servicos']),
                'icon' => '✂️'
            ],
            [
                'number' => 5,
                'title' => 'Atribuir Serviços aos Funcionários',
                'description' => 'Defina quais serviços cada funcionário pode realizar',
                'completed' => DB::table('service_user')->count() > 0,
                'route' => route('tenant.dashboard', ['tabelaAtiva' => 'func_serv']),
                'icon' => '🎯'
            ],
            [
                'number' => 6,
                'title' => 'Definir Horários de Trabalho',
                'description' => 'Configure o horário de atendimento de cada funcionário',
                'completed' => Schedule::count() > 0,
                'route' => route('tenant.dashboard', ['tabelaAtiva' => 'horarios']),
                'icon' => '⏰'
            ]
        ];
        
        // Adicionar passo de customização apenas para template Padrão
        if ($tenant && $tenant->template === 'Padrao') {
            $this->steps[] = [
                'number' => 7,
                'title' => 'Customizar a Home',
                'description' => 'Personalize a página inicial do seu salão',
                'completed' => $tenant->data && isset($tenant->data['home_customized']),
                'route' => route('tenant.dashboard', ['tabelaAtiva' => 'customizar-home']),
                'icon' => '🎨'
            ];
        }
        
        // Renumerar os passos
        $this->steps = collect($this->steps)->values()->map(function($step, $index) {
            $step['number'] = $index + 1;
            return $step;
        })->toArray();
        
        // Atualizar total de steps
        $this->totalSteps = count($this->steps);

        // Contar passos completados
        $this->completedSteps = collect($this->steps)->where('completed', true)->count();
        
        // Atualizar progresso no tenant
        if ($tenant) {
            $progress = collect($this->steps)->mapWithKeys(function($step) {
                return ['step_' . $step['number'] => $step['completed']];
            })->toArray();
            
            $tenant->onboarding_progress = $progress;
            $tenant->onboarding_completed = $this->completedSteps === $this->totalSteps;
            $tenant->save();
        }
    }

    public function refreshProgress()
    {
        $this->checkProgress();
        session()->flash('message', 'Progresso atualizado!');
    }

    public function completeOnboarding()
    {
        $tenant = tenant();
        if ($tenant && $this->completedSteps === $this->totalSteps) {
            $tenant->onboarding_completed = true;
            $tenant->save();
            
            return redirect()->route('tenant.dashboard')->with('success', 'Configuração inicial concluída com sucesso!');
        }
    }

    public function render()
    {
        $progressPercentage = ($this->completedSteps / $this->totalSteps) * 100;
        
        return view('livewire.proprietario.onboarding', [
            'progressPercentage' => $progressPercentage
        ]);
    }
}
