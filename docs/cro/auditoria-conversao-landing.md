# Auditoria de Conversão — Landing Page EAFS

> **Fonte analisada:** código-fonte de `resources/views/website/` (layout `landing.blade.php`, componentes `header`, `hero`, `diferenciais`, `cursos`, `sales-attraction`, `professor`, `faq`, `footer`), `resources/css/website.css` e `docs/lighthouse/analise-otimizacoes.md`
> **Skill aplicada:** `conversion-design-review` v1.1.0 — modo AUDIT
> **Objetivo de conversão declarado:** compra de curso (aluno pagante) — conforme `landing page/product.md` e `briefing.md`
> **Público-alvo:** profissionais e estudantes de Educação Física, Fisioterapia e Nutrição buscando horas complementares válidas e diferenciação de carreira
> **Data:** 2026-08-19

---

## Resumo Executivo

| Severidade | Quantidade | Pilares afetados |
|---|---|---|
| 🔴 Crítico | 4 | 1, 3, 4, 6 |
| 🟡 Atenção | 4 | 5, 7, 10, 11 |
| 🟢 OK | 3 | 2, 8, 9 |

**AI Slop Index:** 1/7 pecados P0 — *visual craftado*. A paleta esmeralda sobre verde-carvão é uma escolha de marca deliberada, não output default de LLM. O único P0 é a métrica "+150%" sem fonte.

**Micro-checklist de persuasão:** 2/5 — reprova (mínimo aceitável: 4/5).

### O diagnóstico em uma frase

A landing constrói **autoridade** muito bem (Prof. Celso, 40 anos, MEC, FAQ que ataca objeções reais) mas **não tem funil**: todo CTA termina em `/login`, um painel Filament sem `->registration()` habilitado. O visitante frio não consegue comprar nem sequer criar conta. A camada de compra não é um bug — ela nunca foi especificada nos casos de uso do MVP.

### Top 3 correções prioritárias

1. **Construir o destino do CTA (Pilar 3 + 4).** `Realizar Curso`, `Começar Agora`, `Ver Cursos Disponíveis` e `Quero Evoluir na Carreira` levam todos para `route('login')`, e `AuthPanelProvider` não expõe registro. Especificar um **UC-04 de compra/matrícula** e criar `/curso/{checkout_code}` com página de venda + checkout. Impacto: é o único item que separa a página de ter conversão zero para tráfego frio.

2. **Adicionar prova social de terceiros (Pilar 6).** Não há **um único depoimento** na página, apesar do briefing especificar três (Bloco 4) e de existirem 8.000 alunos formados. Toda a confiança hoje vem da autodeclaração da marca. Inserir 3 depoimentos com nome, foto e profissão logo acima da grade de cursos.

3. **Reescrever o H1 para o benefício, não a categoria (Pilar 1).** "Capacitação em Educação Física / Certificado Aceito pelo MEC" descreve o que o produto *é*. O briefing traz variações que dizem o que o cliente *ganha* ("Horas complementares garantidas…"). A dor nº1 do VoC — horas complementares — não aparece no H1.

---

## Análise Pilar por Pilar

### Pilar 1 — Clarity Over Creativity
**Status:** 🟡 Atenção
**Micro-Rule (teste dos 5 segundos):** Passa parcialmente. Em 5 s o visitante entende "cursos de Educação Física com certificado MEC". Não entende *por que* isso resolve o problema dele, nem quanto custa, nem o que fazer em seguida.

**Observações:**
- O H1 (`hero.blade.php:14`) é **categoria**, não promessa: "Capacitação em Educação Física — Certificado Aceito pelo MEC." O briefing (Bloco 1) preparou três variações; a implementada é a mais fraca das três em termos de dor.
- A dor primária do VoC — *"preciso de horas complementares para a faculdade"* — só aparece no 4º card de diferenciais e na FAQ. Está enterrada.
- O parágrafo do hero é um **atomic answer** otimizado para motores generativos (GEO), não para o leitor humano: 58 palavras, três cláusulas jurídicas (Decreto Nº 5.124/2004, Resolução CNE Nº 04/99) antes de qualquer benefício. É também o elemento LCP da página — o primeiro texto grande que o usuário lê é regulamentação.
- O badge acima do H1 diz "Certificado aceito pelo MEC" e o H1 repete "Certificado Aceito pelo MEC". Duas linhas consecutivas gastas na mesma informação.

**Correções recomendadas:**
- Trocar o H1 pela variação C do briefing, fundida com a promessa de carreira:
  > **Horas complementares garantidas e conhecimento prático para alavancar sua carreira.**
  > *Certificado aceito pelo MEC. 100% online, acesso imediato.*
- Encurtar o parágrafo do hero para ≤25 palavras focadas em resultado. Mover a citação de decreto/resolução para a FAQ (onde já existe) e para o JSON-LD — o GEO não precisa que o texto citável seja o elemento LCP visível.
- Como o badge já carrega "MEC", liberar o H1 para dizer outra coisa.

---

### Pilar 2 — Strong Visual Hierarchy
**Status:** 🟢 OK
**Micro-Rule (teste do blur):** Passa. Desfocando, o H1 de `text-6xl font-extrabold` com gradiente esmeralda domina o terço superior; o botão `bg-brand-cta` é o segundo blob mais forte.

**Observações:**
- Escala tipográfica coerente: h1 `4xl→6xl`, h2 `3xl→5xl`, body `lg`. Sem competição.
- O `hero-glow` (radial esmeralda a 15% de opacidade) empurra o olhar para o canto superior direito — onde está o card do logo, não o CTA. É um vazamento leve de atenção, mas não crítico.
- O card do hero (`aspect-square`, `rounded-[3rem]`) ocupa metade do viewport em desktop para exibir **apenas o logo**. Real estate premium gasto com zero informação nova — o logo já está no header, 100 px acima.

