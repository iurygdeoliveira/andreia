# REVIEW — Revisão da análise Asaas/Filament do Codex (UC-04)

- **Estágio SDD:** 7 (`sdd-analyze`) — revisão dos apontamentos de `docs/specs/analise-asaas-filament-2026-09-29.md` (Codex)
- **Auditor:** Claude Code (REVIEWER + AUDITOR)
- **Data:** 2026-09-29
- **Base auditada:** HEAD `dbc6972` + working tree (alterações locais não commitadas)
- **Destinatário das correções:** agy (Worker, estágio 5). Itens marcados **CODEX** não devem ser implementados pelo agy.
- **Relacionados:** `sdd/7-reviews/REVIEW-uc-04-checkout-carrinho-multi-curso.md` (14/09), `sdd/1-adrs/0005-idempotencia-por-conjunto-ordenado-de-cursos.md` (§ "Fora de escopo": R1 = F-01, R2 = F-02), `sdd/2-specs/uc-04-requisitos.md` (RF-08, RF-09).
- **Fontes Asaas verificadas nesta revisão (MCP `docs.asaas.com/mcp`):** `get-endpoint GET /v3/payments/{id}` (enum de status, `refunds[]`, `deleted`, `billingType`), `GET /v3/payments` (filtro `externalReference`), docs `estornos`, `webhook-para-cobrancas`, `cobrança-duplicada-após-retry-sem-idempotência`.

---

## VEREDITO: **NÃO APROVADO para produção**

Todos os seis apontamentos do Codex procedem no código atual. Ajustei o escopo de três deles (F-01, F-05, F-06) para a menor correção suficiente. Separei ainda o que é decisão de arquitetura, que pertence ao Codex, do que é correção direta, que pertence ao agy.

**Bloqueiam produção:**
1. F-01, F-02, F-03, F-04, F-05, F-06-A e F-07 (regra nova decidida pelo usuário): o agy corrige conforme este documento.
2. F-06-B, a recepção durável do webhook: planejamento do **CODEX**.
3. OPS-01, filas sem consumidor (herdado de 14/09): o usuário decidiu usar `sync`, e o agy ajusta a configuração.

**Não é necessária nenhuma migration** para as correções atribuídas ao agy.

---

## 1. Divergências em relação à proposta do Codex (Regra 7)

| Item | Proposta do Codex | Decisão desta revisão | Motivo |
|---|---|---|---|
| F-01 | Exigir prova de posse (segredo de sessão) também em `show`/`status` e em fluxo próprio entre dispositivos | **Corrigir apenas os dois caminhos de retomada no `CheckoutService`**. `show`/`status` continuam usando o ULID como credencial | `order_number` é ULID (80 bits aleatórios, `Order.php:98`) e as rotas têm throttle (`routes/web.php:41,45`). O ULID só vaza hoje pela retomada por e-mail+CPF. Vincular `show`/`status` à sessão quebraria abrir o link em outro dispositivo sem ganho real |
| F-02 | "Não repetir o POST imediatamente após busca vazia" | Tornar concreto: **nunca** fazer POST na mesma requisição que teve timeout. Na **próxima tentativa do usuário**, fazer GET por `externalReference`: 0 resultados → POST; 1 → vincular; >1 → falhar e registrar em log | Sem uma regra operacional, o agy criaria um loop ou nunca faria o retry |
| F-05 | Modelar origem e vigência de cada direito de acesso (concessões independentes) | **Agy:** incluir `PartiallyRefunded` nos pedidos que preservam acesso. **CODEX:** modelar a origem das concessões | Hoje não existe concessão independente no app: `Enrollment` só é criado em `GrantCourseAccessAction` e em seeders. Modelar isso é uma feature nova, não uma correção |
| F-06 | Inbox durável + worker + deduplicação por ID de evento | **Agy (F-06-A):** validar o vínculo do pagamento reconsultado. **CODEX (F-06-B):** inbox durável, **bloqueante** | Ver F-06. O risco real é a fila do Asaas pausar, mais do que a duplicidade, que já é idempotente |

---

