# TASKS — UC-04: correções Asaas

Pacote preparado pelas etapas sdd-spec → sdd-plan → sdd-tasks para o agy. **9 tarefas pendentes**, nenhuma implementação executada pelo Codex.

- [Spec](../../2-specs/uc-04-correcoes-asaas.md)
- [Plano](../../3-plans/PLAN-uc-04-correcoes-asaas.md)
- [Revisão de origem](../../7-reviews/REVIEW-uc-04-analise-asaas-2026-09-29.md)

## Instrução para o agy

Execute a skill sdd-implement para `sdd/4-tasks/uc-04-correcoes-asaas/`. Leia spec e plano, depois execute T01 a T09 em ordem. Cada task contém contratos, arquivos, dependências e cenários mínimos. Não criar migrations, alterar ADRs, implementar CODEX-01/03 ou sobrescrever mudanças locais alheias.

O plano complementa a revisão em quatro pontos: posse do órfão antes de HTTP; RateLimiter verificado antes da sondagem; recusa de listagem incompleta; jobs antigos e limites transacionais de sync. A revisão continua sendo a origem dos demais requisitos. As tarefas usam blueprints em prosa; geração de código cabe ao Worker.

## Índice e rastreabilidade

| ID | Tarefa | Critérios | Estado |
|---|---|---|---|
| T01 | [Fundação dos status de acesso](T01-fundacao-status.md) | AC-1 | CONCLUÍDA |
| T02 | [Retomada autorizada pela sessão](T02-posse-da-retomada.md) | AC-2 | CONCLUÍDA |
| T03 | [Reconciliação após timeout](T03-reconciliacao-apos-timeout.md) | AC-3 | CONCLUÍDA |
| T04 | [Estornos parciais e vínculo do webhook](T04-webhook-estornos-e-vinculo.md) | AC-4, AC-5 | CONCLUÍDA |
| T05 | [Reativação idempotente por um ano](T05-reativacao-da-matricula.md) | AC-6 | CONCLUÍDA |
| T06 | [Bloqueio de recompra ativa e mensagem privada](T06-bloqueio-da-recompra.md) | AC-7 | CONCLUÍDA |
| T07 | [Preservar acesso por pedido parcialmente estornado](T07-preservacao-no-estorno.md) | AC-8 | CONCLUÍDA |
| T08 | [Configurar as duas conexões sync](T08-configuracao-sync.md) | AC-9 | CONCLUÍDA |
| T09 | [Validação integrada e passagem ao Grok](T09-validacao-e-handoff.md) | AC-10 | CONCLUÍDA |

## Passagem de etapas

- [x] agy: regressões por tarefa e suíte/Pint completos com evidências em 5-implement.
- [ ] Grok: testes adversariais PostgreSQL e jornadas de navegador, além de Sandbox; indisponibilidade é impedimento registrado.
- [ ] Claude: auditoria de estágio 7 após crítica sem bloqueios.

Concluir o pacote não libera produção: CODEX-03/F-06-B continua bloqueante. CODEX-01, cron de backups e eventual backlog de jobs permanecem explicitamente pendentes. O texto antigo de agentes em sdd/README.md não substitui o AGENTS.md vigente.
