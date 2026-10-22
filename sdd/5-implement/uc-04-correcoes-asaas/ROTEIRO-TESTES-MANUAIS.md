# Roteiro de Verificação Manual — UC-04: Correções Asaas

> **Objetivo:** Fornecer um guia passo a passo, executável e reproduzível para verificar manualmente todas as regras e proteções implementadas no fluxo de checkout Asaas (UC-04, AC-1 a AC-9).  
> **Ambiente Requerido:** Laravel Sail (Docker) ativo (`app`, `pgsql`, `redis`, `mailpit`).

---

## 🛠️ 1. Preparação do Ambiente

Antes de iniciar os testes manuais, garanta que os serviços estejam em execução e o cache de configuração limpo:

```bash
# 1. Verificar containers em execução
rtk ./vendor/bin/sail ps

# 2. Limpar caches de configuração e rotas
rtk ./vendor/bin/sail artisan config:clear
rtk ./vendor/bin/sail artisan route:clear

# 3. Verificar URLs no navegador
# - Aplicação Web: http://localhost (ou a porta configurada no seu .env)
# - Mailpit (E-mails): http://localhost:8025
```

---

## 🧪 2. Casos de Teste Manuais

---

### Teste 1: Compra Nova com PIX e Envio Síncrono de E-mail (AC-1, AC-9)
**O que valida:**
- Criação normal do pedido e vinculação do ID à sessão (`checkout.order_ids`).
- Geração correta da tela de pagamento PIX (QR Code e código copia e cola).
- Processamento do status `Received` concedendo acesso imediato via `grantsAccess()`.
- Envio do e-mail de acesso síncrono via conexão `sync` sem deixar jobs retidos na tabela `jobs`.

#### Passos:
1. Abra o navegador em aba normal ou anônima e acesse a página de cursos / catálogo.
2. Adicione qualquer curso publicado ao carrinho e avance para `/checkout`.
3. Preencha o formulário com dados de um novo comprador fictício:
   - **Nome:** `Aluno Teste Manual 1`
   - **E-mail:** `aluno1.manual@eafs.local`
   - **CPF:** Use um gerador de CPF válido (ex: `000.000.000-00` válido)
   - **Telefone:** `11999998888`
4. Clique em **"Pagar com PIX"**.
5. **Resultado Visual Esperado:**
   - Redirecionamento para a tela `/pagamento/{order_number}`.
   - Exibição visual do QR Code e do campo "Copia e Cola" do PIX.
6. Simule a aprovação do pagamento via terminal (simulação de webhook recebido):
   ```bash
   rtk ./vendor/bin/sail artisan tinker --execute="
   \$order = App\Models\Order::where('buyer_email', 'aluno1.manual@eafs.local')->latest()->first();
   app(App\Actions\GrantCourseAccessAction::class)->execute(\$order);
   echo 'Acesso concedido para pedido: ' . \$order->order_number . PHP_EOL;
   echo 'Status da matrícula: ' . \$order->user->enrollments()->latest()->first()->status->value . PHP_EOL;
   "
   ```
7. **Verificação no Mailpit:**
   - Acesse `http://localhost:8025`.
   - Localize o e-mail enviado para `aluno1.manual@eafs.local`.
   - O e-mail deve conter a notificação de matrícula e os dados para definição de senha.
8. **Verificação na Tabela de Jobs:**
   ```bash
   rtk ./vendor/bin/sail artisan tinker --execute="echo 'Total de jobs pendentes: ' . DB::table('jobs')->count();"
   ```
   - **Resultado esperado:** `Total de jobs pendentes: 0` (o job foi processado de forma síncrona sem fila).

---

### Teste 2: Bloqueio de Recompra com Matrícula Ativa e Proteção LGPD (AC-7)
**O que valida:**
- Aluno logado tentando recomprar curso ativo recebe aviso com o **nome dos cursos** e mantém o carrinho.
- Terceiro/visitante tentando comprar com e-mail de um aluno existente recebe **mensagem neutra sem nomes** (impede enumeração/vazamento de dados).
- `RateLimiter` bloqueia tentativas de sondagem repetidas.

#### Cenário 2.1: Aluno logado tentando recomprar o mesmo curso
1. No painel de login (`/aluno` ou login da plataforma), autentique-se com o e-mail `aluno1.manual@eafs.local`.
2. Adicione ao carrinho o mesmo curso adquirido no **Teste 1**.
3. Acesse `/checkout` e clique em **"Pagar com PIX"**.
4. **Resultado Esperado:**
   - O pedido **não** é gerado.
   - O carrinho **permanece intacto** (os itens continuam no carrinho).
   - Uma notificação flash amarela (Warning) é exibida:
     > *"Você já tem acesso ativo a: [Título do Curso]. Remova-os do carrinho para continuar."*

