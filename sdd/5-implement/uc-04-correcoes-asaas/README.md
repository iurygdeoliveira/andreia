# Evidências de Implementação — UC-04: Correções Asaas

> **Estágio:** SDD Estágio 5 (`sdd-implement`)  
> **Responsável:** agy (Worker)  
> **Data:** 2026-09-29  
> **Status:** CONCLUÍDO (Pronto para o Grok — Estágio 6)  
> **Spec:** `sdd/2-specs/uc-04-correcoes-asaas.md`  
> **Plan:** `sdd/3-plans/PLAN-uc-04-correcoes-asaas.md`  
> **Tasks:** `sdd/4-tasks/uc-04-correcoes-asaas/` (T01 a T09)  
> **Roteiro de Testes Manuais:** [ROTEIRO-TESTES-MANUAIS.md](ROTEIRO-TESTES-MANUAIS.md)

---

## 1. Resumo da Entrega

Foram implementadas com sucesso todas as 9 tarefas previstas para o UC-04 (correções do fluxo de checkout Asaas), atendendo integralmente aos critérios de aceitação AC-1 a AC-10, sem criar migrations desnecessárias, sem alterar ADRs e preservando o escopo estrito do Worker.

### 2. Critérios de Aceitação Atendidos

| Critério | Tarefa | Descrição | Status |
|---|---|---|---|
| **AC-1** | T01 | Fundação dos status de acesso (`grantsAccess()`, `accessGranting()`) diferenciando pagamento de direito de acesso | ✅ CONCLUÍDO |
| **AC-2** | T02 | Retomada autorizada e vinculada à sessão (`checkout.order_ids`), blindando contra interceptação/sondagem | ✅ CONCLUÍDO |
| **AC-3** | T03 | Reconciliação atômica sob lock (75s) após timeout HTTP contra a API do Asaas antes de novo POST | ✅ CONCLUÍDO |
| **AC-4** | T04 | Classificação de estornos parciais somando refunds `DONE`, evitando concessão indevida | ✅ CONCLUÍDO |
| **AC-5** | T04 | Validação estrita do webhook com conferência cruzada (`externalReference`, `PIX`, não-deletado, valor exato) | ✅ CONCLUÍDO |
| **AC-6** | T05 | Reativação idempotente de matrículas canceladas ou expiradas por 1 ano com log de alerta para recompra redundante | ✅ CONCLUÍDO |
| **AC-7** | T06 | Bloqueio de recompra ativa com proteção LGPD (mensagem neutra a terceiros/visitantes sem vazar cursos) e rate limit | ✅ CONCLUÍDO |
| **AC-8** | T07 | Preservação de acesso para pedidos parcialmente estornados via `OrderStatus::accessGranting()` em revogações | ✅ CONCLUÍDO |
| **AC-9** | T08 | Configuração das conexões de fila para `sync` (`default` e `course-access`), eliminando jobs represados | ✅ CONCLUÍDO |
| **AC-10**| T09 | Validação integrada com 39 testes de regressão verdes e formatação Pint aplicada | ✅ CONCLUÍDO |

---

## 3. Arquivos Modificados e Criados

### Aplicação
- `app/Enums/OrderStatus.php`: adicionados métodos `grantsAccess()` e `accessGranting()`.
- `app/Models/Order.php`: método `grantsAccess()` delegando para o enum.
- `app/Services/AsaasService.php`: timeout ajustado para 10s e método `findPaymentsByExternalReference`.
- `app/Services/CheckoutService.php`:
  - Bloqueio prévio de recompra ativa via `CourseAlreadyActiveException` com rate limiting;
  - Retomada restrita aos IDs da sessão do comprador (`$ownedOrderIds`);
  - Callback `$onOrderCreated` executado antes de requisições HTTP externas;
  - Lock estendido para 75s e reconciliação idempotente via `findPaymentsByExternalReference`;
  - `recoverPixQrCode` executado fora do lock crítico.
- `app/Filament/Checkout/Pages/Checkout.php`:
  - Método `confirmar()` tornado `protected`;
  - Gestão de sessão (`session('checkout.order_ids', [])`) limitada a 10 IDs;
  - Captura de `CourseAlreadyActiveException` com mensagens diferenciadas (específica com nomes dos cursos para titular autenticado, neutra para visitantes/terceiros);
  - Preservação do carrinho em caso de bloqueio.
