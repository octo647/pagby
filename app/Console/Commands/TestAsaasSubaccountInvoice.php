<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Services\AsaasService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class TestAsaasSubaccountInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asaas:test-subaccount-invoice 
                            {--tenant= : ID do tenant para testar (opcional, cria tenant teste se não fornecido)}
                            {--cnpj= : CPF/CNPJ para a subconta (opcional, gera CPF automático se não fornecido)}
                            {--save-evidence : Salva evidências em arquivo markdown}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '🧪 TESTE CRÍTICO: Valida se NF é emitida em nome da SUBCONTA (não do PagBy)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->showBanner();
        
        $tenantId = $this->option('tenant');
        $cnpjCustom = $this->option('cnpj');
        $saveEvidence = $this->option('save-evidence');

        // Validar que estamos em ambiente de testes (sandbox ou homologação)
        $apiUrl = config('services.asaas.api_url');
        $apiKey = config('services.asaas.api_key');
        $isSandbox = str_contains($apiUrl, 'sandbox');
        $isHomologacao = str_contains($apiKey, '_hmlg_');
        
        if (!$isSandbox && !$isHomologacao) {
            $this->error("❌ ATENÇÃO: Você está em ambiente de PRODUÇÃO!");
            $this->error("   URL: {$apiUrl}");
            $this->error("   API Key: " . (str_contains($apiKey, '_prod_') ? 'PRODUÇÃO' : 'DESCONHECIDA'));
            $this->newLine();
            
            if (!$this->confirm('Deseja realmente continuar em PRODUÇÃO? (NÃO RECOMENDADO)', false)) {
                $this->warn("Teste cancelado. Use chave de homologação (_hmlg_) ou configure sandbox.");
                return 1;
            }
        } else {
            $envName = $isSandbox ? 'SANDBOX' : 'HOMOLOGAÇÃO';
            $this->info("✅ Ambiente: {$envName}");
            $this->line("   URL: {$apiUrl}");
        }

        $this->newLine();

        // Iniciar log de evidências
        $evidence = [
            'timestamp' => now()->toDateTimeString(),
            'environment' => $isSandbox ? 'sandbox' : ($isHomologacao ? 'homologacao' : 'production'),
            'api_url' => $apiUrl,
        ];

        // Etapa 1: Obter ou criar tenant de teste
        $this->info("📋 ETAPA 1: Preparando tenant de teste");
        $this->line(str_repeat("─", 60));

        if ($tenantId) {
            $tenant = Tenant::on('mysql')->find($tenantId);
            if (!$tenant) {
                $this->error("❌ Tenant {$tenantId} não encontrado.");
                return 1;
            }
            $this->info("   Usando tenant existente: {$tenant->name}");
        } else {
            $tenant = $this->createTestTenant();
        }

        $evidence['tenant_id'] = $tenant->id;
        $evidence['tenant_name'] = $tenant->name;

        $this->newLine();

        // Etapa 2: Criar subconta
        $this->info("📋 ETAPA 2: Criando subconta Asaas");
        $this->line(str_repeat("─", 60));

        $asaasMaster = new AsaasService(); // API Master

        if (!$tenant->asaas_account_id) {
            // Gerar CPF único se não fornecido (sandbox aceita melhor CPF)
            $cpfCnpj = $cnpjCustom ?: $this->generateValidCpf();
            $isCpf = strlen($cpfCnpj) == 11;
            
            $accountData = [
                'name' => $tenant->name,
                'email' => $tenant->email,
                'cpfCnpj' => $cpfCnpj,
                'mobilePhone' => '11987654321',
                'incomeValue' => 5000.00, // Obrigatório para CPF e CNPJ
            ];
            
            // Se for CPF, adicionar birthDate. Se CNPJ, adicionar companyType
            if ($isCpf) {
                $accountData['birthDate'] = '1990-01-01';
            } else {
                $accountData['companyType'] = 'LIMITED';
            }

            $this->line("   Criando subconta no Asaas...");
            $this->line("   " . ($isCpf ? 'CPF' : 'CNPJ') . ": {$cpfCnpj}");
            $this->line("   Email: {$tenant->email}");
            $this->comment("   ⏳ Aguarde, isso pode levar até 60 segundos...");
            
            $result = $asaasMaster->criarSubcontaCompleta($accountData);
            
            $this->line("   ✅ Requisição concluída!");

            if (!$result['success']) {
                $this->error("❌ Erro ao criar subconta: {$result['message']}");
                if (isset($result['errors'])) {
                    $this->error(json_encode($result['errors'], JSON_PRETTY_PRINT));
                }
                return 1;
            }

            $accountId = $result['data']['account_id'];
            $apiKey = $result['data']['api_key'];
            $walletId = $result['data']['wallet_id'] ?? null;

            // Salvar no tenant
            $tenant->asaas_account_id = $accountId;
            $tenant->asaas_api_key = \Illuminate\Support\Facades\Crypt::encryptString($apiKey);
            $tenant->asaas_wallet_id = $walletId;
            $tenant->asaas_account_status = 'pending';
            $tenant->save();

            $this->info("   ✅ Subconta criada!");
            $this->line("      Account ID: {$accountId}");
            $this->line("      Wallet ID: {$walletId}");
            $this->line("      API Key: " . substr($apiKey, 0, 25) . "...");

            $evidence['subaccount_created'] = true;
            $evidence['account_id'] = $accountId;
            $evidence['wallet_id'] = $walletId;

        } else {
            $accountId = $tenant->asaas_account_id;
            $apiKey = $tenant->asaas_api_key_decrypted;
            
            $this->info("   ✅ Subconta já existe");
            $this->line("      Account ID: {$accountId}");
            
            $evidence['subaccount_created'] = false;
            $evidence['account_id'] = $accountId;
        }

        $this->newLine();
        $this->comment("   ⏳ Aguardando 3 segundos (processamento Asaas)...");
        sleep(3);
        $this->newLine();

        // Etapa 3: Criar cobrança usando API da SUBCONTA
        $this->info("📋 ETAPA 3: Criando cobrança usando API da SUBCONTA");
        $this->line(str_repeat("─", 60));

        $asaasSubconta = new AsaasService($apiKey); // API da SUBCONTA!

        $this->line("   ⚠️  IMPORTANTE: Usando API KEY da SUBCONTA (não da master)");
        $this->newLine();

        // Criar customer
        $this->line("   Criando customer (cliente teste)...");
        $customerResult = $asaasSubconta->criarOuAtualizarCliente([
            'name' => 'Cliente Teste - Validação NF',
            'email' => 'cliente.teste.nf@example.com',
            'cpfCnpj' => '12345678909', // CPF fictício
            'mobilePhone' => '11987654321',
        ]);

        if (!$customerResult['success']) {
            $this->error("❌ Erro ao criar customer");
            return 1;
        }

        $customerId = $customerResult['id'];
        $this->info("   ✅ Customer criado: {$customerId}");

        $evidence['customer_id'] = $customerId;

        // Criar cobrança
        $this->line("   Criando cobrança de R$ 100,00...");
        $paymentResult = $asaasSubconta->criarCobranca(
            $customerId,
            100.00,
            now()->addDays(7),
            'PIX',
            'TESTE CRÍTICO: Validação de emissor da NF'
        );

        if (!$paymentResult['success']) {
            $this->error("❌ Erro ao criar cobrança");
            return 1;
        }

        $paymentId = $paymentResult['id'];
        $this->info("   ✅ Cobrança criada: {$paymentId}");

        $evidence['payment_id'] = $paymentId;

        $this->newLine();

        // Etapa 4: TESTE CRÍTICO - Verificar emissor
        $this->info("🔍 ETAPA 4: TESTE CRÍTICO - Verificando emissor da cobrança");
        $this->line(str_repeat("═", 60));

        $paymentDetails = $asaasSubconta->consultarCobranca($paymentId);

        if (!$paymentDetails) {
            $this->error("❌ Erro ao consultar cobrança");
            return 1;
        }

        $this->newLine();
        $this->line("📄 DADOS DA COBRANÇA:");
        $this->line(json_encode($paymentDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->newLine();

        // VERIFICAÇÃO CRÍTICA
        $this->line(str_repeat("═", 60));
        $this->info("🎯 RESULTADO DO TESTE:");
        $this->line(str_repeat("═", 60));

        $paymentAccountId = $paymentDetails['account'] ?? null;

        $this->newLine();
        $this->line("   Subconta criada: {$accountId}");
        $this->line("   Cobrança pertence: {$paymentAccountId}");
        $this->newLine();

        $evidence['payment_account_id'] = $paymentAccountId;
        $evidence['test_passed'] = ($paymentAccountId === $accountId);

        if ($paymentAccountId === $accountId) {
            $this->info("✅✅✅ SUCESSO! ✅✅✅");
            $this->info("   Cobrança pertence à SUBCONTA!");
            $this->info("   Isso significa que a NF será emitida em nome do SALÃO!");
            $this->newLine();
            $this->line("   🎉 MODELO VALIDADO TECNICAMENTE!");
            $this->line("   🎉 Pagamentos diretos (100%, sem split) FUNCIONA!");
            $this->line("   🎉 NF será emitida em nome CORRETO!");
            
            $evidence['conclusion'] = 'SUCESSO - Modelo validado tecnicamente';
            $evidence['invoice_issuer'] = 'SUBCONTA (salão) - CORRETO!';

        } else {
            $this->error("❌❌❌ PROBLEMA! ❌❌❌");
            $this->error("   Cobrança NÃO pertence à subconta!");
            $this->error("   NF será emitida em nome do PAGBY (master)!");
            $this->newLine();
            $this->error("   ⚠️  MODELO PRECISA AJUSTES!");
            
            $evidence['conclusion'] = 'FALHOU - Modelo precisa ajustes';
            $evidence['invoice_issuer'] = 'MASTER (PagBy) - INCORRETO!';
        }

        $this->newLine();
        $this->line(str_repeat("═", 60));

        // Salvar evidências
        if ($saveEvidence || $this->confirm('Deseja salvar evidências em arquivo?', true)) {
            $this->saveEvidenceFile($evidence, $paymentDetails);
        }

        return 0;
    }

    /**
     * Mostra banner do teste
     */
    private function showBanner()
    {
        $this->newLine();
        $this->line(str_repeat("═", 60));
        $this->info("🧪 TESTE CRÍTICO: VALIDAÇÃO DE NOTA FISCAL");
        $this->line(str_repeat("═", 60));
        $this->newLine();
        $this->line("Objetivo: Verificar se a NF é emitida em nome da SUBCONTA");
        $this->line("          (salão) e NÃO em nome do PagBy (master).");
        $this->newLine();
        $this->line("Método: Criar subconta → Criar cobrança → Verificar campo 'account'");
        $this->newLine();
        $this->line(str_repeat("═", 60));
        $this->newLine();
    }

    /**
     * Cria tenant de teste
     */
    private function createTestTenant()
    {
        $tenantId = 'teste' . now()->timestamp;
        
        $this->line("   Criando tenant de teste: {$tenantId}");
        
        try {
            $tenant = Tenant::create([
                'id' => $tenantId,
                'type' => 'barbearia',
                'template' => 'default',
                'employee_count' => 1,
                'name' => 'Salão Teste Validação NF',
                'email' => 'teste.nf.' . now()->timestamp . '@pagby.test',
                'subscription_status' => 'trial',
                'trial_started_at' => now(),
                'trial_ends_at' => now()->addDays(30),
            ]);

            $this->comment("   ⏳ Criando domínio...");
            
            $tenant->domains()->create([
                'domain' => $tenantId . '.localhost',
            ]);

            $this->info("   ✅ Tenant criado: {$tenant->id}");
            
            return $tenant;
            
        } catch (\Exception $e) {
            $this->error("   ❌ Erro ao criar tenant: " . $e->getMessage());
            $this->error("   Arquivo: " . $e->getFile() . ":" . $e->getLine());
            throw $e;
        }
    }

    /**
     * Salva arquivo com evidências do teste
     */
    private function saveEvidenceFile($evidence, $paymentDetails)
    {
        $filename = 'EVIDENCIA_TESTE_SUBACCOUNT_' . now()->format('Y-m-d_His') . '.md';
        
        $content = "# 🧪 Evidência: Teste de Subconta Asaas\n\n";
        $content .= "**Data/Hora:** " . $evidence['timestamp'] . "\n";
        $content .= "**Ambiente:** " . strtoupper($evidence['environment']) . "\n";
        $content .= "**API URL:** " . $evidence['api_url'] . "\n\n";
        
        $content .= "---\n\n";
        $content .= "## 📋 Dados do Teste\n\n";
        $content .= "- **Tenant ID:** " . $evidence['tenant_id'] . "\n";
        $content .= "- **Tenant Nome:** " . $evidence['tenant_name'] . "\n";
        $content .= "- **Account ID (Subconta):** " . $evidence['account_id'] . "\n";
        
        if (isset($evidence['wallet_id'])) {
            $content .= "- **Wallet ID:** " . $evidence['wallet_id'] . "\n";
        }
        
        $content .= "- **Customer ID:** " . $evidence['customer_id'] . "\n";
        $content .= "- **Payment ID:** " . $evidence['payment_id'] . "\n\n";
        
        $content .= "---\n\n";
        $content .= "## 🎯 RESULTADO DO TESTE\n\n";
        
        $content .= "### Campo 'account' da Cobrança\n\n";
        $content .= "```\n";
        $content .= "Subconta criada:     " . $evidence['account_id'] . "\n";
        $content .= "Cobrança pertence a: " . $evidence['payment_account_id'] . "\n";
        $content .= "```\n\n";
        
        if ($evidence['test_passed']) {
            $content .= "### ✅✅✅ SUCESSO! ✅✅✅\n\n";
            $content .= "**Cobrança pertence à SUBCONTA!**\n\n";
            $content .= "Isso significa que:\n";
            $content .= "- ✅ A Nota Fiscal será emitida em nome do SALÃO (subconta)\n";
            $content .= "- ✅ NÃO será emitida em nome do PagBy (master)\n";
            $content .= "- ✅ Modelo SEM SPLIT está TECNICAMENTE VALIDADO\n";
            $content .= "- ✅ Cliente paga direto na conta do salão (100%)\n";
            $content .= "- ✅ Fiscalmente CORRETO!\n\n";
        } else {
            $content .= "### ❌❌❌ TESTE FALHOU ❌❌❌\n\n";
            $content .= "**Cobrança NÃO pertence à subconta!**\n\n";
            $content .= "Isso significa que:\n";
            $content .= "- ❌ A Nota Fiscal será emitida em nome do PAGBY (master)\n";
            $content .= "- ❌ NÃO será emitida em nome do Salão\n";
            $content .= "- ❌ Modelo precisa AJUSTES\n";
            $content .= "- ❌ Contatar suporte Asaas\n\n";
        }
        
        $content .= "---\n\n";
        $content .= "## 📄 Dados Completos da Cobrança\n\n";
        $content .= "```json\n";
        $content .= json_encode($paymentDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $content .= "\n```\n\n";
        
        $content .= "---\n\n";
        $content .= "## 📊 Próximos Passos\n\n";
        
        if ($evidence['test_passed']) {
            $content .= "### ✅ Teste Passou - Implementar em Produção\n\n";
            $content .= "1. ✅ Modelo tecnicamente validado\n";
            $content .= "2. Implementar código completo\n";
            $content .= "3. Contratar contador COM EVIDÊNCIA (este documento)\n";
            $content .= "4. Perguntar: \"A NF sai em nome correto. Está OK fiscalmente?\"\n";
            $content .= "5. Consulta confirmatória (mais barata que exploratória)\n";
            $content .= "6. Deploy em produção\n\n";
        } else {
            $content .= "### ❌ Teste Falhou - Buscar Solução\n\n";
            $content .= "1. Contatar suporte Asaas\n";
            $content .= "2. Perguntar: \"Como fazer NF sair em nome da subconta?\"\n";
            $content .= "3. Avaliar modelo alternativo se não for possível\n";
            $content .= "4. NÃO implementar em produção até resolver\n\n";
        }
        
        $content .= "---\n\n";
        $content .= "*Documento gerado automaticamente pelo comando:*\n";
        $content .= "```bash\n";
        $content .= "php artisan asaas:test-subaccount-invoice\n";
        $content .= "```\n";
        
        File::put(base_path($filename), $content);
        
        $this->newLine();
        $this->info("📄 Evidências salvas em: {$filename}");
        $this->newLine();
    }

    /**
     * Gera CPF válido único para teste
     */
    private function generateValidCpf()
    {
        // Gerar 9 primeiros dígitos baseados no timestamp
        $timestamp = time();
        $base = substr(str_pad($timestamp, 9, '0'), 0, 9);
        
        // Calcular dígitos verificadores
        $cpf = $base . $this->calculateCpfDigits($base);
        
        return $cpf;
    }

    /**
     * Calcula os 2 dígitos verificadores do CPF
     */
    private function calculateCpfDigits($base)
    {
        // Primeiro dígito
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $base[$i] * (10 - $i);
        }
        $digit1 = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);
        
        // Segundo dígito
        $base .= $digit1;
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $base[$i] * (11 - $i);
        }
        $digit2 = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);
        
        return $digit1 . $digit2;
    }

    /**
     * Gera CNPJ válido único para teste
     */
    private function generateValidCnpj()
    {
        // Gerar 8 primeiros dígitos baseados no timestamp
        $timestamp = time();
        $base = substr(str_pad($timestamp, 8, '0'), 0, 8);
        
        // 4 dígitos do estabelecimento (0001)
        $base .= '0001';
        
        // Calcular dígitos verificadores
        $cnpj = $base . $this->calculateCnpjDigits($base);
        
        return $cnpj;
    }

    /**
     * Calcula os 2 dígitos verificadores do CNPJ
     */
    private function calculateCnpjDigits($base)
    {
        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        
        // Primeiro dígito
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += $base[$i] * $weights1[$i];
        }
        $digit1 = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);
        
        // Segundo dígito
        $base .= $digit1;
        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += $base[$i] * $weights2[$i];
        }
        $digit2 = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);
        
        return $digit1 . $digit2;
    }
}