#### Cenário 2.2: Visitante / aba anônima usando o e-mail de aluno ativo (Proteção LGPD)
1. Abra uma janela anônima no navegador (certifique-se de não estar logado).
2. Adicione o mesmo curso ao carrinho e vá ao `/checkout`.
3. Preencha o e-mail com `aluno1.manual@eafs.local` (o aluno ativo).
4. Clique em **"Pagar com PIX"**.
5. **Resultado Esperado:**
   - O pedido **não** é gerado e o Asaas **não** é chamado.
   - O carrinho é preservado.
   - Uma notificação de aviso neutra é exibida **sem o nome do curso**:
     > *"Não foi possível concluir a compra com este e-mail. Se você já é aluno, entre na sua conta para ver seus cursos."*

---

### Teste 3: Retomada Autorizada pela Sessão (AC-2)
**O que valida:**
- Uma cobrança pendente gerada no checkout pode ser retomada na mesma sessão sem gerar cobrança duplicada no Asaas.
- Uma sessão externa (outro navegador ou aba anônima) **não** consegue retomar cobranças alheias.

#### Passos:
1. Em uma aba normal (Sessão A), adicione um curso novo ao carrinho com outro e-mail: `aluno2.manual@eafs.local`.
2. Clique em **"Pagar com PIX"** e observe a tela com o QR Code. Guarde o número do pedido (ex: `ORD-A`).
3. Sem pagar, retorne para o `/checkout` na mesma aba (Sessão A) e clique em submeter novamente com o mesmo curso.
   - **Resultado Esperado:** A aplicação retoma imediatamente a mesma cobrança pendente (`ORD-A`) que pertence à sessão atual, sem chamar a API do Asaas para criar um novo registro.
4. Agora abra um navegador totalmente diferente (ex: Firefox ou Chrome em modo anônimo — Sessão B).
5. Na Sessão B, adicione o mesmo curso e informe o mesmo e-mail `aluno2.manual@eafs.local`.
6. Clique em **"Pagar com PIX"**.
7. **Resultado Esperado:**
   - A Sessão B **não** herda nem sequestra o pedido `ORD-A`.
   - É gerado um novo pedido independente (`ORD-B`) com novo ID de pagamento vinculado exclusivamente à Sessão B.

---

### Teste 4: Reconciliação Idempotente após Timeout (AC-3)
**O que valida:**
- Em caso de timeout ou submissões quase simultâneas sob lock atômico de 75s, o sistema consulta `findPaymentsByExternalReference` antes de emitir um novo POST ao Asaas.

#### Passos (Simulação via Tinker):
Execute no terminal o cenário de concorrência com cobrança já existente no Asaas com o mesmo `order_number`:
```bash
rtk ./vendor/bin/sail artisan tinker --execute="
\$course = App\Models\Course::first();
\$buyer = new App\DTOs\BuyerDTO('Teste Timeout', 'timeout@eafs.local', '88999998888', '52998224725');
// Executa duas vezes consecutivas para validar que a reconciliação não duplica
\$service = app(App\Services\CheckoutService::class);
\$order1 = \$service->processPixCheckout(new \Illuminate\Database\Eloquent\Collection([\$course]), \$buyer);
echo 'Pedido 1: ' . (\$order1 ? \$order1->order_number : 'nulo') . PHP_EOL;
"
```
- **Resultado Esperado:** Se a cobrança já existir ou se houver retomada sob a sessão, nenhuma segunda cobrança PIX é gerada.

---

### Teste 5: Reativação Idempotente de Matrícula Expirada ou Cancelada (AC-6)
**O que valida:**
- Matrículas canceladas ou expiradas (`expires_at <= now()`) têm o acesso restaurado para `Active` por exatamente 1 ano (`now() + 1 year`).
- Reentregas repetidas do webhook do mesmo pedido não somam anos extras nem reenfileiram novos e-mails.

#### Passos:
1. Simule uma matrícula expirada no banco para o `aluno1.manual@eafs.local`:
   ```bash
   rtk ./vendor/bin/sail artisan tinker --execute="
   \$enrollment = App\Models\Enrollment::whereHas('user', fn(\$q) => \$q->where('email', 'aluno1.manual@eafs.local'))->first();
   \$enrollment->update([
       'status' => App\Enums\EnrollmentStatus::Active,
       'expires_at' => now()->subMonths(2)
   ]);
   echo 'Data anterior de expiração: ' . \$enrollment->expires_at->toDateTimeString() . PHP_EOL;
   "
   ```
