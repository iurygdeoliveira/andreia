# UC-04 — Checkout de carrinho multi-curso

> Gerado pela skill idea-to-spec. Núcleo: `../projeto.md` (§14 ordem 4).
> **Numeração local desta feature:** os IDs RF-xx, RNF-xx, D-xx e RN-xx citados aqui estão definidos em [`../2-specs/uc-04-requisitos.md`](../2-specs/uc-04-requisitos.md), **não** nas tabelas globais do `../projeto.md`.

## Identificação

| Campo | Valor |
|-------|-------|
| ID | UC-04 |
| Nome | Comprar múltiplos cursos em uma única cobrança PIX |
| Ator primário | Visitante comprador (P-01), não autenticado |
| Atores secundários | Gateway Asaas; Webhook Asaas; Sistema de e-mail |
| Objetivo | Converter um carrinho com N cursos em um pedido pago e N matrículas ativas |
| Frequência esperada | Baixa (≤ 500 req/dia, conforme seção 7 do núcleo) |

## Pré-condições

1. O carrinho da sessão contém pelo menos um curso.
2. Todos os cursos do carrinho têm status `Published` (RN-02).
3. O visitante **não** precisa estar autenticado (RN-03, C-02).

## Pós-condições

**Sucesso:**
- Existe um `Order` com status `pending`, contendo N `OrderItem` (um por curso).
- Existe exatamente uma cobrança PIX no Asaas, com `value` igual à soma dos preços e `externalReference` igual ao `order_number`.
- O carrinho da sessão foi destruído (RF-11).
- O comprador visualiza o QR Code.

**Após a confirmação do pagamento (assíncrona, via webhook):**
- O `Order` tem `payment_status` pago e `paid_at` preenchido.
- Existem N matrículas ativas, uma por item do pedido (RF-06).
- O comprador recebeu **um** e-mail listando os N cursos (RF-07).
- Se a conta era nova, o e-mail contém link de definição de senha.
- O aluno consegue acessar `/student/{tenant}` — a matrícula ativa concede o tenant (RN-06).

## Fluxo Principal

| # | Ator | Ação |
|---|------|------|
| 1 | Visitante | Navega o catálogo público e adiciona cursos ao carrinho |
| 2 | Sistema | Registra cada curso no carrinho com quantidade 1 (RN-01); curso já presente não duplica |
| 3 | Visitante | Abre o carrinho e aciona "Finalizar compra" |
| 4 | Sistema | Redireciona para a página de checkout, **preservando todos os itens** (RF-02) |
| 5 | Sistema | Exibe o resumo com todos os cursos, preços individuais e total |
| 6 | Visitante | Preenche nome, e-mail, telefone e CPF |
| 7 | Visitante | Aciona "Gerar cobrança PIX" |
| 8 | Sistema | Revalida que todos os cursos continuam `Published` (RN-02) |
| 9 | Sistema | Calcula o total a partir dos preços do banco, ignorando qualquer valor vindo do request (RN-05) |
| 10 | Sistema | Calcula o fingerprint de idempotência a partir do conjunto **ordenado** de IDs de curso + dados do comprador (RF-08) |
| 11 | Sistema | Cria, em transação única, o `Order` e seus N `OrderItem` (RNF-01) |
| 12 | Sistema | Cria/recupera o cliente no Asaas e gera **uma** cobrança PIX pelo total (RF-03) |
| 13 | Sistema | Persiste `payment_id`, QR Code e expiração no pedido |
| 14 | Sistema | Destrói o carrinho da sessão (RF-11) |
| 15 | Sistema | Exibe o QR Code e as instruções de pagamento |
| 16 | Asaas | Notifica o webhook quando o pagamento é confirmado |
| 17 | Sistema | Autentica o webhook e reconsulta o pagamento na API (fonte da verdade) |
| 18 | Sistema | Marca o pedido como pago e concede acesso a **todos** os cursos do pedido (RF-06) |
| 19 | Sistema | Enfileira **um** e-mail listando os N cursos (RF-07) |
| 20 | Aluno | Acessa o painel Student e vê todos os cursos comprados |

## Fluxos de Exceção