## 2. Fundação comum — `OrderStatus::grantsAccess()` (fazer primeiro)

F-03 e F-05 precisam do mesmo conceito: **status que dão direito de acesso** = `Received`, `Confirmed`, `PartiallyRefunded`. A política vigente mantém o acesso em estorno parcial (`RevokeCourseAccessAction` docblock; `AsaasWebhookController:77-81`).

**Alteração em `app/Enums/OrderStatus.php`:**
- Manter `isPaid()` como está (`Received`, `Confirmed`).
- Adicionar `grantsAccess(): bool` (`Received`, `Confirmed`, `PartiallyRefunded`).
- Adicionar `public static function accessGranting(): array` com a lista, para uso em `whereIn`.
- Em `Order.php`, adicionar `grantsAccess()`, que delega ao enum, como já é feito com `isPaid()` em `Order.php:145`.

**Tratamento obrigatório de cada chamador de `isPaid()`** (não decidir caso a caso):

| Local | Troca para `grantsAccess()`? |
|---|---|
| `GrantCourseAccessAction.php:36` `abort_unless(...)` | **Sim** |
| `AsaasWebhookController.php:73` ramo de concessão | **Sim** (ver F-03) |
| `AsaasWebhookController.php:109` carimbo de `paid_at` | **Sim** (C3 continua valendo: grava só se `null`) |
| `CheckoutService.php:185` `recoverPixQrCode` | **Sim** |
| `OrderPaymentController.php:42-43` `pago`/`redirect` | **Sim** |
| `resources/views/public/pages/pagamento.blade.php:14` e `:88` | **Sim** |
| `RevokeCourseAccessAction.php:217` | **Sim**, via `OrderStatus::accessGranting()` (ver F-05) |

---

## 3. Itens

### F-01 — Retomada de pedido por e-mail+CPF entrega o pedido de outra sessão · **CONFIRMADO com ajuste** · Severidade: Média (exposição de dados pessoais/LGPD) · **agy**

**Evidência.** Existem dois caminhos de retomada. Ambos resolvem o pedido apenas pelo fingerprint (cursos + e-mail + CPF), sem vínculo com quem pede:
- Cache: `CheckoutService.php:69-75` (fora do lock) e `:127-133` (dentro do lock). O caminho de fora do lock só exclui `Cancelled`/`Overdue`, então **também devolve pedidos pagos e estornados**. O de dentro do lock não filtra status nenhum.
- Banco: `CheckoutService.php:80-104`.

Quem conhece o e-mail e o CPF de outra pessoa monta o mesmo carrinho e é redirecionado para `/pedido/{ULID}` da vítima (`Checkout.php:168`). Nessa página vê que a vítima tem pedido desses cursos e, se já estiver pago, o e-mail dela (`pagamento.blade.php:23`).

**Correção:**
1. `CheckoutService::processPixCheckout()`: adicionar um **último** parâmetro `array $ownedOrderIds = []`. Manter `$idempotencyKey`, que a ADR-0005 preserva. O padrão vazio significa "nada a retomar", que é o comportamento seguro.
2. Os **três** pontos de retomada (`:69-75`, `:127-133`, `:80-104`) só podem devolver um pedido cujo ID esteja em `$ownedOrderIds` **e** cujo status seja `Pending`. No banco, use `->whereKey($ownedOrderIds)`. No cache, verifique `in_array($cachedOrderId, $ownedOrderIds, true)` e o status.
3. O fingerprint continua como está (ADR-0005). Ele segue servindo de chave de lock e de cache. Escopar a retomada é política de autorização, que a ADR-0005 deixou explicitamente para esta auditoria, então **não há desvio de ADR**.
4. `Checkout::confirmar()`:
   - passar `session('checkout.order_ids', [])` ao serviço;
   - depois do sucesso, gravar o ID na sessão, mantendo só os 10 mais recentes e sem duplicar;
   - tornar o método `protected`, porque hoje é público e chamável direto via Livewire. A closure de `gerarCobrancaAction()` continua funcionando.

**Não fazer:**
- vincular `show`/`status` à sessão;
- gravar `user_id` no pedido na criação. `GrantCourseAccessAction::resolveUser` resolve a conta pelo `buyer_email` e isso geraria conflito.

