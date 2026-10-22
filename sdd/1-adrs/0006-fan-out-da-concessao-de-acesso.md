# 6. Fan-out da concessão de acesso com notificação única

Date: 2026-09-09
Status: accepted

## Context

`GrantCourseAccessAction` está acoplada ao pedido mono-curso em três pontos:

- `$course = $order->course;` — lê a relação direta que a ADR-0003 remove.
- `enroll()` monta os atributos com `'course_id' => $order->course_id`.
- `CourseAccessGrantedMail` recebe um único `Course` e é enfileirada uma vez por pedido, com o marcador `access_mail_queued_at` no próprio pedido.

Com N itens por pedido, uma implementação ingênua iteraria os itens chamando a ação inteira por item — o que enviaria **N e-mails** para uma única compra, já que o marcador de e-mail é por pedido.

A idempotência precisa ser preservada: o Asaas entrega `PAYMENT_RECEIVED` e `PAYMENT_CONFIRMED` para a mesma cobrança e reenvia em timeout. A ação atual resolve isso com `Enrollment::firstOrCreate` protegido por `lockForUpdate` no pedido, capturando `UniqueConstraintViolationException` para o empate de entregas simultâneas — mecanismo que continua válido por matrícula.

Os campos `granted_new_user` e `access_mail_queued_at` são atributos do pedido, não do item: a conta do comprador e a comunicação são únicas por compra.

## Decision

Separar **o que é por item** do **que é por pedido**:

- **Por item (fan-out):** a criação de matrícula. A ação itera os `order_items` e cria uma matrícula por curso, mantendo `firstOrCreate` e o tratamento de violação de unicidade em cada uma.
- **Por pedido (uma vez):** a resolução do usuário, o marcador `granted_new_user`, o marcador `access_mail_queued_at` e o envio do e-mail.
- `CourseAccessGrantedMail` passa a receber a **coleção de cursos** do pedido e produz **um** e-mail listando todos.
- O link de definição de senha continua condicionado a conta recém-criada — em conta existente, o e-mail não carrega link de credencial (proteção já vigente contra redefinição de senha alheia por terceiro pagante).
- `RevokeCourseAccessAction` espelha a estrutura: revoga por item, preservando direitos originados de outros pedidos válidos.
- Toda a operação permanece dentro da transação com `lockForUpdate` no pedido.

## Consequences

**Prós**
- O comprador recebe **um** e-mail listando os N cursos comprados, e não N e-mails para uma compra (CA-10).
- A idempotência por reentrega é preservada em ambos os níveis: matrícula existente não é duplicada, e-mail já enfileirado não é reenviado (CA-11).
- Comprar um curso em que já se está matriculado não quebra o pedido: a matrícula existente é preservada e as demais são criadas (E-09).
- `granted_new_user` e `access_mail_queued_at` permanecem no pedido, sem migração adicional.

**Contras e custos**
- `CourseAccessGrantedMail` e seu template mudam de assinatura — impacto em qualquer teste ou snapshot que assuma um curso único.
- A revogação fica mais delicada: apurar se um curso deve perder acesso exige verificar se **outro** pedido válido concede o mesmo direito. Este é o terreno dos achados R3 e R4 da auditoria.
- Uma falha parcial no meio do fan-out precisa deixar o pedido em estado recuperável pela reentrega do webhook — garantido pela transação, mas exige teste explícito.

**Fora de escopo**
- Os achados R3 (recompra paga mantém matrícula cancelada) e R4 (preservação de direitos independentes incompleta) da auditoria `docs/specs/audit-carrinho-publico.md` permanecem abertos por decisão do usuário. Esta ADR define a **forma** do fan-out; a reconciliação completa de fontes de direito fica para a auditoria futura.
