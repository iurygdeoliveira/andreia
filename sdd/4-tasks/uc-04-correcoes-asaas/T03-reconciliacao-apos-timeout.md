# T03 — Reconciliação após timeout

> Responsável: agy, estágio 5. Status: CONCLUÍDA. Critérios: AC-3.
> Spec: `sdd/2-specs/uc-04-correcoes-asaas.md`
> Plan: `sdd/3-plans/PLAN-uc-04-correcoes-asaas.md` §3
> Dependências: T02 concluída; executar sequencialmente pelo índice.

## 1. Descrição e objetivo

Reconciliação após timeout, conforme a revisão de 29/09 e o contrato detalhado abaixo. Não criar migrations ou implementar CODEX-01/03.

## 2. Justificativa técnica

Atende AC-3 e PLAN §3. Reutilizar os componentes existentes reduz o risco de divergência com o checkout atual. As complementações Codex abaixo resolvem lacunas de integração da revisão e prevalecem nesses pontos.

## 3. Critérios de pronto

- [x] Todos os comportamentos e cenários abaixo foram verificados.
- [x] Regressão mínima reproduziu o defeito antes e passou após a correção (quando aplicável).
- [x] Executar testes focados via `rtk ./vendor/bin/sail test`; registrar resultado real e impedimentos.
- [x] Preservar alterações locais alhehas, dados e testes de outros agentes.
- [x] Sem migrations, alterações de ADR pelo Worker ou ampliação de escopo.

## 4. Arquivos afetados

- `app/Services/CheckoutService.php`
- `app/Services/AsaasService.php`
- Regressões Worker: `tests/Feature/CheckoutAsaas/T03Test.php` (criar ou ampliar equivalente existente sem duplicar).

## 5. Contrato de implementação e verificação

### Complementações do plano (ler primeiro)

A revisão grava posse só no sucesso, mas F-02 exige recuperar um pedido cuja criação no Asaas falhou. Acrescentar um quinto argumento opcional onOrderCreated, callable recebendo Order, padrão null. Checkout passa uma closure do servidor que acrescenta o ID à lista de sessão (dez mais recentes, únicos). O serviço chama essa closure após confirmar a transação local de criação e **antes de qualquer HTTP**. A resposta de erro tratado preserva a sessão. A closure não é propriedade pública Livewire. Após sucesso, atualizar a mesma lista sem duplicar.

Isso preserva o retorno Order/null e evita acoplar CheckoutService diretamente à sessão. Chamadores sem callback continuam válidos, mas devem gerenciar explicitamente os IDs se quiserem retomar. A garantia cobre falhas HTTP tratadas; perda abrupta do processo/sessão não constitui durabilidade e não deve ser descrita como resolvida.

### 3.3 Fluxo dentro do lock

Cache é apenas indicação de candidato. Pedido órfão nunca sai por um retorno antecipado: precisa passar pela reconciliação. Dentro do lock, reler candidato e status antes de decidir criar/cobrar; não reutilizar um snapshot antigo da consulta. Pedidos com payment_id preservam o filtro de validade de QR existente. Órfãos precisam ser Pending, próprios, compatíveis e criados nos últimos 30 minutos.

Novo pedido: criar transação local → callback → cliente/cobrança. Órfão: GET por externalReference **antes de qualquer POST**, inclusive de cliente → uma cobrança compatível vincula seus IDs → zero permite cliente/cobrança com a referência original → qualquer ambiguidade/falha interrompe. Comparar centavos arredondados a inteiros. Não confiar em unicidade remota ou inventar header de idempotência.

AsaasService retorna os itens não deleted da listagem. Fazer uma consulta com limit máximo; se hasMore indicar lista incompleta, falhar para reconciliação manual, sem POST. Essa opção conservadora impede decidir unicidade pela primeira página e mantém o orçamento de chamadas do lock. Não adicionar paginação ilimitada ou retries sob o lock.

Timeout HTTP 10 s/connect 5 s; lock 75 s, espera 5 s. Tratar esses valores como configuração exigida, não prova absoluta de ausência de corridas. Depois de liberar o lock, recuperar QR e retornar pedido atualizado. Erro de QR não perde cobrança. Estender os testes a cache de órfão, GET falho, divergência e lista incompleta.

