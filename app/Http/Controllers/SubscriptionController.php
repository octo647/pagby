<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function store(Request $request)
    {
       
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
           
        ]);
        

        Subscription::create([
            'user_id' => Auth::id(),
            'plan_id' => $request->plan_id,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(), // você pode ajustar isso conforme necessário   
            
        ]);
        

        return redirect()->route('dashboard')->with('success', 'Assinatura realizada com sucesso!');
    }
}