### E-01 — Carrinho vazio ao acessar o checkout
**Gatilho:** passo 4/5, carrinho sem itens (acesso direto por URL ou sessão expirada).
**Tratamento:** redirecionar ao catálogo público, sem criar pedido.
**Pós-condição:** nenhum `Order`, nenhuma chamada ao Asaas.

### E-02 — Curso despublicado entre a adição e a submissão
**Gatilho:** passo 8, um curso do carrinho deixou de ser `Published`.
**Tratamento:** rejeitar a submissão, informar qual curso ficou indisponível e permitir remover o item e prosseguir com os demais.
**Pós-condição:** nenhum pedido criado com curso indisponível; nenhuma chamada ao gateway. (RN-02, preserva SEC-01.)

### E-03 — Curso inexistente ou ID forjado
**Gatilho:** passo 8, item do carrinho referencia curso que não existe ou não é publicado.
**Tratamento:** rejeitar sem revelar dados do curso.
**Pós-condição:** nenhum `Order`, nenhum `Lead`, nenhuma chamada ao Asaas.

### E-04 — Dupla submissão do mesmo carrinho (double-click)
**Gatilho:** passo 7 acionado duas vezes com o mesmo conjunto de cursos e comprador.
**Tratamento:** o fingerprint do conjunto ordenado devolve o pedido já criado (RF-08).
**Pós-condição:** **um** pedido, **uma** cobrança PIX. Nunca duas.

### E-05 — Falha de comunicação com o Asaas
**Gatilho:** passo 12, timeout ou erro do gateway.
**Tratamento:** preservar o pedido pendente e o Lead (retomada comercial), registrar log, informar falha ao comprador sem expor detalhe técnico (RNF-04).
**Pós-condição:** pedido `pending` sem `payment_id`; nenhuma exceção não tratada exibida.

### E-06 — QR Code não retornado pelo gateway
**Gatilho:** passo 13, cobrança criada mas QR Code indisponível.
**Tratamento:** manter a cobrança e permitir nova consulta do QR Code.
**Pós-condição:** pedido com `payment_id` e QR Code recuperável.

### E-07 — Webhook reentregue (RECEIVED e CONFIRMED)
**Gatilho:** passo 16 ocorre mais de uma vez para o mesmo pagamento.
**Tratamento:** concessão idempotente — nem matrícula duplicada, nem segundo e-mail. `paid_at` gravado uma única vez.
**Pós-condição:** N matrículas (não 2N), um e-mail.

### E-08 — Comprador já possui conta
**Gatilho:** passo 18, existe usuário com o e-mail informado.
**Tratamento:** reaproveitar a conta sem tocar em senha, aprovação ou verificação; o e-mail **não** leva link de definição de senha.
**Pós-condição:** matrículas adicionadas à conta existente.

### E-09 — Comprador já matriculado em um dos cursos do pedido
**Gatilho:** passo 18, já existe matrícula ativa para um dos cursos.
**Tratamento:** a matrícula existente é preservada; as demais são criadas normalmente.
**Pós-condição:** nenhuma matrícula duplicada; o pedido é concluído.

### E-10 — Estorno integral do pagamento
**Gatilho:** webhook de estorno após a concessão.
**Tratamento:** revogar o acesso aos cursos **daquele pedido**, preservando direitos originados de outros pedidos válidos (RF-09).
**Pós-condição:** cursos exclusivos do pedido estornado ficam inacessíveis; os demais permanecem.

### E-11 — Pagamento pertencente a outro sistema
**Gatilho:** webhook com `payment_id` desconhecido.
**Tratamento:** responder 200 sem efeito (a fila do Asaas é serial e pausa após falhas consecutivas).
**Pós-condição:** nenhum pedido alterado.

## Critérios de Aceite

