# Arquitetura Frontend e Filosofia de Recursos (`resources/`)

Este documento documenta a arquitetura e a filosofia de organização da pasta `resources/` do projeto **EAFS**, inspirada no modelo de engenharia do **Filacon** (unificação de Livewire 3 + Filament v5 + Tailwind CSS v4).

---

## 1. Contexto e Motivação

Historicamente, o projeto possuía dois ambientes de frontend isolados:
1. **O ecossistema Filament:** Painéis administrativos (`app/Filament/`) com layouts nativos e estilizações orientadas ao Filament.
2. **O ecossistema legado do site:** Uma pasta separada (`resources/views/website/`) com folha de estilos própria (`website.css`), layout independente (`landing.blade.php`) e dezenas de `@include` imperativos.

Essa separação impedia o uso dos recursos modernos do Filament (ações em modais, campos de formulário ricos, schemas e notificações) na landing page pública e no funil de vendas.

A nova arquitetura unifica esses dois mundos: **a landing page e as páginas públicas agora operam dentro do mesmo ecossistema de componentes do Filament**, sem duplicidade de layouts ou fragmentação de assets.

---

## 2. Os Pilares da Filosofia: Separação Nítida entre `public/` e `panels/`

A organização de `resources/views/` divide-se estritamente em duas grandes esferas arquiteturais:
1. **Camada Pública (`public/`):** Tudo o que o visitante anônimo ou lead acessa (landing pages, componentes de marketing, layout público, checkout, sitemap e páginas de erro).
2. **Camada Autenticada dos Painéis (`panels/`):** Tudo o que pertence aos painéis Filament (Admin, Aluno, Autenticação, Usuário, componentes do painel, reprodutor de aulas, e-mails transacionais e views publicadas de pacotes externos).

A pasta raiz `resources/views/` contém exclusivamente esses dois diretórios:

```
resources/views/
├── public/                     # Camada Pública (Visitantes, Leads e Vendas)
│   ├── components/             # Componentes Blade (<x-public::*>)
│   ├── errors/                 # Páginas de erro (404, etc.)
│   ├── layouts/                # Layout base público (app.blade.php)
│   ├── pages/                  # Páginas completas (home, curso, checkout, etc.)
│   └── sitemap.blade.php       # Sitemap XML
│
└── panels/                     # Camada dos Painéis Filament (Autenticado)
    ├── auth/                   # Telas e logos de autenticação
    ├── clusters/               # Telas de clusters de permissão
    ├── components/             # Componentes internos dos painéis (inclui lesson/)
    ├── forms/                  # Componentes de formulário customizados
    ├── lessons/                # Conteúdo didático das aulas (área do aluno)
    ├── livewire/               # NotificationCenter e triggers do painel
    ├── mail/                   # Templates de e-mails transacionais
    ├── pages/                  # Páginas customizadas dos painéis
    ├── resources/              # Páginas customizadas de CRUD
    ├── student/                # Telas dedicadas do painel do aluno
    └── vendor/                 # Views publicadas de pacotes externos
```

---

## 3. Mapa Completo da Estrutura de `resources/`

```text
resources/
├── css/
│   ├── app.css                       # Ponto único de compilação (Tailwind v4 + Filament)
│   ├── website.css                   # Definições de fontes auto-hospedadas e utilitários da landing
│   └── filament/admin/theme.css      # Estilos customizados exclusivos do painel admin
│
├── html/                             # Fatias de protótipos e mockups estáticos (estilo Filacon)
│   ├── checkout.html
│   ├── design-system.html
│   └── index.html
│
├── js/
│   ├── app.js                        # Scripts principais da aplicação
│   └── bootstrap.js                  # Inicialização do Axios e headers CSRF
│
└── views/
    ├── public/                       # CAMADA PÚBLICA
    │   ├── layouts/
    │   │   └── app.blade.php         # Layout canônico público da landing/checkout
    │   ├── components/               # Componentes da landing (<x-public::*>)
    │   │   ├── autoridade-compacta.blade.php
    │   │   ├── cta-final.blade.php
    │   │   ├── cursos.blade.php
    │   │   ├── depoimentos.blade.php
    │   │   ├── diferenciais.blade.php
    │   │   ├── faq.blade.php
    │   │   ├── footer.blade.php
    │   │   ├── header.blade.php
    │   │   ├── hero.blade.php
    │   │   ├── professor.blade.php
    │   │   ├── sales-attraction.blade.php
    │   │   └── schema-jsonld.blade.php
    │   ├── pages/                    # Páginas públicas servidas pelas rotas web
    │   │   ├── home.blade.php
    │   │   ├── curso.blade.php
    │   │   ├── checkout.blade.php
    │   │   ├── pagamento.blade.php
    │   │   ├── obrigado.blade.php
    │   │   └── privacidade.blade.php
    │   ├── errors/
    │   │   └── 404.blade.php
    │   └── sitemap.blade.php
    │
    └── panels/                       # CAMADA DOS PAINÉIS (FILAMENT / ALUNO)
        ├── auth/                     # Logos e autenticação
        ├── clusters/                 # Telas de clusters de permissão
        ├── components/               # Componentes internos dos painéis
        │   └── lesson/               # Header, Bento Cards e slots das aulas (<x-lesson::*>)
        ├── forms/                    # Custom form fields (video preview, etc.)
        ├── lessons/                  # Aulas renderizadas no painel do aluno
        │   └── fisiologia-exercicio/
        ├── livewire/                 # NotificationCenter e triggers
        ├── mail/                     # Templates de e-mails transacionais (Markdown / Mailable)
        ├── pages/                    # AcceptInvite, templates de email, fallbacks
        ├── resources/                # Telas de confirmação de exclusão customizadas
        ├── student/                  # Visualização de cursos do estudante
        └── vendor/                   # Views publicadas de packages (Pulse, FileManager, etc.)
```

