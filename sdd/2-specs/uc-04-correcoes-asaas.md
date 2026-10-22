# UC-04 — Correções do checkout Asaas após revisão

## Contexto

Escopo desta sprint: remediação do UC-04, conforme `sdd/7-reviews/REVIEW-uc-04-analise-asaas-2026-09-29.md`, §§2–6: fundação, F-01 a F-07 (somente F-06-A) e OPS-01. Vínculos: RF-08 (idempotência), RF-09 (revogação), RF-06/07 (acesso/e-mail), numeração **local** do UC-04; ADR-0005 e ADR-0006 em `sdd/1-adrs/`.

Base: `sdd/CONSTITUTION.md`, `sdd/projeto.md` §§7/14 e `sdd/0-casos-de-uso/UC-04-checkout-carrinho-multi-curso.md`. O arquivo legado `sdd/2-specs/uc-04-requisitos.md`, citado pelo UC, está ausente neste checkout; os critérios abaixo são autocontidos e não dependem dele. O E-09 legado passa a representar apenas a defesa no webhook contra dois pedidos já pagos: a revisão mais recente bloqueia novas compras com matrícula vigente.

CODEX-01 (origem das concessões), CODEX-03/F-06-B (recepção durável) e cron de backups permanecem pendentes, fora desta implementação. Concluir esta sprint **não libera produção**: F-06-B continua bloqueante.

## OBJETIVO

Entregar ao agy uma sequência de correções sem migrations: restringir retomadas à sessão, reconciliar cobranças após timeout, reconhecer estornos parciais, reativar acesso expirado/cancelado por um ano, bloquear recompra ativa e executar os envios em `sync` nas duas conexões.

Decisões de produto já registradas na revisão: um ano de acesso, carrinho misto bloqueado inteiro, mensagens neutras para terceiros, nenhuma extensão ou devolução automática, sem worker neste ciclo. A aplicação de grill-me foi resolvida pela exploração desses registros e do código, sem repetir perguntas respondidas. O plano resolve a lacuna técnica de posse do pedido órfão antes da chamada externa.

## Critérios de aceite

- **AC-1:** `isPaid()` mantém Received/Confirmed; `grantsAccess()` e `accessGranting()` incluem também PartiallyRefunded. Todos os chamadores enumerados no §2 da revisão usam o conceito correto; `paid_at` não é sobrescrito.
- **AC-2:** somente pedidos Pending pertencentes à sessão podem ser retomados por cache ou banco. Duas sessões com os mesmos dados recebem pedidos distintos; reenvio na mesma sessão mantém um pedido/uma cobrança. IDs são guardados no servidor, no máximo dez, sem duplicatas; `confirmar` é protegido.
- **AC-3:** timeout mantém pedido e posse na sessão. Nova submissão em até 30 minutos consulta externalReference: zero cobranças permite um POST com a referência original; uma compatível vincula sem POST; divergência, multiplicidade ou falha de GET retorna falha sem POST. Não há retry imediato. HTTP timeout 10 s, conexão 5 s, lock 75 s/block 5 s; QR fora do lock.
- **AC-4:** apenas refunds DONE somam para estorno; parcial concede/mantém acesso, soma igual ou superior ao valor revoga. PENDING/CANCELLED são ignorados, valores originais permanecem intactos e a reentrega é idempotente.
- **AC-5:** divergência de externalReference, tipo PIX, deleted ou valor da cobrança reconsultada responde 200 sem alterar pedido/matrícula. Token inválido continua rejeitado; falha interna continua não-2xx.
- **AC-6:** primeiro processamento de novo pedido pago reativa matrícula cancelada/expirada por um ano; reentrega não renova nem reenvia e-mail. Matrícula ainda vigente mantém prazo e gera aviso de compra redundante na primeira concessão.
- **AC-7:** matrícula Active com expires_at nulo ou futuro bloqueia todo o carrinho antes da retomada, sem pedido ou HTTP. A conta é resolvida pelo e-mail comprador. Cancelada/expirada permite compra; bloqueio preserva carrinho, limita sondagem e revela títulos apenas ao titular autenticado.
- **AC-8:** estorno integral de A preserva a matrícula se B, do mesmo usuário/curso, estiver PartiallyRefunded. Não implementar origem manual das concessões.
- **AC-9:** `.env` e `.env.example` usam QUEUE_CONNECTION=sync e `course-access` usa driver sync. Pagamento de teste entrega e-mail sem worker e sem novos jobs persistidos; SMTP indisponível é documentado como risco pendente de CODEX-03.
- **AC-10:** agy registra regressões mínimas e execução da suíte/Pint, sem migrations nem alterações nos testes adversariais de outro agente. Grok recebe roteiro de navegador/PostgreSQL/Sandbox; Claude realiza auditoria posterior.