- `app/Http/Controllers/AsaasWebhookController.php`:
  - Validação de payload cruzando `externalReference`, `billingType === 'PIX'`, `deleted !== true`, e valor em centavos;
  - Tratamento de estornos somando status `DONE` da API do Asaas para definir `PartiallyRefunded` vs `Refunded`;
  - Unificação de concessão com `grantsAccess()`.
- `app/Actions/GrantCourseAccessAction.php`:
  - Captura prévia de `$firstGrantForOrder`;
  - Reativação de matrículas canceladas ou expiradas (`expires_at <= now()`) com novo prazo de 1 ano;
  - Proteção contra extensão indevida de matrículas já ativas ou reentregas de webhook.
- `app/Actions/RevokeCourseAccessAction.php`:
  - Uso de `OrderStatus::accessGranting()` para preservar acesso respaldado por pedidos `PartiallyRefunded`.
- `app/Exceptions/CourseAlreadyActiveException.php` *(criado)*: Exceção customizada contendo a coleção de cursos ativos.
- `config/queue.php`: driver da conexão `course-access` configurado para `sync`.
- `.env` e `.env.example`: `QUEUE_CONNECTION=sync`.

### Testes de Regressão Criados
- `tests/Feature/CheckoutAsaas/T01Test.php` (4 testes)
- `tests/Feature/CheckoutAsaas/T02Test.php` (5 testes)
- `tests/Feature/CheckoutAsaas/T03Test.php` (5 testes)
- `tests/Feature/CheckoutAsaas/T04Test.php` (8 testes)
- `tests/Feature/CheckoutAsaas/T05Test.php` (4 testes)
- `tests/Feature/CheckoutAsaas/T06Test.php` (7 testes)
- `tests/Feature/CheckoutAsaas/T07Test.php` (4 testes)
- `tests/Feature/CheckoutAsaas/T08Test.php` (2 testes)

---

## 4. Evidências de Execução de Testes

### Execução da Suíte Completa do Checkout Asaas:
```bash
rtk ./vendor/bin/sail test tests/Feature/CheckoutAsaas/
```
**Resultado:**
```
   PASS  Tests\Feature\CheckoutAsaas\T01Test (4 passed)
   PASS  Tests\Feature\CheckoutAsaas\T02Test (5 passed)
   PASS  Tests\Feature\CheckoutAsaas\T03Test (5 passed)
   PASS  Tests\Feature\CheckoutAsaas\T04Test (8 passed)
   PASS  Tests\Feature\CheckoutAsaas\T05Test (4 passed)
   PASS  Tests\Feature\CheckoutAsaas\T06Test (7 passed)
   PASS  Tests\Feature\CheckoutAsaas\T07Test (4 passed)
   PASS  Tests\Feature\CheckoutAsaas\T08Test (2 passed)

  Tests:    39 passed (129 assertions)
  Duration: 5.27s
```

### Formatação de Código (Pint):
```bash
rtk ./vendor/bin/sail pint --dirty
```
**Resultado:** Todos os arquivos modificados e criados estão em conformidade com o padrão PSR-12 / Laravel Pint.

---

## 5. Limitações e Pendências Operacionais Reconhecidas

1. **CODEX-03 / F-06-B (Bloqueio para Produção):**
   - Transação atômica completa de webhook vs envio síncrono SMTP ainda requer a implementação do padrão outbox/inbox conforme mapeado pelo arquiteto.
2. **Cron no Servidor:**
   - O cron de `schedule:run` permanece uma pendência de infraestrutura/deploy no servidor.
3. **Backlog Passivo de Jobs:**
   - Jobs que já se encontravam na tabela `jobs` antes da troca para `sync` necessitam de intervenção operacional manual caso precisem ser expurgados ou reprocessados.

---

## 6. Handoff ao Grok (Estágio 6 — `sdd-critique`)

O código encontra-se 100% implementado, compilando e coberto pela suíte de regressão do Worker. Está liberado para o Critic (Grok) construir os testes adversariais de concorrência PostgreSQL, múltiplos cliques, jornadas de navegador e validações com o Sandbox.