**Teste mínimo** (Pest + `Http::fake`):
- Sessão A cria o pedido. A sessão B, com o mesmo e-mail, CPF e cursos, recebe **outro** pedido e nunca o `order_number` de A.
- A mesma sessão submetendo duas vezes recebe o mesmo pedido, com 1 único POST `/payments`.
- Um pedido pago nunca é retomado.

**Pronto quando:** esses testes passarem.

---

### F-02 — Timeout e concorrência podem gerar cobranças duplicadas · **CONFIRMADO com números** · Severidade: Alta · **agy**

**Evidência:**
- O lock tem TTL de 15 s (`CheckoutService.php:126`), mas até 4 chamadas HTTP sequenciais de até 35 s cada (`connectTimeout(5)` + `timeout(30)`, `AsaasService.php:366-367`) rodam dentro dele. São elas: `findCustomer`, `createCustomer`, `createPixCharge` e `getPixQrCode`.
- Quando há erro, o pedido fica `Pending` sem `payment_id`. A próxima submissão não o encontra, porque `:84` exige `whereNotNull('payment_id')`, e cria **outro pedido com outra `externalReference`**, mesmo que a primeira cobrança tenha sido criada no Asaas.

**Correção:**
1. `AsaasService::client()`: `timeout(10)`, mantendo `connectTimeout(5)`. Cada chamada leva no máximo cerca de 15 s.
2. Tirar `recoverPixQrCode()` de dentro do lock e chamá-lo depois que o lock for liberado. Ele é idempotente, e `OrderPaymentController::show` já o chama de novo.
3. Lock TTL de **75 s**. O pior caso dentro do lock tem 4 chamadas (`findCustomer` + `createCustomer` + `findPaymentsByExternalReference` + `createPixCharge`), cerca de 60 s. Manter `block(5)`.
4. Criar `AsaasService::findPaymentsByExternalReference(string $ref): array` com `GET /payments?externalReference=...`, descartando itens com `deleted === true`.
5. Retomar **pedido órfão**. A consulta de candidatos (já escopada pelo F-01) passa a aceitar também `Pending` **sem** `payment_id` criado há no máximo 30 minutos. Para esse pedido, **antes** de qualquer POST, chamar `findPaymentsByExternalReference($order->order_number)` e seguir esta tabela:

   | Resultado | Ação |
   |---|---|
   | 1 cobrança, `billingType === 'PIX'` e valor em centavos igual a `total_amount` em centavos | Vincular `payment_id` e `asaas_customer_id`, sem POST |
   | 1 cobrança divergente, ou mais de 1 | `Log::error` com `order_number` e IDs, e retornar `null` para reconciliação manual. **Nunca** escolher uma delas |
   | 0 cobranças | POST com a **mesma** `externalReference`. Isso só ocorre nesta tentativa posterior, iniciada pelo usuário |
   | GET falhou | Retornar `null` |

6. Na requisição que teve timeout ou erro, manter o comportamento atual (`return null`) **sem** retry automático.
7. Comparar valores sempre em centavos inteiros, `(int) round($reais * 100)`, porque o `MoneyCast` expõe reais.

**Não fazer:**
- assumir um header `Idempotency-Key` na criação de cobrança (não documentado);
- tratar `externalReference` como único do lado do Asaas.

**Teste mínimo** (`Http::fake` com sequência):
- (a) O 1º POST lança `ConnectionException`. A 2ª submissão da mesma sessão faz GET, encontra 1 cobrança, vincula e faz **0** POSTs novos. Resultado: 1 pedido.
- (b) Sequência com GET vazio: 1 POST na 2ª tentativa, com a mesma `externalReference`.
- (c) GET com 2 cobranças: retorna `null` e não faz POST.

---

### F-03 — Estorno parcial nunca é reconhecido · **CONFIRMADO** · Severidade: Média (conciliação/relatório; o acesso já é mantido) · **agy**

