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

            $primaryRole = $user->getPrimaryRole();

            if ($primaryRole === 'Proprietário') {
                $tabelaAtiva = $request->get('tabelaAtiva', 'gerenciar-comandas');
            } elseif ($primaryRole === 'Funcionário') {
                $tabelaAtiva = $request->get('tabelaAtiva', 'agenda');
            } elseif ($primaryRole === 'Cliente') {
                $tabelaAtiva = $request->get('tabelaAtiva', 'appointments');
            } elseif ($primaryRole === 'Admin') {
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
