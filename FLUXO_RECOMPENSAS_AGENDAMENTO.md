# Fluxo de Recompensas no Agendamento

## ✅ Como Funciona (Implementação Atual)

### Cenário 1: Cliente SEM recompensas pré-existentes

1. Cliente acessa página de agendamento
2. **MinhasRecompensas carrega:** `FidelidadeReward::where('status', 'ativo')` → **Resultado: vazio**
3. Cliente seleciona profissional, serviço, horário
4. Cliente seleciona produtos para comprar
5. **Nenhuma recompensa disponível para usar** (correto!)
6. Cliente clica em "Confirmar"

**Ordem de execução no `confirmTime()`:**
```
a) Linhas 602-638: Verifica se há recompensa selecionada → NÃO HÁ
b) Linha 644: Cria Appointment com valor CHEIO (sem desconto)
c) Linha 660: Cria Comanda do agendamento
d) Linhas 682-697: Adiciona produtos à comanda
e) Observer dispara: cria NOVA recompensa baseada nos produtos
```

**Resultado:** 
- ✅ Appointment criado com valor CHEIO
- ✅ Nova recompensa criada (20% do valor dos produtos)
- ✅ Nova recompensa fica disponível apenas para PRÓXIMO agendamento

---

### Cenário 2: Cliente COM recompensa pré-existente

1. Cliente já tem R$ 44,33 em créditos (de compra anterior)
2. **MinhasRecompensas carrega:** Mostra a recompensa de R$ 44,33 ✅
3. Cliente agenda serviço de R$ 50,00
4. Cliente ESCOLHE usar a recompensa
5. Cliente compra produto de R$ 100,00 junto

**Ordem de execução:**
```
a) Linhas 602-638: Aplica desconto da recompensa PRÉ-EXISTENTE (R$ 44,33)
b) Linha 644: Cria Appointment com R$ 5,67 (50 - 44,33)
c) Linha 625: Marca recompensa antiga como 'usado'
d) Linha 660: Cria Comanda
e) Linha 667: Registra uso na comanda
f) Linhas 682-697: Adiciona produto de R$ 100 à comanda
g) Observer dispara: cria NOVA recompensa de R$ 25,00 (25% de R$ 100)
```

**Resultado:**
- ✅ Appointment com desconto da recompensa ANTIGA
- ✅ Recompensa antiga marcada como 'usado'
- ✅ NOVA recompensa de R$ 25 criada (disponível só no próximo agendamento)

---

## 🔒 Garantias do Sistema

### 1. Separação temporal garantida por:
```php
// app/Livewire/Cliente/MinhasRecompensas.php (linha 27-32)
FidelidadeReward::where('user_id', auth()->id())
    ->where('status', 'ativo')  // ← Apenas recompensas JÁ existentes
    ->where('validade', '>=', now())
    ->orderBy('created_at', 'desc')
    ->get()
```

Esta query roda no **mount()** do componente, ANTES de qualquer produto ser adicionado.

### 2. Observer cria recompensas APÓS produtos serem salvos:
```php
// app/Observers/ComandaProdutoObserver.php (linha 13)
public function created(ComandaProduto $comandaProduto): void
{
    // Só executa DEPOIS de ComandaProduto::create()
    // Linha 682 do MakeAppointment.php
}
```

### 3. Ordem cronológica no código:
```php
// MakeAppointment.php - confirmTime()
LINHA 602: Aplica recompensa PRÉ-EXISTENTE (se houver)
LINHA 644: Cria Appointment
LINHA 660: Cria Comanda
LINHA 682: ComandaProduto::create() ← SÓ AQUI o Observer dispara
```

---

## 📊 Exemplo Prático

**Dia 1 - Cliente compra R$ 200 em produtos:**
```
Entrada: 0 recompensas
Processo: Adiciona produtos à comanda
Observer: Cria recompensa de R$ 50 (25%)
Saída: 1 recompensa DISPONÍVEL para próximo uso
```

**Dia 7 - Cliente agenda corte R$ 40:**
```
Entrada: 1 recompensa de R$ 50 (do dia 1)
Seleção: Cliente escolhe usar a recompensa
Processo: Desconto de R$ 40 aplicado (serviço fica grátis)
Cliente compra: R$ 80 em produtos na mesma sessão
Saída: 
  - Serviço grátis (usou R$ 40 da recompensa antiga)
  - Sobram R$ 10 de crédito da recompensa antiga
  - Nova recompensa de R$ 16 criada (20% de R$ 80)
  - TOTAL disponível no próximo: R$ 10 + R$ 16 = R$ 26
```

---

## 🎯 Conclusão

✅ **O sistema JÁ funciona exatamente como solicitado:**
- Recompensas geradas por produtos NÃO são aplicadas no mesmo agendamento
- Recompensas só aparecem no MinhasRecompensas se já existiam ANTES do agendamento começar
- Observer cria novas recompensas APÓS finalizar o agendamento
- Novas recompensas ficam disponíveis apenas no PRÓXIMO agendamento

**Validade:** Todas as novas recompensas expiram em **30 dias**.