**Evidência (MCP):**
- O enum `PaymentGetResponsePaymentStatus` **não tem** `PARTIALLY_REFUNDED` nem `CANCELLED`. Os valores são: `PENDING`, `RECEIVED`, `CONFIRMED`, `OVERDUE`, `REFUNDED`, `RECEIVED_IN_CASH`, `REFUND_REQUESTED`, `REFUND_IN_PROGRESS`, `CHARGEBACK_*`, `DUNNING_*` e `AWAITING_RISK_ANALYSIS`.
- Em estorno parcial, a cobrança continua `RECEIVED`/`CONFIRMED` e só o evento `PAYMENT_PARTIALLY_REFUNDED` e o array `refunds[]` indicam o estorno.
- Por isso `OrderStatus::PartiallyRefunded` é inalcançável (`AsaasWebhookController.php:65,77`).
- A documentação `estornos` pede: somar só os itens `DONE`, ignorar `PENDING`/`CANCELLED` e nunca substituir o valor original.

**Correção:**
1. `AsaasService::getPayment()`: ampliar o `@return` para incluir `billingType`, `deleted` e `refunds?: array<int, array{status: string, value: float}>`.
2. No webhook, depois de `tryFromAsaas`, se o status for `isPaid()`:
   - `refundedCents` = soma, em centavos, dos `refunds` com `status === 'DONE'`;
   - `valueCents` = `payment.value` em centavos;
   - se `0 < refundedCents < valueCents`, o status final é `PartiallyRefunded`;
   - se `refundedCents >= valueCents`, o status final é `Refunded`.
3. Ramificação do webhook:
   - `grantsAccess()` → `GrantCourseAccessAction`. Continua idempotente e cobre o caso de o primeiro webhook processado já chegar parcialmente estornado.
   - `Refunded` → `RevokeCourseAccessAction`.
   - Remover o ramo exclusivo de `PartiallyRefunded` (`:77-81`), porque ele passa a ser coberto pelo primeiro. Manter o `Log::info` de estorno parcial dentro do novo fluxo.
4. Os status `REFUND_REQUESTED`/`REFUND_IN_PROGRESS` continuam sem mapeamento. O acesso fica mantido até `REFUNDED`, e esse é o comportamento desejado. Não mapear.
5. Sem migration. Não persistir o valor estornado agora. Se o Codex quiser a conciliação exposta no `OrderResource`, isso vira uma spec própria.

**Teste mínimo:**
- `refunds` com um `DONE` parcial vira `partially_refunded` e a matrícula continua ativa.
- `refunds` com `PENDING` e `CANCELLED` deixa o pedido em `received`.
- Parciais `DONE` somando o valor total viram `refunded` e a matrícula é revogada.

**Validação complementar (usuário/Grok):** confirmar o payload real no Sandbox (recipe "Estorno de cobranças em Sandbox").

---

### F-04 — Recompra não reativa matrícula cancelada/expirada · **CONFIRMADO** · Severidade: Alta (aluno paga e não recebe acesso) · **agy**

**Evidência.** `GrantCourseAccessAction.php:125`: `firstOrCreate` devolve a matrícula existente `Cancelled` ou expirada sem alterá-la, porque existe um unique em `(organization_id, user_id, course_id)`.

**Correção:**
1. Em `grant()`, **antes** de qualquer escrita, calcular `$firstGrantForOrder = $order->access_mail_queued_at === null`. O pedido já está sob `lockForUpdate` (`:35`). Explicar em comentário que `access_mail_queued_at` é gravado na mesma transação da primeira concessão e, por isso, funciona como marcador de "este pedido já concedeu acesso".
2. Passar esse valor para `enroll()`. Se `$firstGrantForOrder` for verdadeiro, a matrícula não for recém-criada (`! wasRecentlyCreated`) e ela estiver (`status !== Active` **ou** `expires_at` no passado), atualizar para `status = Active` e `expires_at = now()->addYear()`, a mesma regra da criação. A vigência de 1 ano foi confirmada pelo usuário em 2026-09-29 (CODEX-02).
3. Matrícula ativa e não expirada: **não mexer**. O usuário decidiu em 2026-09-29 que essa recompra não deve acontecer (ver F-07). Se ainda assim ela chegar aqui (dois pedidos pendentes do mesmo curso, ambos pagos), registrar `Log::warning` com `order_number` e `course_id` e a mensagem "compra redundante: matrícula já ativa; avaliar estorno manual". **Não** estornar automaticamente e **não** estender o prazo.

