# 1. Arquitetura de Três Painéis no Filament v5 (Admin, User, Student)

Date: 2026-07-21
Status: accepted

## Context
O sistema EAFS precisa atender a três perfis de usuários com necessidades e interfaces completamente distintas:
1. **Super Admin**: Gestão global da infraestrutura e cadastro de Organizações.
2. **Organization Owner (Celso)**: Gestão de cursos, mídias, precificação e alunos da sua Organização.
3. **Aluno (Student)**: Consumo exclusivo de conteúdo (visualização da grade e download de PDFs).

## Decision
Separar as experiências em três `PanelProvider` distintos no Filament:
- `AdminPanelProvider` em `/admin`
- `UserPanelProvider` em `/user/{organization:slug}`
- `StudentPanelProvider` em `/student/{organization:slug}`

## Consequences
Prós:
- Isolamento estrito de código, rotas e middlewares por perfil.
- Alunos não compartilham rotas nem componentes do painel administrativo do Owner.
- Scoping nativo de multi-tenancy no Filament por Organização mantido de forma limpa nos painéis `User` e `Student`.

Contras:
- Necessidade de registrar e manter três Panel Providers no Laravel.
