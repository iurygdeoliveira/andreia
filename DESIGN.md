---
name: Método Andreia 360
description: Transformação feminina completa para mulheres acima dos 40 anos
colors:
  primary-deep: "#010d26"
  primary: "#023059"
  primary-light: "#5881a6"
  primary-soft: "#9cbcd9"
  neutral-bg: "#F0F4F8"
  neutral-white: "#FFFFFF"
  accent-gold: "#D4A843"
typography:
  display:
    fontFamily: "Inter, sans-serif"
    fontWeight: 700
  body:
    fontFamily: "Inter, sans-serif"
    fontWeight: 400
---

<!-- SEED: re-run /impeccable document once there's code to capture the actual tokens and components. -->

# Design System: Método Andreia 360

## 1. Overview

**Creative North Star: "Acolhimento Premium"**

O design visual deve refletir confiança, experiência e acolhimento para a mulher 40+. Ele se afasta ativamente da estética "fitness clichê" (rosa neon, roxo, fotos super saturadas de suor) e abraça uma paleta de azuis profundos que comunica seriedade, saúde e profissionalismo de alto nível (justificando o ticket VIP). O ambiente é seguro, limpo e direto, focado puramente em conversão e narrativa emocional.

**Key Characteristics:**
- Foco em legibilidade e alto contraste para o público maduro.
- Uso heroico de provas sociais e imagens reais (sem mockups ou stock).
- Caminho direto sem distrações (Taxa de Atenção 1:1).

## 2. Colors

A paleta é construída sobre azuis serenos e profundos, estabelecendo autoridade e quebrando a expectativa do "fitness feminino" tradicional.

### Primary
- **Azul Marinho Escuro** (#010d26): Backgrounds principais, textos de títulos e áreas de alto contraste.
- **Azul Profundo** (#023059): Seções alternadas, cards de plano e rodapé.
- **Azul Médio** (#5881a6): Botões CTA primários, links, destaques visuais e ícones de suporte.
- **Azul Claro** (#9cbcd9): Backgrounds suaves, bordas de componentes, hover states e elementos de apoio.

### Secondary
- **Dourado/Amber** (#D4A843): Badge "RECOMENDADO", destaques de preço, estrelas de avaliação. Oferece o contraste premium perfeito com os azuis frios.

### Neutral
- **Branco** (#FFFFFF): Textos sobre fundos escuros e respiro visual.
- **Off-white** (#F0F4F8): Backgrounds de seções claras que não devem competir pela atenção.

**The Anti-Neon Rule.** Nunca utilize tons rosa-choque, neon ou roxo vibrante, típicos do marketing fitness genérico. O design deve se assemelhar mais ao de uma clínica de estética ou bem-estar premium do que a uma academia comum.

## 3. Typography

**Display Font:** Inter (com fallback sans-serif)
**Body Font:** Inter (com fallback sans-serif)

**Character:** Limpa, neutra e de altíssima legibilidade. O foco não é estilo decorativo, mas clareza absoluta e redução de fadiga visual.

### Hierarchy
- **Display**: Headlines heroicas e propostas de valor centrais.
- **Headline**: Títulos de seções (Benefícios, Como Funciona, Planos).
- **Body**: Textos de descrição, objeções e FAQ. Limitar comprimento da linha a 65–75 caracteres para otimizar a leitura.
- **Label**: Microcopy de suporte, tags de "Vagas Limitadas", legendas de depoimentos.

**The Accessible Scale Rule.** O texto base nunca deve ser menor que 16px. Garantir sempre proporção adequada de contraste em relação ao fundo.

## 4. Elevation

O sistema utiliza sombreamento tático apenas para elevar elementos clicáveis e cards de conversão.

### Shadow Vocabulary
- **Card Lift**: Sombra direcional leve para separar os blocos de depoimentos e os cards de planos do fundo.
- **CTA Glow**: Sombreamento sutil para destacar o botão de compra primário.

**The Purposeful Depth Rule.** As sombras nunca são decorativas. Elas servem exclusivamente para indicar "este elemento é interativo" ou "este é o plano recomendado".

## 5. Components

### Buttons
- **Shape:** Arredondamento suave (radius médio, ~6px a 8px).
- **Primary:** Azul Médio (#5881a6) com texto Branco (#FFFFFF). Usado exclusivamente para a ação principal.
- **Hover / Focus:** Transição para um tom mais escuro e leve elevação vertical, dando feedback tátil de clique.

### Cards / Containers
- **Corner Style:** Radius leve (4px a 8px) — macio o suficiente para acolher, rígido o suficiente para manter a estrutura.
- **Background:** Contrastante com o fundo da seção (ex: cards #023059 sobre fundo #010d26).
- **Border:** Evitar bordas grossas; se necessário, usar #9cbcd9 com opacidade reduzida para demarcar limites.

### Badges / Tags
- **Style:** Fundo #D4A843 (Dourado) para marcações de urgência ou prova (ex: "RECOMENDADO", "Restam poucas vagas VIP").

## 6. Do's and Don'ts

As diretrizes garantem que o visual comunique "método para a mulher real" e evite as armadilhas comuns do nicho fitness.

### Do:
- **Do** usar fotografias reais da Andreia e de alunas reais, sempre que possível mostrando o contexto de vida.
- **Do** garantir contraste altíssimo entre texto e fundo.
- **Do** repetir visualmente o mesmo estilo e texto no botão CTA para reforçar o caminho único de conversão.

### Don't:
- **Don't** usar estética clichê de academia feminina (tons exagerados de rosa neon ou roxo).
- **Don't** utilizar imagens de banco (stock photos) genéricas, falsas ou "perfeitas demais".
- **Don't** esconder ou ofuscar depoimentos e provas reais.
- **Don't** introduzir navegação no topo (sem menu institucional) para manter a taxa de atenção 1:1.