**Correções recomendadas:**
- Substituir o logo do hero pela foto do Prof. Celso (já existe em `public/images/professor-celso.webp`) ou pelo mockup da área do aluno previsto no Bloco 5 do briefing. O briefing pede explicitamente hero shot com "olhar direcional para o CTA" — o logo não faz isso.
- Reposicionar o `hero-glow` para trás do bloco de CTA em vez do canto direito.

---

### Pilar 3 — Single Primary Goal per Page
**Status:** 🔴 Crítico
**Destinos de conversão primários distintos:** 2 (limite: 1) — e **nenhum dos dois é uma página de compra**.

**Observações:**
- Mapa completo dos CTAs e para onde vão:

  | CTA | Arquivo | Destino real |
  |---|---|---|
  | "Começar Agora" (header) | `header.blade.php:23` | `#cursos` (âncora) |
  | "Entrar" (header) | `header.blade.php:20` | `route('login')` |
  | "Ver Cursos Disponíveis" (hero) | `hero.blade.php:28` | `#cursos` (âncora) |
  | "Realizar Curso" (× N cards) | `cursos.blade.php:75` | `route('login')` |
  | "Quero Evoluir na Carreira" | `sales-attraction.blade.php:57` | `#cursos` (âncora) |

- **O problema central:** o único CTA que sai da página é `route('login')` — e `routes/web.php:17` mostra que `login` é apenas uma rota de compatibilidade que redireciona para `/login` do Filament. Não há rota de checkout, nem de detalhe de curso, nem formulário de captura em `routes/web.php`.
- **O `/login` é um beco sem saída confirmado, não apenas fricção.** `AuthPanelProvider.php:46-47` registra `->login()` e `->passwordReset()`, mas **não há `->registration()`**. O visitante frio não consegue nem criar conta: ele cai numa tela de e-mail e senha para um cadastro que não existe e que ele não tem como abrir. O caminho de compra está fechado nos dois extremos.
- O briefing definia como conversão o **formulário de inscrição multi-etapa** (Bloco 8, com barra de progresso e passo de pagamento). Ele não foi implementado. Existe `landing page/checkout.html` como protótipo estático, sem rota Laravel.
- **Isso é escopo de MVP, não erro de execução.** `sdd/0-casos-de-uso/README.md` lista três UCs — cadastrar cursos (UC-01), estruturar grade (UC-02) e consumir cursos como aluno (UC-03). O UC-03 tem como pré-condição *"O Aluno deve possuir pelo menos uma Matrícula (`Enrollment`) ativa"* — ou seja, a matrícula é assumida como já existente. **Não há UC de compra, matrícula ou pagamento no MVP.** A landing foi publicada apontando para um funil que ainda não foi especificado.
- O header viola a decisão arquitetural do próprio briefing: *"Sem barra de navegação institucional"*. Há 3 links de navegação + "Entrar" + "Começar Agora" — 5 alvos concorrendo acima da dobra.
- O botão "Entrar" tem peso visual quase igual ao "Começar Agora" e serve a **alunos existentes**, não a compradores. É um desvio de tráfego pago para uma tela de login.

**Correções recomendadas:**
- Criar `GET /curso/{checkout_code}` com página de detalhe + checkout, e apontar todos os botões "Realizar Curso" para lá. Enquanto o checkout não existir, apontar para um formulário de captura (nome, e-mail, WhatsApp) — melhor capturar um lead que perder a visita inteira.
- Rebaixar "Entrar" para link de texto pequeno, sem fundo, no canto extremo direito (padrão: peso de link, não de botão).
- Remover os links "Cursos / O Professor / Dúvidas" do header no mobile — em LP de tráfego pago eles são rotas de fuga. Manter no máximo em desktop, se houver resistência do cliente.

---

### Pilar 4 — Obvious Calls to Action
**Status:** 🔴 Crítico
**CTA primário está acima da dobra?** Sim
**Copy do CTA atual:** "Ver Cursos Disponíveis" (hero) / "Realizar Curso" (cards) / "Começar Agora" (header)
**Rótulos contextuais completam "Eu quero…"?** Parcialmente — 2 de 4
**Cor do CTA contrasta com fundo?** Sim (`#047857` sobre `#050a08`)

**Observações:**
- **"Ver Cursos Disponíveis" não é um CTA de conversão, é navegação.** Completa "Eu quero *ver*", não "Eu quero *comprar*". O CTA mais valioso da página — o único acima da dobra — pede ao usuário que role a página. Está gastando a posição de maior valor num verbo passivo.
- **"Realizar Curso"** descreve a ação do sistema, não o ganho do usuário. O briefing especifica "Garantir Meu Acesso Agora" e sugere A/B com "Quero Meu Certificado". Nenhum dos dois foi usado.
- **Três copies diferentes para o mesmo destino.** O briefing exige "Único CTA primário (pode repetir, mas é sempre o mesmo copy)". Hoje são 3 textos distintos convergindo para a mesma âncora — o usuário não constrói memória de qual é a ação principal.
- **Cor:** `--color-brand-cta: #047857` foi escolhida deliberadamente por acessibilidade (o comentário em `website.css:175` documenta que `#10B981` reprova em WCAG AA com 2.54:1, e `#047857` passa com 5.42:1). Decisão correta e bem justificada — mas o efeito colateral é que o CTA ficou **mais escuro** que o accent decorativo `#10B981` usado em ícones e no gradiente do H1. No teste dos olhos meio fechados, o blob mais luminoso da tela é o gradiente do H1, não o botão. Ambos passam; o botão simplesmente não vence.
- Os cards de curso usam `bg-brand/10` com texto `text-brand` — ou seja, o CTA de compra de cada card é um **botão fantasma**, com fundo a 10% de opacidade. O elemento que efetivamente vende é o mais fraco visualmente da página inteira.
- Não há **CTA final** após a FAQ. A página termina no último `<details>` e cai direto no footer. O briefing previa Bloco 7 (Recap + Loss Aversion + CTA final) — não implementado. O leitor que consumiu tudo e ficou convencido não tem botão.