---

## 4. Guia de Uso: Como Adicionar uma Nova Seção ou Página

### Criando um novo componente de landing:
1. Crie o arquivo em `resources/views/public/components/nome-da-secao.blade.php`:
   ```blade
   <section class="py-16 bg-dark-card border-t border-dark-border">
       <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
           <h2 class="text-2xl font-bold text-white">{{ $titulo ?? 'Destaque' }}</h2>
       </div>
   </section>
   ```
2. Utilize-o na página desejada como `<x-public::nome-da-secao titulo="..." />`.

### Criando uma nova página pública:
1. Crie o arquivo em `resources/views/public/pages/minha-pagina.blade.php`:
   ```blade
   @extends('public.layouts.app')

   @section('title', 'Título da Página | EAFS')
   @section('description', 'Descrição otimizada para SEO.')

   @section('content')
       <x-public::hero />
       <x-public::minha-secao />
   @endsection
   ```
2. Aponte o controller ou a rota para a view:
   ```php
   Route::get('/minha-pagina', fn () => view('public.pages.minha-pagina'))->name('minha-pagina');
   ```

### Resolução de Namespaces Blade e Views Customizadas

Para manter a raiz de `resources/views/` 100% limpa (apenas `public/` e `panels/`), o [AppServiceProvider](file:///home/iury/Projetos/eafs/app/Providers/AppServiceProvider.php) e o [bootstrap/app.php](file:///home/iury/Projetos/eafs/bootstrap/app.php) gerenciam a resolução de componentes e namespaces:

1. **Componentes Públicos:**
   - Registrados como prefixo `public`: `Blade::anonymousComponentPath(resource_path('views/public/components'), 'public');`
   - Uso no Blade: `<x-public::nome-do-componente />`.

2. **Componentes de Aulas do Aluno:**
   - Registrados como prefixo `lesson`: `Blade::anonymousComponentPath(resource_path('views/panels/components/lesson'), 'lesson');`
   - Uso no Blade: `<x-lesson::header>`, `<x-lesson::bento-card>`, etc.

3. **Views Publicadas de Pacotes (`panels/vendor`):**
   - O método `configVendorViews()` no `AppServiceProvider` itera sobre os diretórios em `resources/views/panels/vendor/` e registra `View::prependNamespace($namespace, $path)`.
   - Pacotes como Filament, Pulse e Mail têm suas views customizadas carregadas prioritariamente a partir de `panels/vendor/` sem poluir a raiz.

4. **Tratamento de Erros HTTP (`public/errors`):**
   - Configurado em `bootstrap/app.php` via `$exceptions->render()`.
   - Requisições web recebendo `NotFoundHttpException` renderizam nativamente `public.errors.404`, eliminando a necessidade de pastas de erro na raiz de `resources/views/`.

---

## 5. Benefícios Alcançados

- **Padronização:** Todo o projeto segue agora uma única convenção arquitetural, reduzindo a carga cognitiva para manutenção.
- **Interoperabilidade com Filament:** Páginas públicas podem consumir Actions, Schemas e Widgets do Filament sem gambiarras de layout.
- **Performance:** Eliminação de render-blocking de layouts paralelos e compilação otimizada com Vite+ e Tailwind v4.
- **Testabilidade:** 100% de cobertura nos testes de SEO, integridade de tags de busca e funil de conversão.