Pré-condição: T02 já registra posse antes do HTTP. Testar duas requisições reais de sessão: primeira falha, segunda recupera um único pedido; também cache de órfão, GET falho, cobrança divergente e hasMore. Não usar lista de IDs fabricada no teste como substituto da integração de sessão.

### Requisitos da revisão (transcrição para execução autônoma)

Timeout e concorrência podem gerar cobranças duplicadas · **CONFIRMADO com números** · Severidade: Alta · **agy**

**Evidência:**
- O lock tem TTL de 15 s (`CheckoutService.php:126`), mas até 4 chamadas HTTP sequenciais de até 35 s cada (`connectTimeout(5)` + `timeout(30)`, `AsaasService.php:366-367`) rodam dentro dele. São elas: `findCustomer`, `createCustomer`, `createPixCharge` e `getPixQrCode`.
- Quando há erro, o pedido fica `Pending` sem `payment_id`. A próxima submissão não o encontra, porque `:84` exige `whereNotNull('payment_id')`, e cria **outro pedido com outra `externalReference`**, mesmo que a primeira cobrança tenha sido criada no Asaas.

**Correção:**
1. `AsaasService::client()`: `timeout(10)`, mantendo `connectTimeout(5)`. Cada chamada leva no máximo cerca de 15 s.
2. Tirar `recoverPixQrCode()` de dentro do lock e chamá-lo depois que o lock for liberado. Ele é idempotente, e `OrderPaymentController::show` já o chama de novo.
3. Lock TTL de **75 s**. O pior caso dentro do lock tem 4 chamadas (`findCustomer` + `createCustomer` + `findPaymentsByExternalReference` + `createPixCharge`), cerca de 60 s. Manter `block(5)`.
4. Criar `AsaasService::findPaymentsByExternalReference(string $ref): array` com `GET /payments?externalReference=...`, descartando itens com `deleted === true`.
5. Retomar **pedido órfão**. A consulta de candidatos (já escopada pelo F-01) passa a aceitar também `Pending` **sem** `payment_id` criado há no máximo 30 minutos. Para esse pedido, **antes** de qualquer POST, chamar `findPaymentsByExternalReference($order->order_number)` e seguir esta tabela:

   | Resultado | Ação |
   |---|---|
   | 1 cobrança, `billingType === 'PIX'` e valor em centavos igual a `total_amount` em centavos | Vincular `payment_id` e `asaas_customer_id`, sem POST |
   | 1 cobrança divergente, ou mais de 1 | `Log::error` com `order_number` e IDs, e retornar `null` para reconciliação manual. **Nunca** escolher uma delas |
   | 0 cobranças | POST com a **mesma** `externalReference`. Isso só ocorre nesta tentativa posterior, iniciada pelo usuário |
   | GET falhou | Retornar `null` |

6. Na requisição que teve timeout ou erro, manter o comportamento atual (`return null`) **sem** retry automático.
7. Comparar valores sempre em centavos inteiros, `(int) round($reais * 100)`, porque o `MoneyCast` expõe reais.

**Não fazer:**
- assumir um header `Idempotency-Key` na criação de cobrança (não documentado);
- tratar `externalReference` como único do lado do Asaas.

**Teste mínimo** (`Http::fake` com sequência):
- (a) O 1º POST lança `ConnectionException`. A 2ª submissão da mesma sessão faz GET, encontra 1 cobrança, vincula e faz **0** POSTs novos. Resultado: 1 pedido.
- (b) Sequência com GET vazio: 1 POST na 2ª tentativa, com a mesma `externalReference`.
- (c) GET com 2 cobranças: retorna `null` e não faz POST.

## 6. Limites da entrega

Implementar e escrever apenas regressões mínimas do Worker; a suíte adversarial pertence ao Grok. Nenhuma task aprova produção. Linhas citadas na revisão são históricas: localizar os símbolos no código atual. Executar PHP/Artisan/testes somente via Sail com RTK. Não comitar `.env` ou registrar segredos.
