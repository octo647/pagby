<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Branch;
use App\Models\Service;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->hasRole('Proprietario')) {
           abort(403);
        }
        $plans = \App\Models\Plan::all();
        $branches = Branch::all();
        $services = Service::all();
        return view('plans.create', compact('plans', 'branches', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate and store the plan data
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'services' => 'required',
            'additional_services' => 'nullable',
            'features' => 'nullable',
            'active' => 'required|boolean',
            'branch_id' => 'required|integer|exists:branches,id',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
            // 'created_by' should not be here as a validation rule
        ]);
            // Trate os campos JSON
            // Sempre converta para array, mesmo se vier vazio
            // Sempre converta para array, mesmo se vier vazio
            if (is_string($request->services)) {
                $data['services'] = json_decode($request->services, true) ?? [];
            } elseif (is_array($request->services)) {
                $data['services'] = $request->services;
            } else {
                $data['services'] = [];
            }

            $data['additional_services'] = $request->additional_services
                ? json_decode($request->additional_services, true)
                : [];

            $data['features'] = $request->features
                ? json_decode($request->features, true)
                : [];

            $data['created_by'] = auth()->id();

        // Assuming you have a Plan model
        \App\Models\Plan::create($data);

        return redirect()->route('plans.index')->with('success', 'Plan created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Show the details of a specific plan
        $plan = \App\Models\Plan::findOrFail($id);
        return view('plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Show the form to edit a specific plan
        $plan = \App\Models\Plan::findOrFail($id);
        if (!auth()->user()->hasRole('Proprietario')) {
            abort(403);
        }
        $branches = Branch::all();
        $services = Service::all();
        // Assuming you have a Plan model
        return view('plans.edit', compact('plan', 'branches', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate and update the plan data
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        // Trate os campos JSON
        // Sempre converta para array, mesmo se vier vazio
        
        if (is_string($request->services)) {
            $data['services'] = json_decode($request->services, true) ?? [];
        } elseif (is_array($request->services)) {
            $data['services'] = $request->services;
        } else {
            $data['services'] = [];
        }
        $data['additional_services'] = $request->additional_services
            ? json_decode($request->additional_services, true)
            : null;

        // Assuming you have a Plan model
        $plan = \App\Models\Plan::findOrFail($id);
        $plan->update($data);

        return redirect()->route('plans.index')->with('success', 'Plan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Delete the specified plan
        $plan = \App\Models\Plan::findOrFail($id);
        $plan->delete();

        return redirect()->route('plans.index')->with('success', 'Plan deleted successfully.');
    }
}
