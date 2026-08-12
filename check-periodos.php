<?php

require __DIR__.'/vendor/autoload.php';

use Carbon\Carbon;

$hoje = Carbon::today();
echo "Hoje: " . $hoje->format('d/m/Y') . "\n\n";

// Semanal
$dataInicio = $hoje->copy()->startOfWeek();
$dataFim = $hoje->copy()->endOfWeek();
echo "SEMANAL:\n";
echo "  Início: " . $dataInicio->format('d/m/Y') . "\n";
echo "  Fim: " . $dataFim->format('d/m/Y') . "\n\n";

// Quinzenal
$dataInicio = $hoje->copy()->subDays($hoje->day <= 15 ? $hoje->day - 1 : $hoje->day - 16);
$dataFim = $dataInicio->copy()->addDays(14);
echo "QUINZENAL:\n";
echo "  Início: " . $dataInicio->format('d/m/Y') . "\n";
echo "  Fim: " . $dataFim->format('d/m/Y') . "\n\n";

// Mensal
$dataInicio = $hoje->copy()->startOfMonth();
$dataFim = $hoje->copy()->endOfMonth();
echo "MENSAL:\n";
echo "  Início: " . $dataInicio->format('d/m/Y') . "\n";
echo "  Fim: " . $dataFim->format('d/m/Y') . "\n";
