<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class AsaasListWallets extends Command
{
    protected $signature = 'asaas:list-wallets {--search=}';
    protected $description = 'Lista todas as carteiras (wallets) da conta Asaas, com walletId, nome e email. Use --search para filtrar por nome/email.';

    public function handle()
    {
        $apiKey = config('services.asaas.api_key');
        $this->info('API KEY utilizada: ' . substr($apiKey, 0, 8) . '...');
        $apiUrl = config('services.asaas.api_url', 'https://www.asaas.com/api/v3');
        $search = $this->option('search');

        $page = 1;
        $wallets = [];
        do {
            $response = Http::withHeaders([
                    'access_token' => $apiKey,
                    'accept' => 'application/json',
                ])
                ->get("$apiUrl/wallets", [
                    'limit' => 100,
                    'offset' => ($page - 1) * 100,
                ]);
            if (!$response->ok()) {
                $this->error('Erro ao consultar API Asaas:');
                $this->error('Status: ' . $response->status());
                $this->error('Body: ' . $response->body());
                return 1;
            }
            $data = $response->json();
            if (empty($data['data'])) break;
            $wallets = array_merge($wallets, $data['data']);
            $page++;
        } while (count($data['data']) === 100);

        if ($search) {
            $wallets = array_filter($wallets, function ($w) use ($search) {
                return stripos($w['name'], $search) !== false || stripos($w['email'], $search) !== false;
            });
        }

        if (empty($wallets)) {
            $this->info('Nenhuma carteira encontrada.');
            return 0;
        }

        $rows = [];
        foreach ($wallets as $w) {
            $this->line('---');
            foreach ($w as $key => $value) {
                $this->line($key . ': ' . (is_array($value) ? json_encode($value) : $value));
            }
        }
        if (empty($wallets)) {
            $this->error('Nenhuma carteira encontrada.');
        }
        return 0;
    }
}
