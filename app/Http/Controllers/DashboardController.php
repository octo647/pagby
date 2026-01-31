<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
  


class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $tabelaAtiva = 'usuarios'; // valor padrão para proprietário
        
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->hasRole('Funcionário')) {
                $tabelaAtiva = $request->get('tabelaAtiva', 'agenda');
            } elseif ($user->hasRole('Proprietário')) {
                $tabelaAtiva = $request->get('tabelaAtiva', 'gerenciar-comandas');
            } elseif ($user->hasRole('Cliente')) {
                $tabelaAtiva = $request->get('tabelaAtiva', 'appointments');
            } elseif ($user->hasRole('Admin')) {
                $tabelaAtiva = $request->get('tabelaAtiva', 'contatos');
        }
        }
        
        // Buscar funcionários para a view
        $employees = User::whereHas('roles', function($query) {
            $query->where('role', 'Funcionário');
        })->orderBy('name')->get();
        
        return view('dashboard', compact('tabelaAtiva', 'employees'));
    }
}
