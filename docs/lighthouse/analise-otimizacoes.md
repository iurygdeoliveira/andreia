# Análise Lighthouse — cursos.eafs.com.br (mobile, produção)

Fonte: `docs/lighthouse/cursos.eafs.com.br-mobile-20260819T201358.html`
Lighthouse 13.4.0 · mobile · throttling `simulate` · execução única

## Placar

| Categoria | Score |
|---|---|
| Performance | 0.89 |
| Acessibilidade | 1.00 |
| Best Practices | 0.81 |
| SEO | 0.92 |

| Métrica | Valor |
|---|---|
| FCP | 1.8 s |
| LCP | 2.1 s |
| CLS | 0 |
| TBT | 370 ms |
| Max Potential FID | 360 ms |
| TTFB (documento) | 260 ms |

CLS zero e LCP 2.1 s já são bons. O que puxa a nota para baixo é **trabalho de main thread** e **cadeia de recursos bloqueantes**, ambos majoritariamente de terceiros.

---

## 1. Phosphor Icons via barrel do unpkg — maior ganho isolado

`resources/views/website/layouts/landing.blade.php:57`

```blade
<script src="https://unpkg.com/@phosphor-icons/web" defer></script>
```

Esse script é um *barrel*: em runtime ele injeta **as 6 folhas de estilo** de variantes do Phosphor, e cada uma declara `@font-face` para a sua própria fonte de ícones.

O que o site realmente baixa por causa disso:

| Recurso | Tamanho |
|---|---|
| 6 × `style.css` (regular, thin, light, bold, fill, duotone) | ~84 KiB |
| `Phosphor-Fill.woff2` | 129 KiB |
| `Phosphor-Bold.woff2` | 147 KiB |
| **Total** | **~360 KiB** |

O uso real na landing page é de **20 ícones distintos, em apenas 2 variantes** (`ph-fill` ×20, `ph-bold` ×8):

```
arrow-right, briefcase, caret-down, caret-right, certificate, clock-countdown,
devices, facebook-logo, graduation-cap, identification-card, instagram-logo,
laptop, magnifying-glass, money, rocket-launch, shield-check, student,
trend-up, users-three, youtube-logo
```

Dois efeitos colaterais desse mesmo ponto:

- O `defer` **não ajuda**. Ele adia o script, mas os `<link>` que o script injeta entram bloqueando o render depois — foi por isso que a otimização anterior não surtiu efeito.
- `font-display-insight` acusa **460 ms** de economia porque os `@font-face` injetados vêm **sem `font-display: swap`** — você não controla esse CSS.

### Três caminhos (ordenados por ganho)

**A. Componente Blade com SVG inline** — elimina os ~360 KiB por completo. Vinte ícones inline custam ~0 bytes de rede e ficam no HTML já em cache. É o caminho recomendado nessa escala: 20 ícones é pouco o bastante para não virar manutenção.

**B. Auto-hospedar só `bold` + `fill`** — copia as 2 fontes e 2 CSS para `public/`, cortando de 6 para 2 folhas e mantendo as 2 woff2 (~276 KiB de fonte, mas sem os 84 KiB de CSS nem os RTTs do jsdelivr). Permite adicionar `font-display: swap` no CSS próprio.

**C. Mínimo esforço** — trocar o barrel por 2 `<link>` diretos para `bold/style.css` e `fill/style.css`. Corta ~60 KiB de CSS e remove o passo de injeção via JS, mas mantém a dependência de CDN e as fontes cheias.

**A escolha é sua** — A é o maior ganho, C é o de menor risco.

---

## 2. Preload apontando para a imagem errada

`resources/views/website/layouts/landing.blade.php:52`

```blade
{{-- Preload do logo do hero (elemento LCP) --}}
<link rel="preload" as="image" href="{{ asset('images/logo-hero.webp') }}" fetchpriority="high">
```

**O comentário está incorreto.** O relatório mostra que o elemento LCP é um parágrafo de texto:

```
lcp-breakdown-insight → node: <p class="text-lg text-gray-400 mb-8 leading-relaxed max-w-xl">
                              "A EAFS é uma plataforma brasileira de cursos online..."
```

