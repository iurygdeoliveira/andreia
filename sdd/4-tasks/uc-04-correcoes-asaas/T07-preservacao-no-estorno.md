# T07 — Preservar acesso por pedido parcialmente estornado

> Responsável: agy, estágio 5. Status: CONCLUÍDA. Critérios: AC-8.
> Spec: `sdd/2-specs/uc-04-correcoes-asaas.md`
> Plan: `sdd/3-plans/PLAN-uc-04-correcoes-asaas.md` §5.4
> Dependências: T06 concluída; executar sequencialmente pelo índice.

## 1. Descrição e objetivo

Preservar acesso por pedido parcialmente estornado, conforme a revisão de 29/09 e o contrato detalhado abaixo. Não criar migrations ou implementar CODEX-01/03.

## 2. Justificativa técnica

Atende AC-8 e PLAN §5.4. Reutilizar os componentes existentes reduz o risco de divergência com o checkout atual. As complementações Codex abaixo resolvem lacunas de integração da revisão e prevalecem nesses pontos.

## 3. Critérios de pronto

- [x] Todos os comportamentos e cenários abaixo foram verificados.
- [x] Regressão mínima reproduziu o defeito antes e passou após a correção (quando aplicável).
- [x] Executar testes focados via `rtk ./vendor/bin/sail test`; registrar resultado real e impedimentos.
- [x] Preservar alterações locais alheias, dados e testes de outros agentes.
- [x] Sem migrations, alterações de ADR pelo Worker ou ampliação de escopo.

## 4. Arquivos afetados

- `app/Actions/RevokeCourseAccessAction.php`
- Regressões Worker: `tests/Feature/CheckoutAsaas/T07Test.php` (criar ou ampliar equivalente existente sem duplicar).

## 5. Contrato de implementação e verificação

### Complementações do plano (ler primeiro)

Em RevokeCourseAccessAction, substituir apenas a lista Received/Confirmed pelo helper accessGranting; conservar os filtros existentes de usuário, curso e exclusão do próprio pedido. Não modelar direitos manuais.

Testar A Refunded e B PartiallyRefunded mantendo acesso, e ausência de B revogando. Usar mesmo usuário/curso nos positivos; pedidos de outro usuário/curso não preservam acesso. Limitar alteração funcional à lista de status.

### Requisitos da revisão (transcrição para execução autônoma)

Revogação ignora pedidos parcialmente estornados · **CONFIRMADO com escopo reduzido** · Severidade: Média · **agy** (parte) / **CODEX** (parte)

**Evidência.** `RevokeCourseAccessAction.php:217` considera como preservadores apenas `Received`/`Confirmed`.

**Correção (agy):**
- Trocar por `->whereIn('payment_status', OrderStatus::accessGranting())`.
- Nada além disso.

**Teste mínimo:** pedido A estornado integralmente e pedido B `PartiallyRefunded` do mesmo curso para o mesmo usuário. A matrícula continua ativa.

**Fora do escopo do agy → CODEX-01.**

## 6. Limites da entrega

Implementar e escrever apenas regressões mínimas do Worker; a suíte adversarial pertence ao Grok. Nenhuma task aprova produção. Linhas citadas na revisão são históricas: localizar os símbolos no código atual. Executar PHP/Artisan/testes somente via Sail com RTK. Não comitar `.env` ou registrar segredos.