**Teste mínimo:**
- Matrícula `Cancelled` + novo pedido pago resulta em `Active` com `expires_at` no futuro.
- Reentrega do mesmo pedido após `travel(1)->month()` deixa `expires_at` inalterado.
- A recompra não envia um segundo e-mail do pedido anterior.

---

### F-05 — Revogação ignora pedidos parcialmente estornados · **CONFIRMADO com escopo reduzido** · Severidade: Média · **agy** (parte) / **CODEX** (parte)

**Evidência.** `RevokeCourseAccessAction.php:217` considera como preservadores apenas `Received`/`Confirmed`.

**Correção (agy):**
- Trocar por `->whereIn('payment_status', OrderStatus::accessGranting())`.
- Nada além disso.

**Teste mínimo:** pedido A estornado integralmente e pedido B `PartiallyRefunded` do mesmo curso para o mesmo usuário. A matrícula continua ativa.

**Fora do escopo do agy → CODEX-01.**

---

### F-06 — Webhook: validação de vínculo (A) e recepção durável (B) · **CONFIRMADO, dividido**

**F-06-A · Severidade: Média · agy.** Depois de `getPayment()` (`AsaasWebhookController.php:63`), antes de `applyStatus`, validar:
- `externalReference === $order->order_number`;
- `billingType === 'PIX'`;
- `deleted !== true`;
- valor em centavos igual a `total_amount` em centavos.

Se alguma divergir: `Log::error` com `order_number`, `payment_id` e o campo divergente, e responder **200 sem alterar o pedido**. Não responder 500 pelos motivos da C2b. Hoje o valor pode ser alterado no painel do Asaas (`PAYMENT_UPDATED`) e o acesso seria concedido mesmo assim.

**Teste mínimo:** payload reconsultado com valor diferente, ou com `billingType: BOLETO`, deixa o pedido inalterado, não cria matrícula e responde 200.

**F-06-B · Severidade: Alta (bloqueia produção) · CODEX.** A C2 responde não-2xx em falhas internas, e o Asaas **pausa a fila** após 15 falhas consecutivas (comentário em `AsaasWebhookController.php:26-28`). Um erro determinístico no caminho de concessão (por exemplo, um bug em `GrantCourseAccessAction` ou uma falha permanente de mail/fila) trava as confirmações de **todas** as vendas seguintes. A duplicidade, por outro lado, já está coberta pela reconsulta + `firstOrCreate` + `access_mail_queued_at`. Por isso o risco dominante é a pausa da fila, e não a deduplicação por ID de evento.

É necessário projetar a recepção durável (persistir o evento e responder 200, processar em worker, com retry e monitoramento). Isso exige migration e ADR, então o agy **não implementa**.

---

### F-07 — Bloquear a compra de curso com matrícula ativa e vigente · **REGRA NOVA (decisão do usuário em 2026-09-29)** · Severidade: Alta (evita cobrar por um acesso que o aluno já tem) · **agy**

**Regra.** A compra **não prossegue** se o comprador já tiver, para **qualquer** curso do carrinho, uma matrícula `Active` com `expires_at` nulo ou no futuro. Nesse caso não se cria pedido nem se chama o Asaas. Com matrícula `Cancelled` ou expirada, a compra é permitida e reativa o acesso por 1 ano (F-04).

**Quem é o comprador.** É a conta encontrada pelo `buyer_email`, a mesma regra de `GrantCourseAccessAction::resolveUser` (`:88`), porque é essa conta que recebe o acesso. Não usar `auth()->user()` nessa verificação.