**Correções recomendadas:**
- Hero: trocar "Ver Cursos Disponíveis" → **"Garantir Meu Acesso Agora"**, apontando para o checkout/formulário. Se houver múltiplos cursos e a escolha for necessária, manter a âncora mas mudar o copy para "Escolher Meu Curso" — ainda melhor que "Ver".
- Cards: trocar "Realizar Curso" → **"Quero Este Curso"** e promover de fantasma para sólido: `bg-brand-cta text-white`, mantendo o `#047857` que já passa em AA.
- Padronizar: um único copy primário repetido 3×, conforme o briefing.
- Adicionar seção de CTA final após a FAQ com o recap de 3 bullets e a frase de loss aversion já redigida no briefing.
- Manter `#047857` no botão, mas reservar o gradiente `#34D399→#10B981` para **uma** ocorrência na página (hoje está no H1 do hero e no H2 de `sales-attraction`), para que o CTA não dispute luminosidade.

---

### Pilar 5 — Low Cognitive Load
**Status:** 🟡 Atenção
**Seções visuais distintas:** 7 (hero, diferenciais, cursos, carreira, professor, FAQ, footer) — **🟢 na régua de densidade** (compacta ≤10)

**Observações:**
- A densidade está boa. O problema não é quantidade de seção, é **redundância dentro delas**.
- Sobreposição direta entre `diferenciais` e `sales-attraction`:

  | `diferenciais` | `sales-attraction` |
  |---|---|
  | "Horas Válidas — Certificados aceitos pelo MEC" | "Aumento de Ticket Médio — …certificados reconhecidos pelo MEC" |
  | "Foco na Prática — Do salão para a consultoria online" | "Transição para o Digital — consultoria online" |
  | "40 Anos de XP — Aprenda com um mestre experiente" | "Autoridade Imediata — 40 anos de carreira do Prof. Celso" |

  Três dos quatro cards de `diferenciais` são repetidos como bullets em `sales-attraction`, que por sua vez repete o bloco `professor`. O mesmo argumento é feito três vezes com palavras diferentes.
- A **busca de cursos** (`cursos.blade.php:36`) é um campo de input em posição de destaque. Faz sentido para um portal com dezenas de cursos; para um catálogo pequeno é um elemento interativo que não converte e que pode devolver estado vazio ("Nenhum curso encontrado") — um beco sem saída sem CTA de recuperação.
- Os cards não mostram carga horária nem número de aulas. Para quem compra *por causa de horas complementares*, a carga horária é o dado decisivo — e ele não está na página.

**Correções recomendadas:**
- Fundir `diferenciais` e `sales-attraction` numa única seção de benefícios, ou eliminar `diferenciais` (a mais rasa das duas) e deixar `sales-attraction` como bloco único de argumentação.
- Ocultar a busca via `@if($courses->count() >= 8)`. Abaixo disso ela só adiciona atrito.
- Adicionar **carga horária em horas** no card de cada curso, ao lado do preço.

---

### Pilar 6 — Trust Before Persuasion
**Status:** 🔴 Crítico

**Trust signals presentes:**
- [ ] Depoimentos com nome, cargo e foto — **ausentes**
- [ ] Logos de clientes/prêmios/IES parceiras — ausentes
- [x] Pricing transparente — sim, preço visível no card
- [ ] Contato físico (endereço, telefone, CNPJ) — ausente
- [x] Números específicos (40 anos, 8.000 alunos, 200 cursos, 3 pós) — sim, seção `professor`
- [x] FAQ mapeia objeções de compra — sim, e bem feita (5 perguntas, todas de objeção real)
- [ ] WhatsApp visível para dúvida pré-compra — **ausente** (a FAQ promete suporte, mas não há canal clicável)
- [x] Garantia com condição clara — sim, 7 dias, ancorada no Art. 49 do CDC

**Observações:**
- **Zero prova social de terceiros.** Todos os números da página são autodeclarados pela própria marca. Um visitante em modo *default-to-distrust* não tem nenhuma voz externa confirmando o valor. O briefing especificava três depoimentos, cada um neutralizando uma objeção específica — nenhum foi implementado (estavam marcados 🚧 PENDENTE aguardando o cliente).
- **Trust signal mais forte está longe do CTA.** A seção `professor` — o argumento que quebra "não conheço o professor" — vem **depois** da grade de cursos. O usuário decide comprar antes de descobrir de quem está comprando. A Micro-Rule do pilar pede reassurance no mesmo campo visual do CTA.
- **Garantia de 7 dias enterrada na FAQ.** O único redutor de risco real da oferta aparece na 5ª pergunta de um accordion fechado. No hero há uma menção de 3 palavras ("7 dias de garantia") em texto cinza `text-sm`. Ela não está junto de nenhum botão de compra.
- **Três links sociais quebrados.** `footer.blade.php` tem Instagram, Facebook e YouTube todos com `href="#"` e um `{{-- TODO --}}` em produção. Clicar não faz nada. Perfil social vazio ou quebrado é sinal negativo de confiança — pior que não ter o ícone.
- **Sem CNPJ, endereço ou telefone.** Para venda de certificado com validade legal, ausência de identificação jurídica da instituição é uma lacuna séria de credibilidade. O rodapé identifica quem *desenvolveu o site* (LabSIS, com card visual elaborado) mas não quem *emite o certificado*.
- **Ausência de identificação da IES parceira.** A promessa central é "aceito pelo MEC". Não há selo, número de credenciamento, ou nome da instituição que chancela. É a afirmação de maior peso da página e a menos verificável.

