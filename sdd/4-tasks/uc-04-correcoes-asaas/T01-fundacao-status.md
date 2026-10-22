# T01 — Fundação dos status de acesso

> Responsável: agy, estágio 5. Status: CONCLUÍDA. Critérios: AC-1.
> Spec: `sdd/2-specs/uc-04-correcoes-asaas.md`
> Plan: `sdd/3-plans/PLAN-uc-04-correcoes-asaas.md` §2
> Dependências: Nenhuma.

## 1. Descrição e objetivo

Fundação dos status de acesso, conforme a revisão de 29/09 e o contrato detalhado abaixo. Não criar migrations ou implementar CODEX-01/03.

## 2. Justificativa técnica

Atende AC-1 e PLAN §2. Reutilizar os componentes existentes reduz o risco de divergência com o checkout atual. As complementações Codex abaixo resolvem lacunas de integração da revisão e prevalecem nesses pontos.

## 3. Critérios de pronto

- [x] Todos os comportamentos e cenários abaixo foram verificados.
- [x] Regressão mínima reproduziu o defeito antes e passou após a correção (quando aplicável).
- [x] Executar testes focados via `rtk ./vendor/bin/sail test`; registrar resultado real e impedimentos.
- [x] Preservar alterações locais alheias, dados e testes de outros agentes.
- [x] Sem migrations, alterações de ADR pelo Worker ou ampliação de escopo.

## 4. Arquivos afetados

- `app/Enums/OrderStatus.php`
- `app/Models/Order.php`
- `app/Actions/GrantCourseAccessAction.php`
- `app/Http/Controllers/AsaasWebhookController.php`
- `app/Services/CheckoutService.php`
- `app/Http/Controllers/OrderPaymentController.php`
- `resources/views/public/pages/pagamento.blade.php`
- Regressões Worker: `tests/Feature/CheckoutAsaas/T01Test.php` (criar ou ampliar equivalente existente sem duplicar).

## 5. Contrato de implementação e verificação

### Complementações do plano (ler primeiro)

Testar os três status de acesso e os demais sem acesso; isPaid permanece falso para parcial; JSON e view tratam parcial como acesso liberado, sem QR. Reentrega conserva paid_at. A troca da consulta de revogação pertence a T07.

### Requisitos da revisão (transcrição para execução autônoma)

— `OrderStatus::grantsAccess()` (fazer primeiro)

F-03 e F-05 precisam do mesmo conceito: **status que dão direito de acesso** = `Received`, `Confirmed`, `PartiallyRefunded`. A política vigente mantém o acesso em estorno parcial (`RevokeCourseAccessAction` docblock; `AsaasWebhookController:77-81`).

**Alteração em `app/Enums/OrderStatus.php`:**
- Manter `isPaid()` como está (`Received`, `Confirmed`).
- Adicionar `grantsAccess(): bool` (`Received`, `Confirmed`, `PartiallyRefunded`).
- Adicionar `public static function accessGranting(): array` com a lista, para uso em `whereIn`.
- Em `Order.php`, adicionar `grantsAccess()`, que delega ao enum, como já é feito com `isPaid()` em `Order.php:145`.

**Tratamento obrigatório de cada chamador de `isPaid()`** (não decidir caso a caso):

| Local | Troca para `grantsAccess()`? |
|---|---|
| `GrantCourseAccessAction.php:36` `abort_unless(...)` | **Sim** |
| `AsaasWebhookController.php:73` ramo de concessão | **Sim** (ver F-03) |
| `AsaasWebhookController.php:109` carimbo de `paid_at` | **Sim** (C3 continua valendo: grava só se `null`) |
| `CheckoutService.php:185` `recoverPixQrCode` | **Sim** |
| `OrderPaymentController.php:42-43` `pago`/`redirect` | **Sim** |
| `resources/views/public/pages/pagamento.blade.php:14` e `:88` | **Sim** |
| `RevokeCourseAccessAction.php:217` | **Sim**, via `OrderStatus::accessGranting()` (ver F-05) |

## 6. Limites da entrega

Implementar e escrever apenas regressões mínimas do Worker; a suíte adversarial pertence ao Grok. Nenhuma task aprova produção. Linhas citadas na revisão são históricas: localizar os símbolos no código atual. Executar PHP/Artisan/testes somente via Sail com RTK. Não comitar `.env` ou registrar segredos.
