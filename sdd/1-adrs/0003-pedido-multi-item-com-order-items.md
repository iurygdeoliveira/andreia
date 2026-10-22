# 3. Pedido multi-item com `order_items`

Date: 2026-09-09
Status: accepted

## Context

A tabela `orders` nasceu mono-curso: possui `course_id` como coluna direta, um único `payment_id` (unique) e `total_amount` derivado do preço de um curso. O carrinho público (LaraCart via `CartService`) sempre aceitou N cursos, criando um descompasso estrutural resolvido no código por `reset($items)` — apenas o primeiro item chega ao checkout (`CoursesCatalog.php:200`, `CoursePurchase.php:184`).

Duas formas de fechar o descompasso foram consideradas:

1. **N pedidos por checkout**, um por curso, mantendo o schema atual. Preserva o código existente, mas entrega N QR Codes ao comprador para uma única intenção de compra e multiplica o consumo do rate limit de geração de cobranças por checkout.
2. **Um pedido agregando N itens**, com uma única cobrança PIX pelo total.

O projeto ainda não foi para produção (não há pedidos reais a preservar), e o volume esperado é ≤ 500 req/dia com time solo — o que desqualifica soluções elaboradas de agregação.

O projeto de referência (Filacon) adota `order_items`, porém para ingressos com quantidade variável. No EAFS o item é uma matrícula: um curso é comprado uma vez, quantidade sempre 1.

## Decision

Adotar **um `Order` agregando N `OrderItem`**, com uma única cobrança PIX pelo total.

- Criar a tabela `order_items` com `order_id`, `course_id` e `unit_price`.
- Remover `orders.course_id` e o índice `['course_id', 'created_at']`.
- Aplicar constraint unique `(order_id, course_id)`: um curso não se repete no mesmo pedido.
- **Não** criar coluna `quantity`. A quantidade é invariavelmente 1 (RN-01) e uma coluna representaria um estado impossível no domínio.
- `orders.total_amount` passa a ser a soma dos `unit_price` dos itens, calculada a partir dos preços do banco.
- Sem backfill: o schema é reescrito diretamente, pois não há dados de produção (C-05).

## Consequences

**Prós**
- Um QR Code por intenção de compra — a única UX correta para um carrinho.
- O rate limit de geração de PIX passa a contar checkouts, não cursos.
- `unit_price` congela o preço praticado no momento da compra, protegendo o histórico contra alterações posteriores no catálogo.
- A ausência de `quantity` impede, por construção, o estado inválido "duas matrículas do mesmo curso no mesmo pedido".

**Contras e custos**
- Decisão **one-way**: reverter exigiria nova migração e reconciliação de dados (C-01).
- Todo leitor de `orders.course_id` precisa mudar: `GrantCourseAccessAction`, `RevokeCourseAccessAction`, `Order::course()` e consultas relacionadas. É a raiz do risco RP-01.
- Consultas de pedido passam a exigir eager loading de `items.course` para evitar N+1 (RNF-05).
- O comprador que quiser desistir de um item após gerar o PIX precisa refazer o pedido (RP-04) — aceito dado o volume baixo.

**Fora de escopo**
- Cupons, descontos por pacote e preço promocional por item. `unit_price` deixa a porta aberta, mas nada disso é implementado agora.