**Correções recomendadas:**
- Inserir bloco de 3 depoimentos (nome, foto, profissão/faculdade) **entre `hero` e `cursos`**, cada um atacando uma objeção do briefing: aceitação do certificado, salto de carreira, qualidade do online.
- Mover o bloco `professor` para **antes** de `cursos`, ou inserir uma faixa compacta de autoridade (foto + "40 anos · 8.000 alunos · CREF ativo") logo abaixo do hero.
- Repetir a garantia como microcopy **sob cada botão de compra**: *"7 dias de garantia incondicional · Acesso imediato · Pagamento seguro"*.
- Corrigir os três `href="#"` do footer — ou remover os ícones até haver perfis reais.
- Adicionar CNPJ, razão social e canal de contato (WhatsApp) no rodapé.
- Nomear a instituição chancelante e exibir o número de credenciamento junto do badge "Certificado aceito pelo MEC".

---

### Pilar 7 — Mobile-First Design
**Status:** 🟡 Atenção

**Observações:**
- Tap targets: adequados. Botões com `py-4 px-8` (~56 px) e `py-2.5 px-5` (~40 px no header, ligeiramente abaixo dos 44 px recomendados). Os `<summary>` da FAQ têm `p-6` — excelentes.
- **Hero mobile invertido.** O grid é `lg:grid-cols-2`: abaixo de 1024 px o card do logo (`py-12`, `aspect-square`) empilha **abaixo** do texto. A análise do Lighthouse confirma `boundingRect.top = 1064` — a imagem está fora do viewport inicial. Ou seja: no mobile, metade do hero é um card decorativo que ninguém vê, e o usuário rola ~1000 px de texto antes de qualquer coisa acontecer.
- **Nenhum elemento fixo de conversão no mobile.** Depois que o usuário rola além do hero, não existe CTA persistente. O header é `fixed` mas contém "Começar Agora", que só rola a página até `#cursos`.
- Micro-Rule do pilar: os elementos interativos críticos devem estar no centro/terço inferior, na zona do polegar. Hoje o único CTA acima da dobra está no terço superior, alcançável apenas com reposicionamento da mão.
- Hover states em `course-card:hover`, `group-hover:scale-105` e nos ícones sociais são invisíveis no touch — não é erro, mas significa que **nenhum feedback visual existe no mobile** para o gesto de tap nos cards.
- `grid-cols-2` em `diferenciais` no mobile deixa 4 blocos de texto centralizado em colunas estreitas — legível, mas apertado a 360 px.

**Correções recomendadas:**
- No mobile, esconder o card do logo do hero (`hidden lg:flex`) e subir o CTA. Isso encurta o hero em ~400 px e traz o botão para dentro do primeiro viewport com folga.
- Adicionar **barra de CTA fixa no rodapé mobile** (`fixed bottom-0 md:hidden`) com o preço e "Garantir Meu Acesso Agora" — é a intervenção de maior retorno isolada em mobile para venda de curso.
- Elevar o botão do header para `py-3` (≥44 px).
- Adicionar `active:scale-[0.98]` nos cards e botões para dar feedback de tap.

---

### Pilar 8 — Speed and Performance
**Status:** 🟢 OK
**Métricas reais** (Lighthouse 13.4.0, mobile, `simulate`, execução única — `docs/lighthouse/`):

| Métrica | Valor | Meta |
|---|---|---|
| Performance | 0.89 | — |
| FCP | 1.8 s | — |
| LCP | 2.1 s | <2.5 s ✅ |
| CLS | 0 | <0.1 ✅ |
| TBT | 370 ms | — |
| TTFB | 260 ms | — |

**Observações:**
- **Passa na Micro-Rule** (visualmente usável em ≤2 s). CLS zero é resultado de disciplina real: todos os `<img>` têm `width`/`height` explícitos.
- O trabalho de otimização já feito é sólido e está documentado: fontes auto-hospedadas, Phosphor reduzido de 6 folhas (~360 KiB) para 4,5 KiB de CSS com apenas os 20 ícones em uso, zero requisições externas, preload correto com `crossorigin`.
- A própria análise do Lighthouse identifica que a maior parte do TBT (654 ms de 1,5 s de bootup) vem do **JS Detections do Cloudflare**, não da aplicação. A landing quase não tem JS próprio.
- **Ponto de atenção CRO, não de performance:** o elemento LCP é o parágrafo de texto do hero. Isso significa que a *primeira coisa que renderiza grande* é o bloco jurídico do GEO. O LCP está rápido, mas está pintando o conteúdo errado primeiro (ver Pilar 1).

**Correções recomendadas:**
- Aplicar os dois itens pendentes já mapeados: `Cache-Control` nos assets estáticos (regra de Cloudflare) e redimensionamento dos logos (51 KiB).
- Re-medir em aba anônima sem extensões — a execução atual tem ruído de uma extensão do Chrome.
- Nada aqui bloqueia conversão. Este pilar não deve consumir sprint.

---

### Pilar 9 — Consistency
**Status:** 🟢 OK

**Observações:**
- **Fontes:** 1 (Inter, variable 400–800) para headings e body. Ideal.
- **Cores primárias:** paleta disciplinada em `@theme` — 4 tons de superfície escura + 5 de marca. Sem token sprawl.
- Sistema de espaçamento consistente: `py-24` para seções principais, `max-w-7xl` para containers, `rounded-2xl` para cards.
- Acessibilidade tratada com rigor incomum: `aria-labelledby` em todas as seções, `aria-hidden` em todos os ícones decorativos, skip link funcional, `<h2 class="sr-only">` em seção sem título visível. Lighthouse Acessibilidade = 1.00.
- Duas inconsistências menores, não sistêmicas:
  - Border radius varia entre `rounded-xl`, `rounded-2xl`, `rounded-3xl` e `rounded-[3rem]` sem regra aparente.
  - `bg-dark` e `bg-dark-surface` alternam entre seções sem cadência previsível (hero→dark, diferenciais→surface, cursos→dark, carreira→surface, professor→dark, faq→surface). Na verdade *é* alternado — funciona, mas o hero e `cursos` compartilham o mesmo fundo sem separador entre eles.
