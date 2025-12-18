<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Comanda extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'appointment_id',
        'numero_comanda',
        'cliente_nome',
        'cliente_telefone',
        'funcionario_id',
        'status',
        'data_abertura',
        'data_fechamento',
        'subtotal_servicos',
        'subtotal_produtos',
        'desconto_servicos',
        'desconto_produtos',
        'total_geral',
        'observacoes'
    ];

    protected $casts = [
        'data_abertura' => 'datetime',
        'data_fechamento' => 'datetime',
        'subtotal_servicos' => 'decimal:2',
        'subtotal_produtos' => 'decimal:2',
        'desconto_servicos' => 'decimal:2',
        'desconto_produtos' => 'decimal:2',
        'total_geral' => 'decimal:2'
    ];

    // Relacionamentos
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'funcionario_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function comandaServicos(): HasMany
    {
        return $this->hasMany(ComandaServico::class);
    }

    public function comandaProdutos(): HasMany
    {
        return $this->hasMany(ComandaProduto::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    // Scopes
    public function scopeAbertas($query)
    {
        return $query->where('status', 'Aberta');
    }

    public function scopeFinalizadas($query)
    {
        return $query->where('status', 'Finalizada');
    }

    public function scopeParaBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeHoje($query)
    {
        return $query->whereDate('data_abertura', today());
    }

    // Métodos auxiliares
    public function recalcularTotais(): void
    {
        // Recalcular subtotal de serviços
        $this->subtotal_servicos = $this->comandaServicos()->sum('subtotal');
        // Recalcular subtotal de produtos
        $this->subtotal_produtos = $this->comandaProdutos()->sum('subtotal');
        // Calcular descontos
        $descontoServicos = $this->desconto_servicos ?? 0;
        $descontoProdutos = $this->desconto_produtos ?? 0;
        $valorDescontoServicos = ($descontoServicos > 0) ? ($this->subtotal_servicos * ($descontoServicos / 100)) : 0;
        $valorDescontoProdutos = ($descontoProdutos > 0) ? ($this->subtotal_produtos * ($descontoProdutos / 100)) : 0;
        // Calcular total geral
        $this->total_geral = ($this->subtotal_servicos - $valorDescontoServicos) + ($this->subtotal_produtos - $valorDescontoProdutos);
        $this->save();
    }

    public function adicionarServico($serviceId, $funcionarioId, $quantidade = 1, $precoUnitario = null, $observacoes = null): void
    {
        $service = Service::find($serviceId);
        $preco = $precoUnitario ?? $service->getPriceForBranch($this->branch_id);
        
        ComandaServico::create([
            'comanda_id' => $this->id,
            'service_id' => $serviceId,
            'funcionario_id' => $funcionarioId,
            'quantidade' => $quantidade,
            'preco_unitario' => $preco,
            'subtotal' => $quantidade * $preco,
            'observacoes' => $observacoes
        ]);

        $this->recalcularTotais();
    }

    public function adicionarProduto($estoqueId, $quantidade, $precoUnitario = null, $observacoes = null): void
    {
        $estoque = Estoque::find($estoqueId);
        
        // Verificar se há estoque suficiente
        if ($estoque->quantidade_atual < $quantidade) {
            throw new \Exception("Estoque insuficiente. Disponível: {$estoque->quantidade_atual}");
        }

        $preco = $precoUnitario ?? $estoque->preco_unitario;
        
        ComandaProduto::create([
            'comanda_id' => $this->id,
            'estoque_id' => $estoqueId,
            'quantidade' => $quantidade,
            'preco_unitario' => $preco,
            'subtotal' => $quantidade * $preco,
            'observacoes' => $observacoes
        ]);

        // Reduzir quantidade do estoque (ao finalizar a comanda)
        if ($this->status === 'Finalizada') {
            $estoque->decrement('quantidade_atual', $quantidade);
        }

        $this->recalcularTotais();
    }

    public function finalizar(): void
    {
        // Reduzir estoque dos produtos vendidos
        foreach ($this->comandaProdutos as $comandaProduto) {
            $comandaProduto->estoque->decrement('quantidade_atual', $comandaProduto->quantidade);
        }

        $this->update([
            'status' => 'Finalizada',
            'data_fechamento' => now()
        ]);
    }

    public function cancelar(): void
    {
        $this->update([
            'status' => 'Cancelada',
            'data_fechamento' => now()
        ]);
    }

    // Geração automática do número da comanda
    public static function gerarNumeroComanda($branchId): string
    {
        $hoje = today()->format('Ymd');
        $ultimaComanda = self::where('branch_id', $branchId)
            ->where('numero_comanda', 'like', "{$branchId}-{$hoje}-%")
            ->orderBy('numero_comanda', 'desc')
            ->first();

        if ($ultimaComanda) {
            $ultimoNumero = (int) substr($ultimaComanda->numero_comanda, -3);
            $proximoNumero = str_pad($ultimoNumero + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $proximoNumero = '001';
        }

        return "{$branchId}-{$hoje}-{$proximoNumero}";
    }

    // Criar comanda a partir de agendamento
    public static function criarDeAgendamento(Appointment $appointment, $observacoes = null): self
    {
        // Verificações de segurança
        if (!$appointment->customer) {
            throw new \Exception("Agendamento sem cliente definido");
        }

        if (!$appointment->employee) {
            throw new \Exception("Agendamento sem funcionário definido");
        }

        if (!$appointment->branch) {
            throw new \Exception("Agendamento sem filial definida");
        }

        $comanda = self::create([
            'branch_id' => $appointment->branch_id,
            'appointment_id' => $appointment->id,
            'numero_comanda' => self::gerarNumeroComanda($appointment->branch_id),
            'cliente_nome' => $appointment->customer->name,
            'cliente_telefone' => $appointment->customer->phone ?? $appointment->customer->telefone ?? null,
            'funcionario_id' => $appointment->employee_id,
            'status' => 'Aberta',
            'data_abertura' => now(),
            'observacoes' => $observacoes
        ]);

        // Adicionar serviços do agendamento à comanda
        if ($appointment->services) {
            $servicosAdicionados = 0;
            
            // Tentar primeiro como IDs numéricos separados por vírgula
            $servicosIds = explode(',', $appointment->services);
            $todosNumericos = true;
            
            foreach ($servicosIds as $serviceId) {
                $serviceId = trim($serviceId);
                if (!is_numeric($serviceId)) {
                    $todosNumericos = false;
                    break;
                }
            }
            
            if ($todosNumericos && count($servicosIds) > 0 && trim($servicosIds[0]) !== '') {
                // Processar como IDs - usar preço proporcional do agendamento
                $totalServicos = count($servicosIds);
                $precoMedioUnitario = $totalServicos > 0 ? $appointment->total / $totalServicos : 0;
                
                foreach ($servicosIds as $serviceId) {
                    $serviceId = trim($serviceId);
                    if ($serviceId && is_numeric($serviceId)) {
                        try {
                            $service = Service::find($serviceId);
                            if ($service) {
                                // Usar preço do agendamento dividido pelo número de serviços
                                // para manter consistência com o valor original
                                $comanda->adicionarServico(
                                    $serviceId, 
                                    $appointment->employee_id, 
                                    1, // quantidade
                                    $precoMedioUnitario, // usar preço do agendamento
                                    "Serviço do agendamento #{$appointment->id} - Preço original preservado (ID {$serviceId})"
                                );
                                $servicosAdicionados++;
                                Log::info("Serviço ID {$serviceId} ({$service->service}) adicionado à comanda com preço do agendamento: R$ {$precoMedioUnitario}");
                            }
                        } catch (\Exception $e) {
                            Log::warning("Erro ao adicionar serviço {$serviceId} à comanda do agendamento {$appointment->id}: " . $e->getMessage());
                        }
                    }
                }
            } else {
                // Processar como nomes separados por separadores
                $separadores = ['/', ',', ';'];
                $servicosNomes = [];
                
                foreach ($separadores as $sep) {
                    if (strpos($appointment->services, $sep) !== false) {
                        $servicosNomes = explode($sep, $appointment->services);
                        break;
                    }
                }
                
                // Se não encontrou separador, trata como um serviço único
                if (empty($servicosNomes)) {
                    $servicosNomes = [$appointment->services];
                }
                
                // Calcular preço médio por serviço baseado no total do agendamento
                $totalServicos = count($servicosNomes);
                $precoMedioUnitario = $totalServicos > 0 ? $appointment->total / $totalServicos : 0;
                
                foreach ($servicosNomes as $servicoNome) {
                    $servicoNome = trim($servicoNome);
                    if ($servicoNome) {
                        try {
                            // Primeiro, tentar encontrar o serviço pelo nome E preço correspondente
                            $servicosComNome = Service::where('service', 'like', "%{$servicoNome}%")->get();
                            
                            $service = null;
                            
                            // Se há múltiplos serviços com o mesmo nome, escolher pelo preço mais próximo
                            if ($servicosComNome->count() > 1) {
                                $melhorMatch = null;
                                $menorDiferenca = PHP_FLOAT_MAX;
                                
                                foreach ($servicosComNome as $servicoCandidate) {
                                    $precoServico = (float) $servicoCandidate->price;
                                    $diferenca = abs($precoServico - $precoMedioUnitario);
                                    
                                    if ($diferenca < $menorDiferenca) {
                                        $menorDiferenca = $diferenca;
                                        $melhorMatch = $servicoCandidate;
                                    }
                                }
                                
                                $service = $melhorMatch;
                                Log::info("Múltiplos serviços '{$servicoNome}' encontrados. Selecionado ID {$service->id} (R$ {$service->price}) por ter preço mais próximo ao agendamento (R$ {$precoMedioUnitario})");
                            } else {
                                // Se há apenas um serviço com esse nome, usar ele
                                $service = $servicosComNome->first();
                            }
                            
                            if ($service) {
                                $comanda->adicionarServico(
                                    $service->id, 
                                    $appointment->employee_id, 
                                    1, // quantidade
                                    $precoMedioUnitario, // usar preço do agendamento
                                    "Serviço do agendamento #{$appointment->id}: {$servicoNome} - Preço original preservado (ID {$service->id})"
                                );
                                $servicosAdicionados++;
                            }
                        } catch (\Exception $e) {
                            Log::warning("Erro ao adicionar serviço '{$servicoNome}' à comanda do agendamento {$appointment->id}: " . $e->getMessage());
                        }
                    }
                }
            }

            // Se nenhum serviço foi adicionado, adiciona observação
            if ($servicosAdicionados === 0 && $appointment->services) {
                $comanda->update([
                    'observacoes' => ($observacoes ?? '') . "\nNOTA: Serviços do agendamento não puderam ser adicionados automaticamente: {$appointment->services}"
                ]);
            }
        }

        return $comanda;
    }

    // Atributos calculados
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'Aberta' => '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Aberta</span>',
            'Finalizada' => '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Finalizada</span>',
            'Cancelada' => '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Cancelada</span>',
            default => '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Desconhecido</span>'
        };
    }

    public function getTempoAbertoAttribute(): string
    {
        if ($this->status === 'Aberta') {
            $diff = $this->data_abertura->diffForHumans(now(), true);
            return "há {$diff}";
        }
        
        if ($this->data_fechamento) {
            $diff = $this->data_abertura->diffForHumans($this->data_fechamento, true);
            return $diff;
        }

        return '-';
    }
}