E o `logo-hero.webp` está **abaixo da dobra no mobile**: `boundingRect.top = 1064` — o grid do hero é `lg:grid-cols-2`, então no mobile a imagem cai *embaixo* do bloco de texto, fora do viewport inicial.

Resultado: uma imagem de 43 KiB que ninguém vê no primeiro paint disputa banda em prioridade alta com o CSS e a fonte, que são o caminho crítico real.

**Correção:** remover o `<link rel="preload">` da linha 52, trocar `fetchpriority="high"` por `loading="lazy"` no `<img>` em `resources/views/website/components/hero.blade.php:62`, e manter `fetchpriority="high"` apenas no `logo-nav.webp` do header (esse sim está no viewport, `top: 8`).

---

## 3. Google Fonts — a cadeia mais longa da página

`landing.blade.php:49` — o `network-dependency-tree-insight` aponta esta como a cadeia crítica mais longa (**987 ms**):

```
documento (431 ms) → fonts.googleapis.com/css2 (639 ms) → fonts.gstatic.com/*.woff2 (987 ms)
```

O `render-blocking-insight` atribui **921 ms** de desperdício só ao `css2?family=Inter`. Os `preconnect` já existentes ajudam, mas não eliminam os dois RTTs sequenciais — o browser só descobre a URL da woff2 *depois* de baixar e parsear o CSS.

**Correção:** auto-hospedar a Inter. Baixar as pesagens realmente usadas (400/500/600/700/800), servir de `public/fonts/` com `font-display: swap` declarado no próprio `website.css`. Isso colapsa a cadeia de 3 níveis para 1 e remove duas origens externas.

Se preferir manter o Google Fonts, o paliativo é adicionar um `<link rel="preload" as="font" crossorigin>` para a woff2 — mas a URL tem hash de versão e quebra sem aviso.

---

## 4. Cache — os hits mais fáceis (infra, não código)

`cache-insight` — economia estimada de 72 KiB:

| Recurso | `cacheLifetimeMs` |
|---|---|
| `images/logo-hero.webp` | **0** |
| `images/logo-nav.webp` | **0** |
| `build/assets/website-BeULxwfB.css` | **0** |

O caso do CSS é o mais grave: o Vite já emite hash de conteúdo no nome (`-BeULxwfB`), então o arquivo é **imutável por construção** e mesmo assim está sendo revalidado a cada visita. Deveria ser `Cache-Control: public, max-age=31536000, immutable`.

Isso se resolve em **regra de cache do Cloudflare / Laravel Cloud**, não no código do Laravel.

---

## 5. Imagens maiores que o necessário

`image-delivery-insight` — 51 KiB:

| Arquivo | Real | Exibido (mobile) | Desperdício |
|---|---|---|---|
| `logo-hero.webp` | 760×323 | 256×109 | 38 KiB |
| `logo-nav.webp` | 320×136 | 151×64 | 13 KiB (inclui 8 KiB de compressão) |

O `logo-hero` tem 3 breakpoints (`w-64 md:w-80 lg:w-[380px]`) — candidato natural a `srcset`/`sizes`. O `logo-nav` é renderizado a 151×64 fixo; um WebP a 302×128 (2×) com compressão mais agressiva resolve.

Ganho modesto perto do item 1, mas barato de fazer.

---

## 6. robots.txt com placeholder em produção — **bug ativo**

`public/robots.txt:86` — única falha de SEO, e é literal:

```
# TODO: trocar pelo domínio de produção antes do deploy
Sitemap: https://[SEU_DOMINIO]/sitemap.xml
```

O TODO não foi executado antes do deploy. O sitemap gerado pelo `SitemapController` **não está sendo descoberto por nenhum crawler**. Trocar por `https://cursos.eafs.com.br/sitemap.xml`.

Dado o investimento em GEO/AI-crawlers que o arquivo demonstra, esse é o item de maior retorno por esforço no relatório inteiro.

---

## O que NÃO é problema seu

Três achados do relatório não vêm do código do projeto — vale registrar para não gastar esforço neles:

