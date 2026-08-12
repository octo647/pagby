<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Converte tabela payments do MercadoPago para Asaas
     * Adiciona campos novos, remove campos antigos
     */
    public function up(): void
    {
        // Se a tabela não existe, cria do zero
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('comanda_id')->nullable()->constrained()->onDelete('set null');
                $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('set null');
                $table->string('customer_id')->nullable()->comment('ID do cliente no Asaas');
                $table->string('asaas_payment_id')->unique()->comment('ID do pagamento no Asaas');
                $table->string('asaas_customer_id')->nullable()->comment('ID do customer no Asaas');
                $table->string('asaas_invoice_url')->nullable()->comment('URL do boleto/invoice');
                $table->string('asaas_invoice_number')->nullable()->comment('Número da NF');
                $table->decimal('amount', 10, 2)->comment('Valor total');
                $table->decimal('net_value', 10, 2)->nullable()->comment('Valor líquido (descontando taxas)');
                $table->string('billing_type')->comment('PIX, BOLETO, CREDIT_CARD, UNDEFINED');
                $table->text('description')->nullable();
                $table->date('due_date')->comment('Data de vencimento');
                $table->date('payment_date')->nullable()->comment('Data do pagamento');
                $table->date('estimated_credit_date')->nullable()->comment('Previsão de crédito');
                $table->enum('status', [
                    'pending', 'confirmed', 'received', 'received_in_cash',
                    'overdue', 'refunded', 'deleted', 'cancelled'
                ])->default('pending');
                $table->json('asaas_data')->nullable()->comment('Payload completo do Asaas');
                $table->string('external_reference')->nullable()->comment('Referência externa');
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Índices
                $table->index('asaas_customer_id');
                $table->index(['status', 'due_date']);
                $table->index('payment_date');
            });
            return;
        }

        // Se a tabela existe, converte MercadoPago → Asaas
        Schema::table('payments', function (Blueprint $table) {
            // Adicionar novos campos Asaas (IF NOT EXISTS para segurança)
            if (!Schema::hasColumn('payments', 'comanda_id')) {
                $table->foreignId('comanda_id')->nullable()->after('id')->constrained()->onDelete('set null');
            }
            
            if (!Schema::hasColumn('payments', 'customer_id')) {
                $table->string('customer_id')->nullable()->after('appointment_id')->comment('ID do cliente no Asaas');
            }
            
            if (!Schema::hasColumn('payments', 'asaas_payment_id')) {
                $table->string('asaas_payment_id')->unique()->after('customer_id')->comment('ID do pagamento no Asaas');
            }
            
            if (!Schema::hasColumn('payments', 'asaas_customer_id')) {
                $table->string('asaas_customer_id')->nullable()->after('asaas_payment_id')->comment('ID do customer no Asaas');
            }
            
            if (!Schema::hasColumn('payments', 'asaas_invoice_url')) {
                $table->string('asaas_invoice_url')->nullable()->after('asaas_customer_id')->comment('URL do boleto/invoice');
            }
            
            if (!Schema::hasColumn('payments', 'asaas_invoice_number')) {
                $table->string('asaas_invoice_number')->nullable()->after('asaas_invoice_url')->comment('Número da NF');
            }
            
            if (!Schema::hasColumn('payments', 'net_value')) {
                $table->decimal('net_value', 10, 2)->nullable()->after('amount')->comment('Valor líquido (descontando taxas)');
            }
            
            if (!Schema::hasColumn('payments', 'billing_type')) {
                $table->string('billing_type')->after('net_value')->comment('PIX, BOLETO, CREDIT_CARD, UNDEFINED');
            }
            
            if (!Schema::hasColumn('payments', 'due_date')) {
                $table->date('due_date')->nullable()->after('description')->comment('Data de vencimento');
            }
            
            if (!Schema::hasColumn('payments', 'payment_date')) {
                $table->date('payment_date')->nullable()->after('due_date')->comment('Data do pagamento');
            }
            
            if (!Schema::hasColumn('payments', 'estimated_credit_date')) {
                $table->date('estimated_credit_date')->nullable()->after('payment_date')->comment('Previsão de crédito');
            }
            
            if (!Schema::hasColumn('payments', 'asaas_data')) {
                $table->json('asaas_data')->nullable()->after('status')->comment('Payload completo do Asaas');
            }
            
            if (!Schema::hasColumn('payments', 'external_reference')) {
                $table->string('external_reference')->nullable()->after('asaas_data')->comment('Referência externa');
            }
            
            if (!Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('external_reference');
            }
            
            if (!Schema::hasColumn('payments', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }

            // Modificar campo status para enum (se ainda for varchar)
            DB::statement("ALTER TABLE payments MODIFY status ENUM('pending', 'confirmed', 'received', 'received_in_cash', 'overdue', 'refunded', 'deleted', 'cancelled') DEFAULT 'pending'");

            // Adicionar índices se não existirem
            if (!Schema::hasColumn('payments', 'asaas_customer_id')) {
                $table->index('asaas_customer_id');
            }
        });

        // Renomear/deprecar campos MercadoPago (opcional - pode manter para histórico)
        // Schema::table('payments', function (Blueprint $table) {
        //     $table->renameColumn('mp_payment_id', 'mp_payment_id_old');
        //     $table->renameColumn('mp_data', 'mp_data_old');
        // });
    }

    /**
     * Rollback não recomendado - perda de dados
     */
    public function down(): void
    {
        // Não implementar rollback para evitar perda acidental de dados
        // Em produção, fazer backup antes de executar
    }
};
