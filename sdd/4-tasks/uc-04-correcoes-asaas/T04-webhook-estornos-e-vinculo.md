# T04 — Estornos parciais e vínculo do webhook

> Responsável: agy, estágio 5. Status: CONCLUÍDA. Critérios: AC-4, AC-5.
> Spec: `sdd/2-specs/uc-04-correcoes-asaas.md`
> Plan: `sdd/3-plans/PLAN-uc-04-correcoes-asaas.md` §4
> Dependências: T03 concluída; executar sequencialmente pelo índice.

## 1. Descrição e objetivo

Estornos parciais e vínculo do webhook, conforme a revisão de 29/09 e o contrato detalhado abaixo. Não criar migrations ou implementar CODEX-01/03.

## 2. Justificativa técnica

Atende AC-4, AC-5 e PLAN §4. Reutilizar os componentes existentes reduz o risco de divergência com o checkout atual. As complementações Codex abaixo resolvem lacunas de integração da revisão e prevalecem nesses pontos.

## 3. Critérios de pronto

- [x] Todos os comportamentos e cenários abaixo foram verificados.
- [x] Regressão mínima reproduziu o defeito antes e passou após a correção (quando aplicável).
- [x] Executar testes focados via `rtk ./vendor/bin/sail test`; registrar resultado real e impedimentos.
- [x] Preservar alterações locais alheias, dados e testes de outros agentes.
- [x] Sem migrations, alterações de ADR pelo Worker ou ampliação de escopo.

## 4. Arquivos afetados

- `app/Http/Controllers/AsaasWebhookController.php`
- `app/Services/AsaasService.php`
- Regressões Worker: `tests/Feature/CheckoutAsaas/T04Test.php` (criar ou ampliar equivalente existente sem duplicar).

## 5. Contrato de implementação e verificação

### Complementações do plano (ler primeiro)

— AC-4/5 / T04

Manter autenticação por token e reconsulta como fonte da verdade. Antes de applyStatus, exigir referência exata, PIX, não deleted e centavos iguais ao total local. Divergência: log com IDs/campo, 200, zero mutações; não registrar token, CPF ou payload integral.

Para status financeiro pago, somar cada refund DONE em centavos. Soma positiva menor que value define PartiallyRefunded; soma maior/igual define Refunded. Não alterar total_amount. grantsAccess aciona concessão inclusive quando a primeira entrega já é parcial; Refunded revoga; manter log parcial e paid_at preenchido apenas se nulo. REFUND_REQUESTED/IN_PROGRESS continuam sem mapeamento. Atualizar contrato PHPDoc de getPayment com billingType, deleted e refunds.

Cobrir cada divergência isoladamente, inclusive referência e deleted, com HTTP 200 e zero mutações; token inválido rejeitado; status desconhecido preservado; primeira entrega parcial concede acesso. Nenhuma inbox, migration ou resposta 200 incondicional em falha interna.

### Requisitos da revisão (transcrição para execução autônoma)

Estorno parcial nunca é reconhecido · **CONFIRMADO** · Severidade: Média (conciliação/relatório; o acesso já é mantido) · **agy**

**Evidência (MCP):**
- O enum `PaymentGetResponsePaymentStatus` **não tem** `PARTIALLY_REFUNDED` nem `CANCELLED`. Os valores são: `PENDING`, `RECEIVED`, `CONFIRMED`, `OVERDUE`, `REFUNDED`, `RECEIVED_IN_CASH`, `REFUND_REQUESTED`, `REFUND_IN_PROGRESS`, `CHARGEBACK_*`, `DUNNING_*` e `AWAITING_RISK_ANALYSIS`.
- Em estorno parcial, a cobrança continua `RECEIVED`/`CONFIRMED` e só o evento `PAYMENT_PARTIALLY_REFUNDED` e o array `refunds[]` indicam o estorno.
- Por isso `OrderStatus::PartiallyRefunded` é inalcançável (`AsaasWebhookController.php:65,77`).
- A documentação `estornos` pede: somar só os itens `DONE`, ignorar `PENDING`/`CANCELLED` e nunca substituir o valor original.

**Correção:**
1. `AsaasService::getPayment()`: ampliar o `@return` para incluir `billingType`, `deleted` e `refunds?: array<int, array{status: string, value: float}>`.
2. No webhook, depois de `tryFromAsaas`, se o status for `isPaid()`:
   - `refundedCents` = soma, em centavos, dos `refunds` com `status === 'DONE'`;
   - `valueCents` = `payment.value` em centavos;
   - se `0 < refundedCents < valueCents`, o status final é `PartiallyRefunded`;
   - se `refundedCents >= valueCents`, o status final é `Refunded`.
3. Ramificação do webhook:
   - `grantsAccess()` → `GrantCourseAccessAction`. Continua idempotente e cobre o caso de o primeiro webhook processado já chegar parcialmente estornado.
   - `Refunded` → `RevokeCourseAccessAction`.
   - Remover o ramo exclusivo de `PartiallyRefunded` (`:77-81`), porque ele passa a ser coberto pelo primeiro. Manter o `Log::info` de estorno parcial dentro do novo fluxo.
4. Os status `REFUND_REQUESTED`/`REFUND_IN_PROGRESS` continuam sem mapeamento. O acesso fica mantido até `REFUNDED`, e esse é o comportamento desejado. Não mapear.
5. Sem migration. Não persistir o valor estornado agora. Se o Codex quiser a conciliação exposta no `OrderResource`, isso vira uma spec própria.

**Teste mínimo:**
- `refunds` com um `DONE` parcial vira `partially_refunded` e a matrícula continua ativa.
- `refunds` com `PENDING` e `CANCELLED` deixa o pedido em `received`.
- Parciais `DONE` somando o valor total viram `refunded` e a matrícula é revogada.

**Validação complementar (usuário/Grok):** confirmar o payload real no Sandbox (recipe "Estorno de cobranças em Sandbox").

Severidade: Média · agy.** Depois de `getPayment()` (`AsaasWebhookController.php:63`), antes de `applyStatus`, validar:
- `externalReference === $order->order_number`;
- `billingType === 'PIX'`;
- `deleted !== true`;
- valor em centavos igual a `total_amount` em centavos.

Se alguma divergir: `Log::error` com `order_number`, `payment_id` e o campo divergente, e responder **200 sem alterar o pedido**. Não responder 500 pelos motivos da C2b. Hoje o valor pode ser alterado no painel do Asaas (`PAYMENT_UPDATED`) e o acesso seria concedido mesmo assim.

**Teste mínimo:** payload reconsultado com valor diferente, ou com `billingType: BOLETO`, deixa o pedido inalterado, não cria matrícula e responde 200.

## 6. Limites da entrega

Implementar e escrever apenas regressões mínimas do Worker; a suíte adversarial pertence ao Grok. Nenhuma task aprova produção. Linhas citadas na revisão são históricas: localizar os símbolos no código atual. Executar PHP/Artisan/testes somente via Sail com RTK. Não comitar `.env` ou registrar segredos.
