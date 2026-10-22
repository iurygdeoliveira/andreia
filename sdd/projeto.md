# Projeto: Backend EAFS (Gestão de Cursos)

> Documento núcleo gerado pela skill idea-to-spec.
> Versão: 1.2 | Data: 2026-07-21 | Autor: Antigravity

## Sumário Executivo
A EAFS possui grande demanda por venda de cursos com certificado válido, mas hoje sofre com ausência de plataforma online e alta dependência técnica para operar. Este projeto visa construir a fundação backend (multi-tenant no Filament v5) para que a organização consiga cadastrar cursos, estruturar PDFs das aulas e configurar preços com total autonomia, entregando acesso imediato aos alunos.

## 1. Visão e Contexto de Negócio
- **Objetivo de negócio (OST):** Permitir que a equipe lance, precifique e disponibilize novos cursos para venda em menos de 2 horas (redução drástica de tempo operacional e dependência técnica).
- **Problema central:** O stakeholder (Celso) não possui infraestrutura própria e dependia de agências/planilhas para escalar, perdendo timing de vendas e dificultando o crescimento.
- **Razão de agora (urgência):** Nova campanha de tráfego sendo montada para o lançamento da nova LP (Landing Page).
- **Stakeholders:** Celso (Idealizador / Organization Owner).

## 2. Métricas
| ID | Métrica | Baseline | Meta | Prazo |
|---|---|---|---|---|
| M-01 | Autonomia de Publicação | 0% | 100% dos novos cursos publicados sem devs | 6 meses |
| M-02 | Suporte Técnico para Conteúdo | Alto | 0 chamados | 6 meses |

## 3. Personas
| ID | Persona | Contexto | Dor principal | Comportamento atual |
|---|---|---|---|---|
| P-01 | Super Admin | Dono do SaaS/Plataforma. | Custos altos com setups manuais. | Configura organizações via banco/painel `/admin`. |
| P-02 | Organization Owner (Celso) | Especialista focado em vendas e alunos. | Depender de devs para atualizar preço/cursos. | Acessa `/user/{organization:slug}` para gerenciar conteúdo. |
| P-03 | Aluno (Student) | Usuário final comprador do curso. | Atraso no recebimento de conteúdo/acesso após compra. | Acessa `/student/{organization:slug}` para estudar os PDFs. |

## 4. Dores Mapeadas
| ID | Dor | Persona afetada | Evidência (MOM Test) |
|---|---|---|---|
| D-01 | Ausência de plataforma própria para vender e entregar acesso imediato. | Organization Owner (P-02), Aluno (P-03) | "A EAFS não possui plataforma online e o Celso precisa cadastrar cursos para a nova landing page." |

## 5. Como resolver as dores mapeadas
| ID | Dor(es) | Abordagem (produto/processos) | Entregáveis de alto nível (capacidades) | MoSCoW |
|----|---------|----------------------------------|------------------------------------------|--------|
| R-01 | D-01 | Painel administrativo SaaS. | Módulo de gestão de Organizações (`organizations`) no `AdminPanelProvider`. | Must |
| R-02 | D-01 | Gestor de Conteúdo visual no Filament. | Módulo para cadastrar Cursos e estruturar a grade (Módulos e Aulas em PDF) no `UserPanelProvider`. | Must |
| R-03 | D-01 | Engine simples de monetização. | Configuração de preço por curso gerando link de checkout. | Must |
| R-04 | D-01 | Portal de Aprendizagem do Aluno. | Área logada no `StudentPanelProvider` com visualizador da grade e download dos PDFs. | Must |

## 6. Riscos de Produtos
| ID | Domínio | Descrição | Severidade | Experimento de validação |
|---|---|---|---|---|
| RSK-01 | Técnico | Vazamento de dados de cursos/alunos entre organizações do sistema multi-tenant. | Alta | Uso imediato da arquitetura nativa multi-tenant do Filament (`Organization`). |

## 7. Capacidade Operacional
| Dimensão | Valor declarado | Implicação para RNFs |
|---|---|---|
| Tamanho e composição do time | Pequeno (1 a 4 pessoas) | Evitar microsserviços. Stack monolítica e produtiva (TALL). |
| Capacidade operacional | Best-effort / Comercial | Sem plantão 24/7. Erros devem ser capturados passivamente (Log/Sentry). |
| Volume esperado no 1º ano | Baixo (< 500 req/dia no admin) | Banco relacional clássico (PostgreSQL) sem réplicas iniciais é suficiente. |
| Trajetória 12 meses | Estável | Foco total na consistência do dado em vez de escalabilidade massiva. |

## 8. Requisitos Funcionais
| ID | Requisito | Critérios de aceite | MoSCoW | Dor |
|---|---|---|---|---|
| RF-01 | Gestão de Organizações | Super admin cadastra a Organização "EAFS" e associa um usuário como Organization Owner no `/admin`. | Must | D-01 |
| RF-02 | Isolamento de Dados | Organization Owner vê apenas dados vinculados ao seu `organization_id` no `/user/{organization:slug}`. | Must | D-01 |
| RF-03 | Gestão de Cursos (CRUD) | Owner cria curso com Título, Descrição, Capa e Status. | Must | D-01 |
| RF-04 | Grade Curricular | Owner cria Módulos dentro do Curso e faz upload de aulas em PDF. | Must | D-01 |
| RF-05 | Precificação e Checkout | Owner define preço; sistema expõe link ou ID da oferta para a LP. | Must | D-01 |
| RF-06 | Área do Aluno (Student Panel) | Aluno acessa `/student/{organization:slug}`, navega na grade e baixa os PDFs das suas Matrículas ativas. | Must | D-01 |