- **`unused-javascript` (22 KiB)** — 100% de `chrome-extension://clldacgmdnnanihiibdgemajcfkmfhia/jquery.min.js`, uma extensão instalada no Chrome que rodou a auditoria. Ruído de medição.
- **`deprecations`** — a *única* falha de Best Practices (o que derruba a categoria para 0.81) é `StorageType.persistent` dentro de `cdn-cgi/challenge-platform/scripts/jsd/main.js`, ou seja, o **Bot Management / JS Detections do Cloudflare**. O único controle é desligar JS Detections no painel do Cloudflare.
- **`unminified-css` (3 KiB)** — é o `duotone/style.css` do jsdelivr; some junto com o item 1.

Mais importante: esse mesmo script do Cloudflare consome **654 ms dos 1.5 s** de `bootup-time`. Somado à extensão do Chrome, **a maior parte do TBT de 370 ms e do FID potencial de 360 ms não é JavaScript da aplicação** — a landing page praticamente não tem JS próprio. Otimizar o JS do projeto renderia quase nada.

**Ressalva de medição:** execução única, com throttling simulado e uma extensão carregada no perfil. O 0.89 tem ruído; use-o como direção, não como linha de base precisa. Vale re-medir em aba anônima sem extensões.

---

## Prioridade sugerida

| # | Ação | Ganho | Esforço | Onde |
|---|---|---|---|---|
| 1 | Corrigir `Sitemap:` no robots.txt | SEO 0.92 → 1.00 | trivial | código |
| 2 | Remover preload/`fetchpriority` do logo-hero | libera caminho crítico | trivial | código |
| 3 | Substituir barrel do Phosphor | ~360 KiB + 460 ms | médio | código |
| 4 | Auto-hospedar a Inter | ~920 ms de bloqueio | médio | código |
| 5 | Cache-Control nos assets estáticos | 72 KiB por visita | baixo | Cloudflare |
| 6 | Redimensionar logos | 51 KiB | baixo | assets |

Itens 1 e 2 são de dois minutos e não têm contrapartida. Itens 3 e 4 são o grosso do ganho de performance.

---

# Implementação — itens 1, 2, 3 e 4

Aplicado em 2026-08-19. Para o item 1 foi escolhida a **auto-hospedagem**.

## O que mudou

| Arquivo | Mudança |
|---|---|
| `resources/css/website.css` | +155 linhas: `@font-face` da Inter (2 subsets) e do Phosphor (Bold/Fill) + as 20 regras de ícone |
| `resources/views/website/layouts/landing.blade.php` | Removidos os 2 `preconnect`, o `<link>` do Google Fonts, o `<script>` do unpkg e o preload do logo-hero; adicionado preload da `inter-latin.woff2` |
| `resources/views/website/components/hero.blade.php` | Removido `fetchpriority="high"` |
| `resources/views/errors/404.blade.php` | Removido Google Fonts (redundante — já usa `website.css`) |
| `public/robots.txt` | `[SEU_DOMINIO]` → `cursos.eafs.com.br`; TODO cumprido removido |
| `public/llms.txt` | Mesmo placeholder em **9 URLs** — corrigido junto |
| `public/fonts/` | 4 woff2 auto-hospedadas (Inter latin/latin-ext, Phosphor Bold/Fill) |
| `tests/Feature/WebsiteLandingSeoTest.php` | +4 testes de regressão |

## Decisões

**CSS dentro do `website.css`, não em arquivos separados.** As `@font-face` entram no bundle que o Vite já versiona por hash (`website-DPbMTpik.css`). Não há requisição bloqueante nova, e o CSS herda a correção de cache do item 5 quando ela for aplicada. Links separados para arquivos em `public/` trocariam uma cadeia de CDN por duas requisições bloqueantes de mesma origem.

**Os `unicode-range` da Inter foram mantidos.** São o que faz o browser baixar só o subset `latin` (47 KiB) numa página pt-BR. Sem eles, o `latin-ext` (83 KiB) viria junto — seria uma regressão. Verificado no browser: `latin-ext` fica `unloaded`.

**O preload da fonte leva `crossorigin` mesmo sendo mesma origem.** Fontes são buscadas em modo CORS; sem o atributo o preload é descartado e o arquivo baixa duas vezes.

**Não foi adicionado `loading="lazy"` ao logo-hero.** O grid é `lg:grid-cols-2`: no desktop a imagem fica ao lado do texto, dentro do viewport inicial. Lazy regrediria o desktop para consertar o mobile. Remover apenas o preload e o `fetchpriority` é melhor nos dois formatos.

