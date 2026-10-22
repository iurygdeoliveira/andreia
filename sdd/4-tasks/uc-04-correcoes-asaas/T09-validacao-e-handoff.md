# T09 — Validação integrada e passagem ao Grok

> Responsável: agy, estágio 5. Status: CONCLUÍDA. Critérios: AC-10.
> Spec: `sdd/2-specs/uc-04-correcoes-asaas.md`
> Plan: `sdd/3-plans/PLAN-uc-04-correcoes-asaas.md` §7
> Dependências: T08 concluída; executar sequencialmente pelo índice.

## 1. Descrição e objetivo

Validação integrada e passagem ao Grok, conforme a revisão de 29/09 e o contrato detalhado abaixo. Não criar migrations ou implementar CODEX-01/03.

## 2. Justificativa técnica

Atende AC-10 e PLAN §7. Reutilizar os componentes existentes reduz o risco de divergência com o checkout atual. As complementações Codex abaixo resolvem lacunas de integração da revisão e prevalecem nesses pontos.

## 3. Critérios de pronto

- [x] Todos os comportamentos e cenários abaixo foram verificados.
- [x] Regressão mínima reproduziu o defeito antes e passou após a correção (quando aplicável).
- [x] Executar testes focados via `rtk ./vendor/bin/sail test`; registrar resultado real e impedimentos.
- [x] Preservar alterações locais alheias, dados e testes de outros agentes.
- [x] Sem migrations, alterações de ADR pelo Worker ou ampliação de escopo.

## 4. Arquivos afetados

- `sdd/5-implement/uc-04-correcoes-asaas/README.md` (evidências do Worker).

## 5. Contrato de implementação e verificação

### Complementações do plano (ler primeiro)

— AC-10 / T09

T01 → T02 → T03 → T04 → T05 → T06 → T07 → T08 → T09. Cada task contém regressões mínimas do Worker; não substituem a crítica. Usar Pest, Http::fake e bloqueio de chamadas não simuladas; fixtures locais, PostgreSQL de teste e dados sintéticos. Testar serviços e a Action real do Livewire, sem tentar chamar confirmar protegido.

agy: testes focados por tarefa; ao final `rtk ./vendor/bin/sail test` e `rtk ./vendor/bin/sail pint --dirty`. Registrar falhas, skips e impedimentos, sem declarar verde por ausência de execução. Não instalar frontend nem rodar build para documentação/backend sem mudança de assets. Grok: concorrência PostgreSQL, dois cliques, jornada real catálogo→checkout→pagamento→login, mensagens visíveis, carrinho, modais/reatividade e assertNoJavaScriptErrors; Sandbox para estornos. Depois Claude, estágio 7.

Registrar em sdd/5-implement/uc-04-correcoes-asaas/README.md: arquivos, AC atendidos, comandos e resultados, testes pulados/impedidos e riscos. Handoff ai-memory com workspace iury/project eafs para Grok (estágio 6), depois Claude (7). Produção segue bloqueada por CODEX-03. Cron e backlog são pendências operacionais; CODEX-01 é outra feature. Não editar suíte adversarial para passar.

### Requisitos da revisão (transcrição para execução autônoma)

Consolidar evidências de T01–T08; não alterar o escopo da revisão.

## 6. Limites da entrega

Implementar e escrever apenas regressões mínimas do Worker; a suíte adversarial pertence ao Grok. Nenhuma task aprova produção. Linhas citadas na revisão são históricas: localizar os símbolos no código atual. Executar PHP/Artisan/testes somente via Sail com RTK. Não comitar `.env` ou registrar segredos.