## 9. Requisitos Não Funcionais
| ID | Categoria | Requisito | Métrica | Âncora na seção 7 |
|---|---|---|---|---|
| RNF-01 | Arquitetura | Uso rigoroso de Scopes Multi-tenant via Filament (`Organization`). | 100% de isolamento | Mitigação RSK-01. |
| RNF-02 | Segurança | PDFs salvos em bucket S3 privado (`s3_private`), acessíveis via URL assinada/rota protegida pós-validação de Matrícula. | 0 vazamentos | Arquitetura TALL monolítica. |
| RNF-03 | Manutenibilidade | Stack baseada unicamente em Laravel 13 + Filament v5. | Padrão da plataforma | Time pequeno. |

## 10. Regras de Negócio
| ID | Regra | UCs aplicáveis |
|---|---|---|
| RN-01 | Um Organization Owner nunca pode acessar cursos de outra Organização. | UC-01, UC-02 |
| RN-02 | Apenas arquivos com extensão `.pdf` são aceitos na criação de Aulas. | UC-02 |
| RN-03 | O Aluno visualiza apenas cursos nos quais possui Matrícula (`Enrollment`) ativa na Organização atual. | UC-03 |

> **Casos de uso:** ver `0-casos-de-uso/README.md` e arquivos `UC-xx-*.md`.

## 11. Restrições
| ID | Descrição | Reversibilidade (one-way / two-way) | Justificativa |
|---|---|---|---|
| RST-01 | Aulas apenas em PDF | Two-way | Simplifica drásticamente o storage no MVP (não precisamos de CDN de vídeo agora). |

## 12. Matriz de Rastreabilidade
| Dor | RF | RNF | UC |
|---|---|---|---|
| D-01 | RF-01, RF-02 | RNF-01 | Infra (Nativo) |
| D-01 | RF-03, RF-05 | RNF-03 | UC-01 |
| D-01 | RF-04 | RNF-02 | UC-02 |
| D-01 | RF-06 | RNF-01, RNF-02 | UC-03 |

## 13. Log de Auditoria (Maker-Checker)
| Iteração | Itens reprovados | Correções |
|---|---|---|
| 1 | Nenhum. Conformidade com todas as regras e âncoras validada com sucesso. | N/A |
| 2 | Alinhamento da terminologia e stack com a base de código real. | Atualizado para Organization / Organization Owner e Laravel 13 + Filament v5. |
| 3 | Inclusão do StudentPanelProvider e fluxo do Aluno. | Adicionado Persona P-03, RF-06, RN-03 e UC-04. |
| 4 | Enxerto do CONTEXT.md em projeto.md. | Incorporada a arquitetura de painéis e o glossário canônico do domínio na íntegra. |
| 5 | Remoção do UC-01 original (gerenciamento de tenants). | Removido UC-01 por ser infra nativa e re-numerados UC-01 (Cursos), UC-02 (Grade) e UC-03 (Aluno). |

## 14. Escopo do MVP e ordem sugerida de entrega

| Ordem sugerida | RF | UC(s) | Dependências / notas |
|----------------|-----|-------|----------------------|
| Base | RF-01, RF-02 | Infra (Nativo) | Base de Organizações (`Organization`) nativa e já integrada no Filament v5. |
| 1 | RF-03, RF-05 | UC-01 | Gestão e precificação de Cursos no `UserPanelProvider`. |
| 2 | RF-04 | UC-02 | O Curso precisa existir para anexar módulos e PDFs. |
| 3 | RF-06 | UC-03 | Depende de Cursos, Módulos e Aulas estarem cadastrados para liberação do acesso ao Aluno. |
| 4 | (RFs próprios da spec UC-04) | UC-04 | Checkout de carrinho multi-curso: pedido multi-item, cobrança PIX única e concessão em fan-out. Depende de Cursos (UC-01) e Matrículas (UC-03). Ver `2-specs/uc-04-checkout-carrinho-multi-curso.md`. |

## 15. Glossário e Arquitetura de Domínio (CONTEXT)

### Arquitetura de Painéis (Filament v5)
- **`AdminPanelProvider` (`/admin`)**: Painel exclusivo do Super Admin para gestão global de Organizações (`organizations`), usuários e segurança.
- **`UserPanelProvider` (`/user/{organization:slug}`)**: Painel exclusivo do Organization Owner (Celso) e equipe para gerenciar cursos, mídias, preços e a grade curricular.
- **`StudentPanelProvider` (`/student/{organization:slug}`)**: Painel exclusivo do Aluno para navegação nos cursos adquiridos e download dos PDFs das aulas.

### Entidades e Conceitos Centrais
- **Organização (`Organization`)**: Entidade primária de multi-tenancy (`organizations`). Isolamento estrito de dados, cursos e membros por inquilino.
- **Organization Owner**: Proprietário da Organização (`role: owner` na pivot `organization_user`) com gestão total sobre o `UserPanelProvider`.
- **Super Admin**: Usuário com permissões globais na plataforma com acesso ao `AdminPanelProvider`.
- **Aluno (`Student`)**: Usuário final matriculado que acessa o `StudentPanelProvider` para consumo didático.
- **Matrícula (`Enrollment`)**: Vínculo ativo entre um Aluno e um Curso dentro de uma Organização, gerado após pagamento na Landing Page.
- **Curso (`Course`)**: Unidade educacional de uma Organização (`title`, `slug`, `description`, `cover_image_path`, `price`, `status`).
- **Módulo (`Module`)**: Agrupamento temático de aulas dentro de um `Course`.
- **Aula (`Lesson`)**: Unidade de estudo de um `Module`, com anexo em PDF armazenado no bucket S3 privado (`s3_private`).
