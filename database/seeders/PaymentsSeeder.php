<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\Appointment;

class PaymentsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Transaction::all() as $transaction) {
            $appointment = Appointment::find($transaction->appointment_id);

            DB::table('payments')->insert([
                'appointment_id' => $transaction->appointment_id,
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'payment_method' => $transaction->payment_method,
                'status' => $transaction->status,
                'paid_at' => now(), // ajuste se houver campo específico
                // Adicione outros campos necessários aqui
            ]);
        }
    }
}
