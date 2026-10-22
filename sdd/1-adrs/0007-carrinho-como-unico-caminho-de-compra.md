# 7. Carrinho como único caminho de compra

Date: 2026-09-09
Status: accepted

## Context

O EAFS possui hoje **dois** caminhos de compra em paralelo:

1. **Compra direta por curso** — a `buyAction` em `CoursesCatalog` e `CoursePurchase` abre um modal com os dados do comprador e redireciona para `route('curso.checkout', $course)`, servido por um `CheckoutController` HTTP.
2. **Carrinho** — o slide-over que acumula cursos e, na finalização, descarta tudo menos o primeiro item.

Manter os dois após a mudança para pedido multi-item significaria preservar `processPixCheckout(Course, BuyerData)` na assinatura antiga ao lado da nova assinatura por coleção — dois caminhos de cobrança, duas superfícies de validação e duas trilhas de teste para o mesmo resultado de negócio.

Há um agravante já registrado na auditoria: `tests/Feature/Http/Controllers/CheckoutControllerTest.php` foi excluído enquanto as rotas e o controller continuam ativos (achado R5). O caminho HTTP está hoje em produção-piloto sem a cobertura que tinha.

O contexto operacional é time solo, best-effort, ≤ 500 req/dia — cada superfície duplicada custa manutenção desproporcional.

## Decision

Consolidar **o carrinho como único caminho de compra**.

- "Comprar agora" passa a adicionar o curso ao carrinho e direcionar ao checkout, em vez de abrir um fluxo de compra próprio.
- A rota `curso.checkout` e o `CheckoutController` são removidos.
- `CheckoutService::processPixCheckout` é refatorado para a assinatura por coleção de cursos, **sem** manter compatibilidade com a assinatura mono-curso.
- A comunicação na UI de que "apenas o primeiro item será cobrado" é removida, por ter deixado de ser verdade.

## Consequences

**Prós**
- Uma única superfície de compra para validar, testar e auditar — decisivo para um time solo.
- O `CheckoutService` pode ser refatorado sem carregar a assinatura antiga, evitando a coexistência de dois modelos de pedido no mesmo serviço.
- Elimina o caminho HTTP que hoje está ativo sem cobertura de testes (achado R5), em vez de mantê-lo descoberto.
- Comprador com um único curso continua com fluxo curto: um clique adiciona e leva ao checkout.

**Contras e custos**
- É uma **refatoração**, não trabalho aditivo: `CheckoutService`, rotas, controller e ambos os componentes Livewire mudam na mesma entrega. Maior superfície de mudança simultânea.
- Um clique a mais no caminho mental do comprador que queria "comprar agora" — passa pelo carrinho antes do checkout. Mitigado por adicionar e redirecionar numa única ação.
- Qualquer link externo, campanha ou material que aponte para `curso.checkout` deixa de funcionar. Precisa ser verificado antes da remoção da rota.

**Classificação**
- RF-10 é **Should**, não Must: se a entrega precisar ser fatiada, os RFs 01–09 e 11 podem ir antes, com a remoção do caminho HTTP como último passo da ordem sugerida (seção 14, ordem 8).
