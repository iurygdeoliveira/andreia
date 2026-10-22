# T05 — Reativação idempotente por um ano

> Responsável: agy, estágio 5. Status: CONCLUÍDA. Critérios: AC-6.
> Spec: `sdd/2-specs/uc-04-correcoes-asaas.md`
> Plan: `sdd/3-plans/PLAN-uc-04-correcoes-asaas.md` §5.1
> Dependências: T04 concluída; executar sequencialmente pelo índice.

## 1. Descrição e objetivo

Reativação idempotente por um ano, conforme a revisão de 29/09 e o contrato detalhado abaixo. Não criar migrations ou implementar CODEX-01/03.

## 2. Justificativa técnica

Atende AC-6 e PLAN §5.1. Reutilizar os componentes existentes reduz o risco de divergência com o checkout atual. As complementações Codex abaixo resolvem lacunas de integração da revisão e prevalecem nesses pontos.

## 3. Critérios de pronto

- [x] Todos os comportamentos e cenários abaixo foram verificados.
- [x] Regressão mínima reproduziu o defeito antes e passou após a correção (quando aplicável).
- [x] Executar testes focados via `rtk ./vendor/bin/sail test`; registrar resultado real e impedimentos.
- [x] Preservar alterações locais alheias, dados e testes de outros agentes.
- [x] Sem migrations, alterações de ADR pelo Worker ou ampliação de escopo.

## 4. Arquivos afetados

- `app/Actions/GrantCourseAccessAction.php`
- Regressões Worker: `tests/Feature/CheckoutAsaas/T05Test.php` (criar ou ampliar equivalente existente sem duplicar).

## 5. Contrato de implementação e verificação

### Complementações do plano (ler primeiro)

Na transação e sob lock do pedido, capturar firstGrantForOrder antes de qualquer escrita, usando access_mail_queued_at nulo. Passar para enroll. Somente primeira concessão reativa registro já existente não Active ou expirado; prazo now + um ano. Vigente mantém prazo. Aviso de redundância deve ocorrer na primeira concessão, não em reentrega rotineira. Caminho de recuperação após violação unique deve aplicar a mesma decisão. Não alterar credenciais de conta existente. Preservar o marcador e e-mail único por pedido.

Testar Cancelled, Active expirado e expires_at exatamente now; após reentrega um mês depois o prazo e o número de e-mails permanecem iguais. Matrícula vigente não recebe extensão, devolução automática nem aviso repetido por reentrega.

### Requisitos da revisão (transcrição para execução autônoma)

Recompra não reativa matrícula cancelada/expirada · **CONFIRMADO** · Severidade: Alta (aluno paga e não recebe acesso) · **agy**

**Evidência.** `GrantCourseAccessAction.php:125`: `firstOrCreate` devolve a matrícula existente `Cancelled` ou expirada sem alterá-la, porque existe um unique em `(organization_id, user_id, course_id)`.

**Correção:**
1. Em `grant()`, **antes** de qualquer escrita, calcular `$firstGrantForOrder = $order->access_mail_queued_at === null`. O pedido já está sob `lockForUpdate` (`:35`). Explicar em comentário que `access_mail_queued_at` é gravado na mesma transação da primeira concessão e, por isso, funciona como marcador de "este pedido já concedeu acesso".
2. Passar esse valor para `enroll()`. Se `$firstGrantForOrder` for verdadeiro, a matrícula não for recém-criada (`! wasRecentlyCreated`) e ela estiver (`status !== Active` **ou** `expires_at` no passado), atualizar para `status = Active` e `expires_at = now()->addYear()`, a mesma regra da criação. A vigência de 1 ano foi confirmada pelo usuário em 2026-09-29 (CODEX-02).
3. Matrícula ativa e não expirada: **não mexer**. O usuário decidiu em 2026-09-29 que essa recompra não deve acontecer (ver F-07). Se ainda assim ela chegar aqui (dois pedidos pendentes do mesmo curso, ambos pagos), registrar `Log::warning` com `order_number` e `course_id` e a mensagem "compra redundante: matrícula já ativa; avaliar estorno manual". **Não** estornar automaticamente e **não** estender o prazo.

**Teste mínimo:**
- Matrícula `Cancelled` + novo pedido pago resulta em `Active` com `expires_at` no futuro.
- Reentrega do mesmo pedido após `travel(1)->month()` deixa `expires_at` inalterado.
- A recompra não envia um segundo e-mail do pedido anterior.

## 6. Limites da entrega

Implementar e escrever apenas regressões mínimas do Worker; a suíte adversarial pertence ao Grok. Nenhuma task aprova produção. Linhas citadas na revisão são históricas: localizar os símbolos no código atual. Executar PHP/Artisan/testes somente via Sail com RTK. Não comitar `.env` ou registrar segredos.