| # | Critério | Verificação |
|---|----------|-------------|
| CA-01 | Um carrinho com 3 cursos gera 1 pedido com 3 itens | Teste de feature |
| CA-02 | Um carrinho com 3 cursos gera exatamente 1 cobrança PIX, com valor igual à soma | Teste de feature com Asaas simulado |
| CA-03 | O total do pedido é a soma dos preços do banco, imune a valor vindo do request | Teste adversarial |
| CA-04 | Um curso não pode aparecer duas vezes no mesmo pedido | Constraint no banco (RNF-02) |
| CA-05 | A quantidade de todo item é 1 | Schema (RN-01) |
| CA-06 | Carrinho vazio no checkout redireciona sem criar pedido | Teste de feature |
| CA-07 | Curso despublicado impede a cobrança e não cria pedido | Teste adversarial |
| CA-08 | Dupla submissão do mesmo carrinho gera um único pedido | Teste de concorrência |
| CA-09 | Pagamento confirmado gera N matrículas ativas | Teste de feature do webhook |
| CA-10 | Pagamento confirmado gera exatamente 1 e-mail, listando N cursos | Assert de fila de e-mail |
| CA-11 | Webhook reentregue não duplica matrícula nem e-mail | Teste de idempotência |
| CA-12 | O aluno acessa `/student/{tenant}` e vê todos os cursos comprados | Teste de feature autenticado |
| CA-13 | Estorno revoga apenas os direitos exclusivos do pedido estornado | Teste adversarial |
| CA-14 | Falha do Asaas preserva pedido pendente e não vaza detalhe técnico | Teste com HTTP simulado falhando |
| CA-15 | Concluído o pedido, o carrinho da sessão está vazio | Teste de feature |
| CA-16 | `orders` não possui mais `course_id`; nenhum código lê essa coluna | Migration + suíte verde |

## Impactos Técnicos Mapeados

> Levantados por inspeção do código atual; insumo para a `sdd-plan`.

| Arquivo / artefato | Impacto |
|---|---|
| `database/migrations/..._create_orders_table.php` | Remover `course_id` e o índice `['course_id','created_at']` |
| Nova migration `order_items` | `order_id`, `course_id`, `unit_price`, unique `(order_id, course_id)` |
| `app/Models/Order.php` | Remover `course()`; adicionar `items()` |
| Novo `app/Models/OrderItem.php` | `belongsTo` Order e Course |
| `app/Services/CheckoutService.php` | Assinatura passa a receber a coleção de cursos; fingerprint por conjunto ordenado; total somado |
| `app/Actions/GrantCourseAccessAction.php` | Fan-out: itera itens, cria N matrículas; e-mail único |
| `app/Actions/RevokeCourseAccessAction.php` | Revogação por item, preservando direitos de outros pedidos |
| `app/Mail/CourseAccessGrantedMail.php` | Recebe coleção de cursos em vez de um `Course` |
| `app/Http/Controllers/AsaasWebhookController.php` | Continua orientado ao pedido; a concessão é que muda |
| `app/Livewire/Public/CoursesCatalog.php:200` | Remover `reset($items)`; redirecionar carrinho inteiro |
| `app/Livewire/Public/CoursePurchase.php:184` | Idem; "comprar agora" passa a usar o carrinho (RF-10) |
| Nova página Filament de checkout | Fora do painel Student, acessível sem autenticação |
| `routes/web.php` + `CheckoutController` | Rota de checkout individual removida (RF-10) |
| Views públicas do carrinho | Remover o aviso de que só o primeiro item é cobrado |

## Notas de Referência

O projeto **Filacon** (`/home/iury/orca/workspaces/filacon/Filacon`) implementa o padrão equivalente e serve de modelo estrutural:

- `app/Filament/Participant/Pages/Checkout.php` — página de checkout com formulário do comprador + `RepeatableEntry` dos itens + ação de conclusão.
- `app/Services/CheckoutService.php` — `processPixCheckout` recebendo `array $cartItems` e criando `order_items` em transação.
- `resources/views/pages/event/view/view.php` — carrinho em slide-over redirecionando para o checkout.

**Diferenças que impedem cópia literal:**
1. O Filacon vende ingressos **com quantidade**; o EAFS vende matrículas — quantidade sempre 1 (RN-01).
2. O checkout do Filacon exige participante autenticado; o do EAFS é anônimo (C-02, preserva DEC-01).
3. O painel Student do EAFS é multi-tenant; por isso o checkout fica **fora** dele (C-03).
4. O `order_number` do Filacon usa `Str::random(8)`; o EAFS já usa ULID por causa de colisão sob constraint unique.