- **Inconsistência de tom, essa relevante:** o rodapé afirma "A maior plataforma de atualização profissional…" — superlativo não verificável que destoa da personalidade "Direta ao Ponto, Prática" definida em `product.md`. Aparece também como fallback do meta description.

**Correções recomendadas:**
- Padronizar radius em 2 valores: `rounded-2xl` para cards, `rounded-full` para botões e pills.
- Trocar "A maior plataforma…" por uma afirmação verificável: *"Cursos de atualização profissional para as áreas da saúde, com certificação aceita pelo MEC."*

---

### Pilar 10 — Feedback and Reassurance
**Status:** 🟡 Atenção

**Observações:**
- Botões têm hover state: sim (`hover:bg-brand-cta-hover` + intensificação de shadow). Transições em ~300 ms — dentro dos 100 ms de percepção para o início da mudança.
- **Nenhum `active:` state em toda a página.** No mobile, onde não existe hover, o usuário toca num botão e não recebe confirmação visual alguma até a navegação acontecer. É exatamente o cenário que a Micro-Rule do pilar cobre.
- **Sem loading state.** Como todos os CTAs saem para `/login`, há um intervalo de navegação sem qualquer indicação — o usuário pode clicar duas vezes.
- **Forms mostram loading state?** Não aplicável — não existe formulário na página (ver Pilar 3).
- **Mensagens de erro humanas?** A única mensagem de estado é `"Nenhum curso encontrado com esse nome."` (`cursos.blade.php:89`), com `role="status"` — tecnicamente correto e acessível, mas é um **beco sem saída**: nenhum botão de "limpar busca" ou "ver todos".
- **Página de sucesso dedicada?** Não existe. O briefing especifica uma Thank You Page com princípio Peak-End (Bloco 7) — não implementada, consequência direta de não haver fluxo de conversão.
- Estado vazio do catálogo (`@empty`) é uma linha de texto cinza. Se o banco estiver sem cursos publicados, a seção que dá nome à página fica em branco, sem CTA alternativo.

**Correções recomendadas:**
- Adicionar `active:scale-[0.98] transition-transform` em todos os botões e cards.
- Na mensagem de busca vazia, incluir botão "Ver todos os cursos" que limpa o input.
- No `@empty` do catálogo, oferecer captura de e-mail: *"Novos cursos em breve — avise-me quando abrir."*
- Ao implementar o checkout, construir a Thank You Page conforme o Bloco 7 do briefing.

---

### Pilar 11 — Anti-AI-Slop Visual
**Status:** 🟡 Atenção
**AI Slop Index:** 1/7 pecados P0 — *visual craftado*

| Sinal | Sev. | Encontrado? | Evidência | Recomendação |
|---|---|---|---|---|
| Indigo default | P0 | **Não** | Accent é `#10B981` esmeralda sobre superfície `#050a08` verde-carvão. Nenhum hex da família indigo/violet no CSS. | — |
| Gradiente "trust" no hero | P0 | **Não** | `text-gradient` é `#34D399→#10B981` — monocromático dentro da marca, aplicado a texto, não a superfície. Não é o two-stop roxo→azul. | — |
| Emoji como ícone | P0 | **Não** | 20 ícones Phosphor via `<i>`, todos com `aria-hidden`. Zero emoji. | — |
| Display serif ignorado | P0 | **Não** | `product.md` não declara fonte display. Inter é escolha consciente. | — |
| Card "dashboard IA" | P0 | **Não** | Cards usam borda completa `border-dark-border`, sem borda lateral colorida. | — |
| **Métricas inventadas** | P0 | **Sim** | `sales-attraction.blade.php:65` — **"+150% Aumento médio na renda relatado por alunos que foram para o online"**, sem fonte, amostra ou metodologia. Também "100% Válido" (l. 80) como afirmação absoluta. | Substituir por dado verificável ou remover. Se vier de pesquisa informal, rotular: *"relato de alunos — amostra de N"*. Ver também Pilar 6. |
| Copy filler | P0 | **Não** | Toda a copy é específica do produto e rastreável ao VoC do briefing. | — |
| Skeleton de template | P1 | Parcial | Sequência Hero→Features→Catálogo→Benefícios→Autoridade→FAQ. Padrão convencional, mas o bloco `professor` com foto e badge de 40 anos é uma variação genuína. | Ao inserir depoimentos, usar formato full-bleed para quebrar a cadência. |
| **Placeholder CDN** | P1 | **Sim** | `sales-attraction.blade.php:69` e `:74` — duas imagens de `images.unsplash.com` em produção, com `alt=""` e `aria-hidden`. | Anti-referência explícita do `product.md`: *"Fotos genéricas de banco de imagens de pessoas malhando"*. Substituir por fotos reais do Celso ministrando aula ou remover. |
| Token sprawl | P1 | **Não** | 9 tokens em `@theme`, hex fora dos tokens apenas em `text-gradient` e `hero-glow`. | — |
| **Accent overuse** | P1 | **Sim** | `text-brand` / `bg-brand` aparece 51 vezes nos componentes: ícones de `diferenciais` (4), bullets de `sales-attraction` (3), círculos de `professor` (3), gradientes de H1/H2, badges, hover de FAQ, hover do footer. | Reduzir ícones decorativos a `text-gray-400`, reservando esmeralda para: CTA, badge MEC e números de autoridade. |

