<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PlanAdjustment;
use App\Models\Tenant;

class PlanAdjustments extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterType = '';

    protected $queryString = ['search', 'filterStatus', 'filterType'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function render()
    {
        $adjustments = PlanAdjustment::query()
            ->with('tenant')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('tenant_id', 'like', '%' . $this->search . '%')
                        ->orWhereHas('tenant', function ($tenantQuery) {
                            $tenantQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterType, function ($query) {
                $query->where('type', $this->filterType);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => PlanAdjustment::count(),
            'pending_credits' => PlanAdjustment::where('type', 'credit')->where('status', 'pending')->sum('amount'),
            'pending_debits' => PlanAdjustment::where('type', 'debit')->where('status', 'pending')->sum('amount'),
            'paid_debits' => PlanAdjustment::where('type', 'debit')->where('status', 'paid')->sum('amount'),
            'applied_credits' => PlanAdjustment::where('type', 'credit')->where('status', 'applied')->sum('amount'),
        ];

        return view('livewire.admin.plan-adjustments', [
            'adjustments' => $adjustments,
            'stats' => $stats,
        ]);
    }
}