2. Agora, com a matrícula expirada, o aluno pode passar pelo checkout (não é bloqueado pela trava do Teste 2).
3. Simule a execução da action de concessão de um novo pedido para essa matrícula:
   ```bash
   rtk ./vendor/bin/sail artisan tinker --execute="
   \$order = App\Models\Order::factory()->withCourses(App\Models\Course::first())->create([
       'buyer_email' => 'aluno1.manual@eafs.local',
       'payment_status' => App\Enums\OrderStatus::Received
   ]);
   app(App\Actions\GrantCourseAccessAction::class)->execute(\$order);
   \$enrollment->refresh();
   echo 'Nova data de expiração (+1 ano): ' . \$enrollment->expires_at->toDateTimeString() . PHP_EOL;
   echo 'Status: ' . \$enrollment->status->value . PHP_EOL;
   "
   ```
4. **Resultado Esperado:**
   - Status: `active`.
   - `expires_at`: Exatamente 1 ano a contar da data de hoje.
5. Execute novamente a action com o mesmo pedido (`$order`):
   ```bash
   rtk ./vendor/bin/sail artisan tinker --execute="
   app(App\Actions\GrantCourseAccessAction::class)->execute(\$order);
   echo 'Reentrega executada sem erro.' . PHP_EOL;
   "
   ```
   - **Resultado Esperado:** A data de expiração permanece a mesma (não soma outro ano) e o Mailpit não recebe e-mail duplicado da reentrega.

---

### Teste 6: Estorno Parcial Preservando Acesso vs Estorno Integral (AC-4, AC-8)
**O que valida:**
- Pedido com status `PartiallyRefunded` continua concedendo acesso ao aluno (`grantsAccess() === true`).
- A action `RevokeCourseAccessAction` preserva a matrícula do aluno caso haja outro pedido `PartiallyRefunded` para o mesmo curso.
- Apenas estorno integral (`Refunded`) sem outro pedido pago revoga o acesso.

#### Passos:
1. Execute a simulação via Tinker:
   ```bash
   rtk ./vendor/bin/sail artisan tinker --execute="
   \$order = App\Models\Order::where('buyer_email', 'aluno1.manual@eafs.local')->latest()->first();
   \$order->update(['payment_status' => App\Enums\OrderStatus::PartiallyRefunded]);
   echo 'Status do Pedido: ' . \$order->payment_status->value . PHP_EOL;
   echo 'Concede Acesso? ' . (\$order->grantsAccess() ? 'SIM' : 'NÃO') . PHP_EOL;
   "
   ```
2. **Resultado Esperado:** `Concede Acesso? SIM`.
3. Verifique na interface web: faça login com `aluno1.manual@eafs.local` e confirme que a área do aluno continua exibindo as aulas normalmente.
4. Agora altere o pedido para `Refunded` (estorno integral):
   ```bash
   rtk ./vendor/bin/sail artisan tinker --execute="
   \$order->update(['payment_status' => App\Enums\OrderStatus::Refunded]);
   app(App\Actions\RevokeCourseAccessAction::class)->execute(\$order);
   \$enrollment = \$order->user->enrollments()->where('course_id', \$order->items->first()->course_id)->first();
   echo 'Status da matrícula após estorno integral: ' . \$enrollment->status->value . PHP_EOL;
   "
   ```
5. **Resultado Esperado:** `Status da matrícula após estorno integral: cancelled`.

---

## 📋 3. Checklist de Verificação Concluída

Marque os itens conforme verificar no seu ambiente:

- [ ] **Teste 1:** Compra PIX gerada com QR Code e e-mail de acesso entregue no Mailpit imediatamente.
- [ ] **Teste 1 (Jobs):** Tabela `jobs` no banco de dados continua vazia (fila `sync`).
- [ ] **Teste 2.1:** Aluno ativo recebe notificação com nomes dos cursos ao tentar recomprar; carrinho preservado.
- [ ] **Teste 2.2:** Terceiro/visitante usando e-mail do aluno recebe mensagem genérica/neutra (sem nomes de curso); proteção LGPD.
- [ ] **Teste 3:** Retomada funciona perfeitamente na mesma sessão; aba anônima não invade cobrança de outra sessão.
- [ ] **Teste 4:** Reconciliação atômica sob lock de 75s não cria cobranças duplicadas.
- [ ] **Teste 5:** Reativação de matrícula expirada ou cancelada soma +1 ano a partir de hoje; reentregas são idempotentes.
- [ ] **Teste 6:** Pedido parcialmente estornado (`PartiallyRefunded`) mantém acesso aberto ao aluno; estorno integral revoga.