**Interpretação:** 1/7 = **visual craftado**. A identidade é coerente e não parece output de LLM. As três marcas encontradas (métrica sem fonte, stock Unsplash, saturação do accent) são pontuais e corrigíveis sem redesign. Não bloqueiam testes de copy.

---

## Micro-checklist de Persuasão

| # | Pergunta | Status |
|---|---|---|
| 1 | Prova numérica (≥1 métrica) antes ou junto do 1º CTA? | ✅ "+8.000 profissionais formados" no hero, logo abaixo do botão |
| 2 | Objeção principal reframeada em copy (headline ou FAQ #1)? | ✅ FAQ #1 é exatamente "O certificado é válido na minha faculdade?" |
| 3 | Depoimento mata objeção #1 (não elogio genérico)? | ❌ Não há nenhum depoimento na página |
| 4 | Risco reduzido junto ao CTA de conversão (garantia/microcopy)? | ❌ Garantia existe, mas está na FAQ e em texto cinza pequeno — nunca sob um botão |
| 5 | Message match anúncio → headline? | ❌ Impossível validar — não há instrumentação nem campanha rastreável; e o H1 não contém a keyword primária do VoC ("horas complementares") |

**Score: 2/5** — reprova. Mínimo aceitável em LP compacta: 4/5.

Os três itens que falham são resolvidos por duas ações: **adicionar depoimentos** e **colar a garantia sob cada CTA**. O item 5 depende de definir a campanha e reescrever o H1 (Pilar 1).

---

## Plano de Ação Priorizado

### Sprint 1 — Correções Críticas 🔴

| # | Correção | Pilar | Impacto esperado |
|---|---|---|---|
| 1 | Especificar **UC-04 — Comprar/Matricular** e criar rota `/curso/{checkout_code}` com página de venda + checkout; apontar "Realizar Curso" para lá. Enquanto não houver checkout, apontar para formulário de captura (nome, e-mail, WhatsApp) | 3, 4 | Sai de conversão estruturalmente zero para tráfego frio — o painel não tem `->registration()`, então hoje nem cadastro é possível. Nenhuma outra correção importa antes desta |
| 2 | Inserir 3 depoimentos (nome, foto, profissão) entre `hero` e `cursos`, cada um matando uma objeção do briefing | 6 | Único trust signal de terceiro na página. Depende de o Celso fornecer as citações — pendência aberta desde o briefing |
| 3 | Reescrever H1 para a promessa ("Horas complementares garantidas…") e encurtar o parágrafo do hero para ≤25 palavras | 1 | Alinha o primeiro contato com a dor nº1 do VoC e libera o LCP para conteúdo persuasivo |
| 4 | Trocar copy dos CTAs para um único "Garantir Meu Acesso Agora"; promover o botão dos cards de fantasma (`bg-brand/10`) para sólido (`bg-brand-cta`) | 4 | O elemento que vende deixa de ser o mais fraco da tela |
| 5 | Adicionar microcopy de garantia sob todo botão de compra: *"7 dias de garantia · Acesso imediato · Pagamento seguro"* | 6 | Redutor de risco no ponto da decisão. Resolve 1 dos 3 itens reprovados do micro-checklist |
| 6 | Corrigir os 3 `href="#"` dos ícones sociais no footer | 6 | Link social quebrado é sinal negativo ativo de confiança |

### Sprint 2 — Correções de Atenção 🟡

| # | Correção | Pilar |
|---|---|---|
| 7 | Barra de CTA fixa no rodapé mobile com preço + botão | 7 |
| 8 | Esconder card do logo no hero mobile (`hidden lg:flex`) e subir o CTA | 7, 2 |
| 9 | Adicionar seção de CTA final após a FAQ (recap 3 bullets + loss aversion do briefing) | 4 |
| 10 | Mover `professor` para antes de `cursos`, ou inserir faixa compacta de autoridade sob o hero | 6 |
| 11 | Substituir as 2 imagens do Unsplash por fotos reais do Celso, ou remover | 11 |
| 12 | Substituir "+150%" por dado verificável ou rotular a origem | 11, 6 |
| 13 | Fundir `diferenciais` com `sales-attraction` (3 dos 4 cards são redundantes) | 5 |
| 14 | Rebaixar "Entrar" para link de texto; remover navegação do header no mobile | 3 |
| 15 | Adicionar `active:scale-[0.98]` em botões e cards | 10 |
| 16 | Adicionar carga horária ao card de cada curso | 5 |
| 17 | Adicionar CNPJ, razão social e WhatsApp no rodapé; nomear a IES chancelante | 6 |

### Sprint 3 — Refinamentos 🟢 → 🌟

| # | Melhoria | Pilar |
|---|---|---|
| 18 | Trocar o logo do hero pela foto do Celso ou mockup da área do aluno | 2 |
| 19 | Ocultar busca quando `$courses->count() < 8`; adicionar "ver todos" no estado vazio | 5, 10 |
| 20 | Captura de e-mail no `@empty` do catálogo | 10 |
| 21 | Reduzir uso do accent esmeralda: ícones decorativos para `text-gray-400` | 11 |
| 22 | Padronizar border radius em 2 valores | 9 |
| 23 | Trocar "A maior plataforma…" por afirmação verificável | 9 |
| 24 | Aplicar `Cache-Control` (Cloudflare) e redimensionar logos | 8 |
| 25 | Thank You Page conforme Bloco 7 do briefing | 10 |

---

## Notas Finais

**O que está genuinamente bem feito.** A execução técnica está acima da média de mercado: acessibilidade 1.00, CLS zero, LCP 2.1 s, zero dependências externas, JSON-LD estruturado, paleta disciplinada em tokens. O AI Slop Index de 1/7 confirma identidade visual própria — a escolha de verde-carvão + esmeralda é uma decisão de marca, não um default. A FAQ é o melhor ativo de conversão da página: cinco perguntas, todas de objeção real, com respostas concretas e citação legal. O comentário em `website.css:175` documentando a troca de `#10B981` por `#047857` por contraste WCAG é o tipo de rigor que raramente aparece em landing page.

**O que trava a conversão.** A página foi construída como **site institucional de catálogo**, não como landing page de venda. A diferença é estrutural: um catálogo apresenta opções e deixa o usuário decidir; uma LP conduz a uma ação única. Aqui, todo caminho de saída termina em `/login` — um painel sem `->registration()`, onde o visitante frio não consegue sequer abrir conta. O briefing havia definido a arquitetura correta (formulário multi-etapa com barra de progresso, CTA final com loss aversion, thank you page com Peak-End) e essa camada não existe.

**Ressalva importante sobre esse achado.** Isso não é falha de execução da equipe: os três casos de uso do MVP (`sdd/0-casos-de-uso/`) cobrem cadastro de cursos, grade curricular e consumo pelo aluno — o UC-03 assume a `Enrollment` como pré-condição já satisfeita. **A camada de compra/matrícula nunca foi especificada.** A landing entrou no ar antes do funil que ela pressupõe. A recomendação não muda — o Sprint 1 item 1 continua sendo a prioridade absoluta — mas a leitura correta é *"falta um UC-04 de compra no roadmap"*, não *"o site foi mal implementado"*. Tudo que estava no escopo foi entregue com qualidade acima da média.

**Trade-off consciente identificado.** O parágrafo do hero está otimizado para GEO (atomic answer de 40–60 palavras, citável por motores generativos) ao custo de ser o texto humano menos persuasivo da página. Ambos os objetivos são legítimos, mas hoje o GEO ganhou a posição mais valiosa do imóvel. A recomendação é servir o texto citável ao motor via JSON-LD (onde ele já existe) e devolver o hero ao leitor humano — não abandonar o GEO.

**Dependência externa que bloqueia o Sprint 1.** Os depoimentos estão marcados 🚧 PENDENTE no briefing desde a origem, aguardando o Celso. É o item de maior impacto isolado no Pilar 6 e não pode ser resolvido em código. Vale escalar como pedido formal ao cliente antes de iniciar o sprint.

**Limitação desta auditoria.** A análise foi feita sobre código-fonte e sobre o relatório Lighthouse existente, sem renderização visual da página em produção nem dados de comportamento real. Não há GA4, GTM, Clarity, Hotjar ou Pixel instalados no projeto — nenhuma métrica de conversão, bounce ou funil existe hoje. As Micro-Rules de percepção visual (teste dos 5 segundos, teste do blur, olhos meio fechados) foram aplicadas por inferência a partir da estrutura de estilos, não por observação. Para validar as hipóteses deste relatório com números — e para medir o efeito das correções — a instrumentação precisa vir antes das mudanças, não depois.

---

# Implementação — Sprint 1

Aplicado em 2026-08-19. Caminho escolhido para o funil: **ponte agora, checkout depois** — captura de lead com fechamento manual via WhatsApp, enquanto o UC-04 e o gateway não existem.

## Arquivos criados

| Arquivo | Papel |
|---|---|
| `app/Http/Controllers/CourseLandingController.php` | Página de curso, captura de lead, página de obrigado |
| `app/Models/Lead.php` | Model do lead capturado |
| `database/migrations/2026_08_19_210000_create_leads_table.php` | Tabela `leads` com FK opcional para `courses` |
| `resources/views/website/pages/curso.blade.php` | Página de venda + formulário de captura |
| `resources/views/website/pages/obrigado.blade.php` | Thank You Page (Peak-End, Bloco 7 do briefing) |
| `resources/views/website/components/cta-final.blade.php` | CTA final após a FAQ (Bloco 7) |
| `resources/views/website/components/depoimentos.blade.php` | Prova social — renderiza vazio até haver citações reais |
| `resources/views/website/components/autoridade-compacta.blade.php` | Trust signal ao lado do formulário |
| `tests/Feature/CourseLeadFunnelTest.php` | 8 testes de regressão do funil |

## Correções aplicadas

| # | Correção | Pilar | Arquivo |
|---|---|---|---|
| 1 | CTAs do catálogo passam a apontar para `/curso/{checkout_code}` em vez de `route('login')` | 3, 4 | `cursos.blade.php` |
| 2 | H1 reescrito para a promessa: *"Horas complementares garantidas e prática para alavancar sua carreira."* | 1 | `hero.blade.php` |
| 3 | Parágrafo do hero (elemento LCP) encurtado de 58 para 32 palavras, sem abrir com regulamentação | 1 | `hero.blade.php` |
| 4 | CTA do hero: "Ver Cursos Disponíveis" → "Garantir Meu Acesso Agora" | 4 | `hero.blade.php` |
| 5 | Botão dos cards promovido de fantasma (`bg-brand/10`) para sólido (`bg-brand-cta`) | 4 | `cursos.blade.php` |
| 6 | Copy do CTA padronizado em "Garantir Meu Acesso" nas 4 ocorrências | 4, 9 | vários |
| 7 | Microcopy de garantia sob cada botão de compra | 6 | `cursos.blade.php`, `curso.blade.php` |
| 8 | Bloco de autoridade (Celso, +8.000, +200) ao lado do formulário | 6 | `autoridade-compacta.blade.php` |
| 9 | Seção de CTA final após a FAQ, com recap e loss aversion | 4 | `cta-final.blade.php` |
| 10 | Três `href="#"` sociais substituídos por render condicional via config | 6 | `footer.blade.php` |
| 11 | "+150%" sem fonte trocado por "+8.000 profissionais formados" (dado verificável) | 11, 6 | `sales-attraction.blade.php` |
| 12 | Duas imagens do Unsplash substituídas por cards de conteúdo | 11 | `sales-attraction.blade.php` |
| 13 | "Entrar" rebaixado para "Já sou aluno" com peso de link | 3 | `header.blade.php` |
| 14 | Card do logo escondido no hero mobile (`hidden lg:flex`) | 7, 2 | `hero.blade.php` |
| 15 | `active:scale-[0.98]` em todos os botões (feedback de tap) | 10 | vários |
| 16 | Busca de cursos só renderiza com catálogo ≥ 8 | 5 | `cursos.blade.php` |
| 17 | Superlativo "A maior plataforma" trocado por afirmação verificável | 9 | `footer.blade.php` |
| 18 | Tap target do header de `py-2.5` (~40 px) para `py-3` (≥44 px) | 7 | `header.blade.php` |

## Decisões

**A rota de conversão fica fora do middleware `static`.** `SetCacheHeaders` envia `Cache-Control: public, max-age=3600, s-maxage=86400`. Uma página com token CSRF cacheada no Cloudflare por um dia devolveria **419 em todo submit** — falha silenciosa que parece "o formulário não funciona". A home continua cacheada; só as rotas com formulário saem do grupo.

**O binding usa `checkout_code`, não `slug`.** O `slug` não tem índice unique (só `checkout_code` tem) e a aplicação é multi-tenant: dois cursos de organizações diferentes podem gerar o mesmo slug e o binding ficaria ambíguo. `getRouteKeyName()` do `Course` já retornava `checkout_code` — a rota apenas respeita o que o model declara. O filtro por `status = published` no binding impede que rascunho seja acessível por URL direta.

**Depoimentos e WhatsApp ficam em `config/landing.php`, vazios.** Ambos foram identificados como críticos no relatório e **nenhum dos dois pode ser resolvido em código**: as citações estão 🚧 PENDENTE no briefing desde a origem, e não há número de WhatsApp em lugar nenhum do repositório. Preencher com nomes, fotos ou telefone plausíveis seria fabricar exatamente o sinal de confiança falso apontado no Pilar 11. Os componentes renderizam **nada** enquanto a config estiver vazia, e passam a funcionar sozinhos quando os dados reais chegarem. Dois testes travam esse comportamento.

**Os 3 ícones novos foram validados contra a fonte oficial.** `check-circle` (`\e184`), `lock-simple` (`\e308`) e `whatsapp-logo` (`\e5d0`) vieram do CSS do Phosphor 2.1.2 no jsDelivr. A conferência foi feita comparando dois codepoints já presentes no projeto (`certificate` = `\e766`, `users-three` = `\e68e`) — batem exatamente, confirmando que é a mesma versão da fonte auto-hospedada.

## Verificação

- Ortografia: todo o texto visível ao usuário nas páginas novas usa acentuação completa. A citação do Prof. Celso em `autoridade-compacta.blade.php` foi conferida contra `professor.blade.php` — a mesma frase aparece duas vezes na jornada e agora está grafada igual nos dois lugares.
- `php artisan test` — **69 passando, 258 asserções** (eram 60/236; +8 testes do funil, +1 pré-existente).
- `npm run build` — OK. Os avisos de `didn't resolve at build time` nas woff2 são esperados: URLs absolutas `/fonts/...` devem permanecer intactas para resolver a partir de `public/`.
- `./vendor/bin/pint --test` nos arquivos novos — **passed**. (O Pint acusa 20 arquivos no restante do projeto, todos pré-existentes e fora do escopo desta mudança.)
- `./vendor/bin/phpstan analyse` no controller e no model — **No errors**.
- **Validação por mutação** dos três testes mais críticos: apontar o CTA do catálogo de volta para `route('login')` faz o teste falhar; remover o filtro `status = published` do binding faz o teste de rascunho falhar; neutralizar o guard de sessão da página de obrigado faz o teste de Peak-End falhar. Os testes não passam por acidente.
- `SitemapController` usa lista fixa (`home` e `privacidade`), então as novas rotas `noindex` não entram no `sitemap.xml` — sem sinal contraditório para o Google.

## Pendente

**Bloqueado por dependência externa (precisa do Celso):**
- **Citações dos 3 depoimentos** + fotos. É o maior impacto isolado no Pilar 6 e o componente já está pronto esperando os dados.
- **Número de WhatsApp** (`LANDING_WHATSAPP`). Sem ele, a página de obrigado não mostra o botão de contato direto e o lead espera o contato ativo da equipe.
- **URLs dos perfis sociais** (`LANDING_INSTAGRAM`, `LANDING_FACEBOOK`, `LANDING_YOUTUBE`).
- **CNPJ, razão social e nome da IES chancelante** para o rodapé.

**Não verificado nesta máquina:**
- A migration `create_leads_table` **não foi executada em desenvolvimento** — o host `pgsql` do Docker não está de pé aqui. Ela roda e passa na suíte de testes (que usa banco próprio), mas o fluxo não foi percorrido end-to-end num browser. Rodar `php artisan migrate` antes do deploy.

**Fora do escopo do Sprint 1** (itens 7 a 25 do plano acima): barra de CTA fixa no mobile, fusão de `diferenciais` com `sales-attraction`, mover `professor` para antes do catálogo, carga horária nos cards, `Cache-Control` no Cloudflare, redimensionamento dos logos.

**Recomendação sobre medição.** Nenhuma das mudanças acima é mensurável hoje: o projeto não tem GA4, GTM, Clarity ou Pixel. A tabela `leads` passa a ser a primeira fonte de dado de conversão do projeto — mas sem instrumentação de topo de funil não há denominador, ou seja, dá para contar leads mas não calcular taxa de conversão. Instrumentar antes de julgar o efeito destas correções.
