# Pipeline SDD — EAFS

As pastas são numeradas pela **etapa do pipeline** que as produz. O número da pasta é o mesmo `stage:` declarado na skill correspondente.

```
sdd/
├── projeto.md            núcleo do produto (visão, requisitos, §14 ordem do MVP)
├── CONSTITUTION.md       índice das decisões arquiteturais
│
├── 0-casos-de-uso/       ← idea-to-spec      · fluxos e exceções por UC
├── 1-adrs/               ← sdd-constitution  · uma decisão por arquivo
├── 2-specs/              ← sdd-spec          · escopo e critérios de aceite da sprint
├── 3-plans/              ← sdd-plan          · plano técnico (schema, camadas, Filament)
├── 4-tasks/              ← sdd-tasks         · tarefas atômicas T01, T02… por feature
├── 5-implement/          ← sdd-implement     · registro de código construído por UC
└── 6-reviews/            ← sdd-analyze       · auditoria de conformidade
```

**Etapa 5 (`5-implement`):** a etapa 5 (`sdd-implement`) produz código-fonte em `app/`, `database/` e `tests/`, e registra todo o mapeamento técnico e rastreabilidade dos artefatos construídos na pasta `5-implement/`.

## Ordem de execução

| # | Skill | Agente | Entrega |
|---|-------|--------|---------|
| 0 | `idea-to-spec` | claude | `projeto.md` + `0-casos-de-uso/` |
| 1 | `sdd-constitution` | claude | `1-adrs/` + `CONSTITUTION.md` |
| 2 | `sdd-spec` | claude | `2-specs/{slug}.md` |
| 3 | `sdd-plan` | claude | `3-plans/PLAN-{slug}.md` |
| 4 | `sdd-tasks` | claude | `4-tasks/{slug}/T01…Tnn.md` |
| 5 | `sdd-implement` | **agy** | código em `app/`, `database/` + docs em `5-implement/` |
| 6 | `sdd-analyze` | codex | `6-reviews/REVIEW-{slug}.md` |

As etapas 0–4 são de planejamento (Explorer/Orchestrator); a 5 é construção (Worker); a 6 é auditoria (Reviewer).

## Convenção de nomes

Cada feature usa o mesmo slug em todas as etapas, prefixado pelo seu UC:

```
0-casos-de-uso/UC-04-checkout-carrinho-multi-curso.md
2-specs/uc-04-checkout-carrinho-multi-curso.md
3-plans/PLAN-uc-04-checkout-carrinho-multi-curso.md
4-tasks/uc-04-checkout-carrinho-multi-curso/
5-implement/uc-04-checkout-carrinho-multi-curso/
6-reviews/REVIEW-uc-04-checkout-carrinho-multi-curso.md
```

## Features

| UC | Nome | Spec | Plan | Tasks | Impl (5) | Review |
|----|------|:----:|:----:|:-----:|:--------:|:------:|
| UC-01 | Cadastrar e configurar cursos | ✅ | ✅ | ✅ | ✅ | ✅ |
| UC-02 | Estruturar grade curricular | ✅ | ✅ | ✅ | ✅ | — |
| UC-03 | Acessar e consumir cursos (aluno) | ✅ | ✅ | ✅ | ✅ | ✅ |
| UC-04 | Checkout de carrinho multi-curso | ✅ | ✅ | ✅ | ✅ | — |

> ⚠️ **Numeração de requisitos:** os IDs `RF-xx`, `RNF-xx`, `D-xx` e `RN-xx` do `projeto.md` são **globais** e cobrem UC-01 a UC-03. O UC-04 usa IDs **locais**, definidos em [`2-specs/uc-04-requisitos.md`](2-specs/uc-04-requisitos.md) — a mesma faixa numérica com significados diferentes. Ao citar um RF do UC-04, indique a origem.
