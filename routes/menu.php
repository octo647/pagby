<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// ...existing routes...

Route::post('/menu/selecionar', function(Request $request) {
    $menu = $request->input('menu');
    if (in_array($menu, ['proprietario', 'funcionario'])) {
        session(['menu_selecionado' => $menu]);
        return response()->json(['ok' => true]);
    }
    return response()->json(['ok' => false], 400);
})->middleware(['web', 'auth'])->name('menu.selecionar');