**Correção:**
1. Criar `app/Exceptions/CourseAlreadyActiveException.php` (a pasta ainda não existe), carregando a `Collection` dos cursos já ativos. Use uma exceção, e não `null`: o `null` já significa "falha no Asaas ou limite excedido", e a tela precisa mostrar uma mensagem diferente.
2. `CheckoutService::processPixCheckout()`: logo depois da validação de cursos publicados (`:55-63`) e **antes** de qualquer retomada (cache ou banco), fazer a verificação:
   - `User` pelo `buyer->email`;
   - se existir, buscar `Enrollment` desse usuário com `whereIn('course_id', ...)`, `status = Active` e `expires_at` nulo ou maior que `now()`;
   - se houver resultado: `RateLimiter::hit` na mesma chave do limite existente (`:108`), para limitar a sondagem de e-mails, e em seguida lançar a exceção.
   
   A verificação fica no serviço, e não só na página, para valer também em chamadas diretas via Livewire. Ficando antes da retomada, ela também impede retomar um pedido pendente antigo de um curso que, nesse meio-tempo, ficou ativo por outro pedido.
3. `Checkout::confirmar()`: capturar a exceção, enviar uma `Notification` de aviso e **não** limpar o carrinho. A mensagem depende de quem está comprando:
   - **Usuário autenticado com `email` igual ao e-mail digitado:** listar os cursos. Exemplo: "Você já tem acesso ativo a: {títulos}. Remova-os do carrinho para continuar."
   - **Qualquer outro caso, incluindo visitante:** mensagem neutra, **sem nomear cursos**. Exemplo: "Não foi possível concluir a compra com este e-mail. Se você já é aluno, entre na sua conta para ver seus cursos."
   
   Motivo da mensagem neutra: impedir que alguém descubra em quais cursos outra pessoa está matriculada só digitando o e-mail dela. É a mesma classe de problema do F-01 (LGPD). O risco que sobra (a mensagem é diferente das demais) é aceito, e a sondagem fica limitada pelo `RateLimiter` do item 2.
4. Um carrinho misto (curso ativo + curso novo) é bloqueado **inteiro**. O comprador remove o curso ativo e tenta de novo.

**Não fazer:**
- bloquear só na interface (carrinho ou página do curso). Avisos na interface são opcionais e ficam fora deste escopo;
- estornar ou estender o prazo automaticamente no webhook (ver F-04 §3).

**Teste mínimo** (Pest + `Http::fake`):
- Usuário com matrícula ativa e vigente no curso X, carrinho `[X]` com o e-mail dele: exceção, 0 pedidos criados e `Http::assertNothingSent()`.
- Carrinho `[X, Y]` com X ativo: bloqueia tudo, 0 pedidos.
- Matrícula de X expirada (`expires_at` no passado) ou `Cancelled`: o checkout prossegue normalmente.
- Visitante usando o e-mail de um aluno: a notificação **não** contém o título do curso.
- Pedido pendente antigo do curso X não é retomado depois que a matrícula de X ficou ativa.

---

## 4. Itens para o Codex (planejamento; o agy não implementa)

| ID | Tema | Por quê |
|---|---|---|
| **CODEX-01** | Origem das concessões de acesso (compra × concessão manual) | F-05 completo. Hoje só existem seeders, mas uma matrícula concedida manualmente seria cancelada pelo estorno de uma compra do mesmo curso |
| **CODEX-02** | Política de vigência — **DECIDIDO pelo usuário em 2026-09-29: acesso válido por 1 ano** | O código atual (`expires_at = now()->addYear()`, `GrantCourseAccessAction.php:128`) está correto e o agy não altera nada. Cabe ao Codex apenas corrigir o texto da ADR-0005, que ainda afirma "o acesso é vitalício por matrícula". Recompra com matrícula ativa e vigente: **também decidido pelo usuário, a compra não prossegue**. Virou ordem para o agy no F-07 |
| **CODEX-03** | Recepção durável do webhook (F-06-B) | Bloqueia produção. Precisa de ADR + tasks |

---

## 5. Operação

**OPS-01 · Bloqueia produção · agy (configuração).** Nenhum processo consome as filas. Este item foi herdado da revisão de 14/09 e detalhado em 29/09.

**Onde ficam os jobs.** O app usa duas conexões de fila:

| Conexão | Driver | O que vai para ela |
|---|---|---|
| `course-access` (`config/queue.php:36`) | `database`, tabela `jobs` | `CourseAccessGrantedMail` (`GrantCourseAccessAction.php:75`). O driver `database` é intencional: o job é gravado na mesma transação da matrícula |
| padrão, `QUEUE_CONNECTION=redis` (`.env:40`) | `redis` | `OrganizationInviteMail` e a notificação `NewDevice` (ambas `ShouldQueue`) |

**Evidência de que nada consome essas filas:**
- `docker/8.5/supervisord.conf` só declara `[program:php]`, que é o servidor web.
- O script `composer dev` (`composer.json:117`) roda `queue:listen` apenas na conexão padrão (redis). **Nem em desenvolvimento** a conexão `course-access` é consumida.
- O scheduler (`schedule:work` ou o cron de `schedule:run`) também não está declarado. Com isso, `backup:run` e `backup:clean` (`routes/console.php:11-12`) nunca rodam.

**Impacto.** O pagamento é confirmado e a matrícula fica ativa, mas o e-mail fica parado na tabela `jobs`. O aluno **novo** tem uma senha aleatória (`GrantCourseAccessAction.php:104`). Por isso ele só consegue entrar se descobrir sozinho o "esqueci minha senha" do painel de autenticação.

**Decisão do usuário (2026-09-29): começar com filas `sync`, sem worker.** Voltar a usar um worker fica para depois.

**Correção (agy, somente configuração):**
1. `.env` e `.env.example`: `QUEUE_CONNECTION=sync`. Isso cobre o convite de organização e o `NewDevice`.
2. `config/queue.php`, conexão `course-access`: trocar o driver para `sync` e atualizar o comentário da linha 35. **Só o passo 1 não basta**: `GrantCourseAccessAction.php:75` fixa `->onConnection('course-access')`, que continua em `database` até ser alterado. Não mexer no `GrantCourseAccessAction`. Mantendo o nome da conexão, para voltar a usar worker basta trocar o driver de novo.
3. Não mexer no `composer dev`: o `queue:listen` fica ocioso, mas não atrapalha.

**Consequências aceitas com `sync`** (o agy não trata; ficam registradas):
- **O e-mail de acesso é enviado dentro da transação do webhook.** Se o SMTP falhar, a matrícula sofre rollback, o webhook responde 500 e o Asaas reenvia. O e-mail não se perde, mas uma indisponibilidade longa do SMTP acumula falhas até a pausa da fila do Asaas (15 consecutivas). Isso **reforça o CODEX-03**.
- O webhook e o envio de convites passam a esperar o tempo de envio do SMTP.
- O `NewDevice` (`config/authentication-log.php:40`) é enviado durante o login. Com o SMTP fora do ar, o login pode falhar. Mitigação disponível: `NEW_DEVICE_NOTIFICATION=false`.

**Continua pendente (operação no servidor):** o cron de `schedule:run` a cada minuto, para os backups diários de `routes/console.php:11-12`.

**Pronto quando:**
- um pagamento aprovado no Sandbox faz o e-mail de acesso chegar sem nenhum worker rodando;
- a tabela `jobs` continua vazia.

---

## 6. Ordem de aplicação para o agy

1. §2 — `grantsAccess()` + troca nos chamadores.
2. F-01.
3. F-02 (depende do escopo de retomada do F-01).
4. F-03 + F-06-A (mesmo arquivo; aplicar juntos).
5. F-04.
6. F-07 (no `CheckoutService`, antes da retomada criada no F-01).
7. F-05.
7.1. OPS-01 (somente `.env`, `.env.example` e `config/queue.php`).
8. `rtk ./vendor/bin/sail test` com a suíte completa verde, mais `rtk ./vendor/bin/sail pint --dirty`.
9. Handoff via ai-memory para o Grok (estágio 6), que escreverá a suíte adversarial: corrida em PostgreSQL, navegador, Sandbox.

**Regras para o agy:**
- Não criar migrations.
- Não alterar a ADR-0005.
- Não implementar os itens CODEX-*.
- Os testes mínimos listados acima são regressões do Worker e não substituem o estágio 6.
