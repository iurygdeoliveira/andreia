# 2. Armazenamento Privado de Assets de Mídia em Bucket S3

Date: 2026-07-21
Status: accepted
Amended: 2026-08-17 — ADR-0002-AMEND-01

## Context

Os materiais de mídia da plataforma EAFS (capas de cursos, imagens institucionais, avatares)
contêm ativos visuais das Organizações que não podem ser expostos via URLs públicas diretas
ou buckets abertos.

O conteúdo instrucional das aulas (texto, HTML, componentes interativos) é gerenciado como
views Blade parametrizadas pelo campo `view_path` no model `Lesson`, não como arquivos PDF
binários em storage.

## Decision

Utilizar um **bucket S3 privado** (`disk: s3_private`) para todos os **assets de mídia binária**:

1. Capas de cursos (imagens cover) — upload pelo Organization Owner no `UserPanelProvider`
2. Avatares de usuários — upload pelo próprio usuário
3. Attachments e documentos de suporte (caso implementados no futuro)

O conteúdo das **aulas** é entregue via **Blade views** nativas do Laravel:
- Cada aula possui um campo `view_path` que aponta para uma view Blade registrada
- O `StudentPanelProvider` renderiza a view diretamente via `ViewLesson` page
- Não há arquivos PDF de aulas armazenados em S3

## Amendment History

### ADR-0002-AMEND-01 — 2026-08-17

**Motivo:** Durante a implementação do UC-03 (Acessar e Consumir Cursos pelo Aluno),
ficou evidenciado que o modelo de "aula = PDF em S3" não se adequa ao produto desejado.
A EAFS adotou o modelo de aulas como conteúdo HTML/Blade com `view_path`, o que:

- Permite controle total do layout e interatividade de cada aula
- Elimina a dependência de S3 para entrega do conteúdo instrucional principal
- Simplifica o fluxo de autenticação (sem URLs assinadas temporárias para aulas)
- Mantém o S3 privado para o propósito correto: binários de mídia (imagens, attachments)

**Impacto no código:**
- Removido `HasMedia` / coleção `pdf` do model `Lesson`
- Removido `LessonMaterialController` e rota `/student/{org}/lessons/{lesson}/pdf`
- Adicionado campo `view_path` (migration `2026_08_11_004058_add_view_path_to_lessons_table`)
- Adicionada página `ViewLesson` no `StudentPanelProvider` com fallback para view ausente

## Consequences

### Prós
- Proteção total de assets de mídia (capas, imagens) contra pirataria e acesso não autorizado.
- Escalabilidade do storage sem sobrecarregar o disco local do servidor monolítico.
- Aulas em Blade oferecem flexibilidade total de layout, componentes interativos e versionamento via Git.
- Sem dependência de S3 para o fluxo crítico de consumo de aulas (menor latência e menor custo).

### Contras
- Necessidade de configuração de credenciais AWS/S3 no ambiente (variáveis `.env`).
- Desenvolvimento de aulas requer criação de Blade views pelos desenvolvedores (não é upload simples).
