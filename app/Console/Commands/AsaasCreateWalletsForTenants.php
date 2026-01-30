<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class AsaasCreateWalletsForTenants extends Command
{
    protected $signature = 'asaas:create-wallets-for-tenants {--dry-run}';
    protected $description = 'Cria wallets no Asaas para todos os tenants que ainda não possuem asaas_wallet_id';

    public function handle()
    {
        $asaasApiKey = config('services.asaas.api_key');
        $asaasApiUrl = config('services.asaas.api_url', 'https://www.asaas.com/api/v3');
        $client = new Client();
        $dryRun = $this->option('dry-run');

        $tenants = Tenant::on('mysql')->whereNull('asaas_wallet_id')->get();
        if ($tenants->isEmpty()) {
            $this->info('Nenhum tenant sem wallet_id encontrado.');
            return 0;
        }

        foreach ($tenants as $tenant) {
            $this->info("Processando tenant: {$tenant->id} - {$tenant->name}");
            $payload = [
                'name' => $tenant->name,
                'cpfCnpj' => $tenant->documento ?? $tenant->cpf_cnpj ?? '',
                'email' => $tenant->email,
                // Adicione outros campos obrigatórios do Asaas aqui se necessário
            ];

            if ($dryRun) {
                $this->info('DRY RUN: Não enviando para o Asaas. Payload: ' . json_encode($payload));
                continue;
            }

            try {
                $response = $client->post($asaasApiUrl . '/wallets', [
                    'headers' => [
                        'access_token' => $asaasApiKey,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $payload,
                    'timeout' => 20,
                ]);
                $data = json_decode($response->getBody(), true);
                if (!empty($data['id'])) {
                    $tenant->asaas_wallet_id = $data['id'];
                    $tenant->save();
                    $this->info("Wallet criada e salva: {$data['id']}");
                } else {
                    $this->error('Resposta inesperada do Asaas: ' . $response->getBody());
                }
            } catch (\Exception $e) {
                $this->error('Erro ao criar wallet: ' . $e->getMessage());
                Log::error('Erro ao criar wallet Asaas', [
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        $this->info('Processo finalizado.');
        return 0;
    }
}
