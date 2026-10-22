# 4. Checkout anônimo em página Filament fora do painel Student

Date: 2026-09-09
Status: accepted

## Context

O pedido original era reproduzir o padrão do Filacon, onde o checkout vive dentro do painel do participante (`app/Filament/Participant/Pages/Checkout.php`). A inspeção do EAFS revelou dois obstáculos que impedem a cópia literal:

1. **O painel Student exige matrícula ativa.** `User::canAccess()` para o painel `student` retorna `$this->enrollments()->active()->exists()` (salvo Admin/Owner). Uma matrícula só existe após o pagamento confirmado.
2. **DEC-01, já vigente no código:** *"a conta do aluno só nasce depois que o dinheiro entra"* (`CheckoutService`, migration de `orders`). O `user_id` do pedido é nullable exatamente por isso.

Logo, colocar o checkout dentro do painel Student criaria um impasse circular: para pagar seria preciso ter matrícula, e para ter matrícula seria preciso pagar. Sair do impasse exigiria login/cadastro antes do pagamento — invertendo DEC-01 e introduzindo o problema de sobrevivência do carrinho através da regeneração de sessão no login.

O Filacon não enfrenta isso: seu painel `participant` não é multi-tenant e admite participante autenticado sem compra prévia.

Três alternativas foram apresentadas ao usuário: cadastro antes do pagamento; manter o checkout público como está; ou página de checkout Filament fora do painel Student.

## Decision

Implementar o checkout como **página Filament acessível sem autenticação, fora do painel Student**.

- O visitante permanece anônimo até o pagamento ser confirmado — **DEC-01 preservado integralmente**.
- A página é construída com o schema do Filament (formulário do comprador, resumo dos itens, ação de gerar PIX), obtendo a identidade visual e a validação nativa do painel sem herdar seu gate de acesso.
- O painel Student continua sendo o destino **pós-pagamento**: a matrícula criada pela concessão de acesso concede também o tenant, via `canAccessTenant()`, que aceita `enrollments()->where('organization_id', …)->active()`.
- Nenhuma alteração no gate de acesso do painel Student.

## Consequences

**Prós**
- DEC-01 intacto: nenhuma conta é criada antes do pagamento, e nenhum usuário órfão surge de carrinhos abandonados.
- Sem problema de sobrevivência do carrinho: não há fronteira de autenticação atravessada, então a sessão do LaraCart permanece contínua.
- Nenhuma flexibilização do gate de acesso ao painel Student — a superfície de autorização não muda.
- Ganho da experiência de painel (schema, notificações, validação do Filament) sem seu custo de autenticação.

**Contras e custos**
- Divergência do padrão do Filacon: quem conhecer os dois projetos não encontrará o checkout no mesmo lugar. Mitigado pelo registro nesta ADR e nas notas do UC-01.
- Uma página Filament pública exige atenção deliberada à autorização: como não há usuário, toda validação de negócio (curso publicado, preço do banco) precisa acontecer na fronteira do serviço, sem depender de policies.
- O comprador não acompanha o pedido dentro do painel antes de pagar; o acompanhamento se dá pela página do QR Code.

**Decisão two-way**
- Reversível (C-03): se no futuro fizer sentido exigir login antes do pagamento, a página pode migrar para dentro de um painel autenticado. Nada no schema de dados fica preso a esta escolha.