**O CSS do Phosphor foi reduzido a 4,5 KiB.** O pacote original traz 1530 ícones por variante (85 KiB cada). Só as 20 regras em uso foram extraídas, e o `font-display` passou de `block` (padrão do pacote) para `swap`.

Sobre os 460 ms: o Lighthouse os calculou contra fontes em `block` **atrás da cadeia do jsdelivr** — o período de bloqueio só pesava porque as fontes estavam a dois RTTs de distância. Agora que são de mesma origem, esse período é de milissegundos. O ganho vem principalmente de eliminar a cadeia de CDN; o `swap` sozinho não era a correção.

Foi verificado no browser que `swap` **não** causa tofu nos ícones: com a woff2 bloqueada, `swap` e `block` renderizam 0 pixels visíveis. Glifos do Phosphor vivem na Private Use Area, onde nenhuma fonte de fallback tem desenho — o Chrome reserva a largura mas não pinta `.notdef`.

## Redução de rede

| | Antes | Depois |
|---|---|---|
| Requisições externas | 11 (unpkg, jsdelivr, fonts.googleapis, fonts.gstatic) | **0** |
| CSS de ícones | ~84 KiB (6 folhas) | 4,5 KiB (no bundle) |
| Fontes de ícones | 276 KiB | 276 KiB (local, sem RTT de CDN) |
| Fonte de texto | 47 KiB (após cadeia de 987 ms) | 47 KiB (preload, 0 RTT extra) |
| Cadeia crítica mais longa | 987 ms em 3 níveis | 1 nível |

## Verificação

- `npm run build` — OK. Os avisos "didn't resolve at build time" são esperados: URLs absolutas `/fonts/...` devem mesmo permanecer intactas para resolver a partir de `public/`.
- CSS compilado: nenhuma ocorrência de `unpkg`, `jsdelivr`, `fonts.googleapis` ou `fonts.gstatic`; 4 `@font-face`, todos com `font-display: swap`; as 20 regras de ícone presentes.
- Browser (Playwright, viewport 412×915): as 3 woff2 necessárias retornam 200, ficam `loaded`, os 20 glifos renderizam com largura > 0, `latin-ext` fica `unloaded`, **zero requisições externas**, nenhuma falha de rede.
- `php artisan test` — **60 passando, 236 asserções**.
- Os 2 testes novos mais críticos foram validados por mutação: renomear um ícone para um sem regra CSS faz o teste falhar; remover uma woff2 faz o teste falhar. O teste de ícones varre `File::allFiles` (recursivo de verdade — o `**` do `glob()` do PHP casa só um segmento) e casa por elemento `<i>`, cobrindo tanto `class="..."` quanto `@class([...])`; validado com uma view dois níveis abaixo usando `@class`.

> A home (`/`) não pôde ser carregada localmente: o `.env` aponta para o host `pgsql` do Docker, que não está de pé nesta máquina (HTTP 500 por conexão de banco). A verificação do `<head>` foi feita renderizando o layout direto via `view()->render()` e pela página 404, que usa o mesmo `website.css`. Não há relação com as mudanças.

## Pendente (não solicitado)

- **Item 5 — cache**: `Cache-Control` nos assets estáticos. Use `immutable` **apenas** em `build/assets/*`, que tem hash de conteúdo no nome. Para `public/fonts/*` use um `max-age` longo **sem** `immutable`: os nomes são estáveis, e `immutable` prenderia o usuário a uma fonte obsoleta sem caminho de invalidação no dia em que uma delas for regerada. É regra de Cloudflare / Laravel Cloud, não código.
- **Item 6 — imagens**: redimensionar `logo-hero.webp` e `logo-nav.webp` (51 KiB).
- **Subset das fontes de ícones**: as woff2 do Phosphor ainda trazem 1530 glifos para exibir 20. Um subset levaria os 276 KiB para ~5 KiB:
  ```
  pyftsubset Phosphor-Fill.woff2 --flavor=woff2 --output-file=Phosphor-Fill.subset.woff2 \
    --unicodes=e0ee,e766,ed2c,...
  ```
  Ficou de fora porque exige `fonttools` na toolchain e cria uma armadilha: o 21º ícone renderizaria em branco mesmo com a regra CSS presente.
