# T06 — Bloqueio de recompra ativa e mensagem privada

> Responsável: agy, estágio 5. Status: CONCLUÍDA. Critérios: AC-7.
> Spec: `sdd/2-specs/uc-04-correcoes-asaas.md`
> Plan: `sdd/3-plans/PLAN-uc-04-correcoes-asaas.md` §5.2–5.3
> Dependências: T05 concluída; executar sequencialmente pelo índice.

## 1. Descrição e objetivo

Bloqueio de recompra ativa e mensagem privada, conforme a revisão de 29/09 e o contrato detalhado abaixo. Não criar migrations ou implementar CODEX-01/03.

## 2. Justificativa técnica

Atende AC-7 e PLAN §5.2–5.3. Reutilizar os componentes existentes reduz o risco de divergência com o checkout atual. As complementações Codex abaixo resolvem lacunas de integração da revisão e prevalecem nesses pontos.

## 3. Critérios de pronto

- [x] Todos os comportamentos e cenários abaixo foram verificados.
- [x] Regressão mínima reproduziu o defeito antes e passou após a correção (quando aplicável).
- [x] Executar testes focados via `rtk ./vendor/bin/sail test`; registrar resultado real e impedimentos.
- [x] Preservar alterações locais alheias, dados e testes de outros agentes.
- [x] Sem migrations, alterações de ADR pelo Worker ou ampliação de escopo.

## 4. Arquivos afetados

- `app/Exceptions/CourseAlreadyActiveException.php`
- `app/Services/CheckoutService.php`
- `app/Filament/Checkout/Pages/Checkout.php`
- Regressões Worker: `tests/Feature/CheckoutAsaas/T06Test.php` (criar ou ampliar equivalente existente sem duplicar).

## 5. Contrato de implementação e verificação

### Complementações do plano (ler primeiro)

Criar CourseAlreadyActiveException carregando a coleção de cursos. Serviço resolve User por buyer_email e matrículas dos cursos/organizações correspondentes, nunca pelo usuário autenticado. Active e expires_at nulo ou maior que now bloqueia o carrinho inteiro. Fixar a fronteira: expires_at igual a now é expirado, também na reativação.

Calcular a chave IP do RateLimiter e verificar o limite **antes** da consulta de matrículas; só incrementar não impediria sondagem contínua. Bloqueio por matrícula incrementa a mesma chave (dez/60 s) antes da exceção; criação/reconciliação mantém a limitação existente, sem contar duas vezes a mesma tentativa.

### 5.3 Contrato Filament

Reutilizar `App\Filament\Checkout\Pages\Checkout` e `Filament\Actions\Action` gerarCobranca, pública a visitantes no painel checkout. A closure da Action chama confirmar protegido; dados continuam validados pelo schema existente. Nenhum scaffold, Resource, campo, layout ou alteração de estilo.

Capturar a exceção e enviar `Filament\Notifications\Notification`, tipo flash warning ao comprador. Titular autenticado cujo e-mail corresponde ao comprador: “Você já tem acesso ativo a: {títulos}. Remova-os do carrinho para continuar.” Visitante/terceiro: “Não foi possível concluir a compra com este e-mail. Se você já é aluno, entre na sua conta para ver seus cursos.” Sem títulos nos demais canais da resposta. Preservar carrinho em qualquer falha; limpar e redirecionar apenas com Order válido. Null mantém mensagem genérica atual.

Além dos cenários abaixo, testar usuário autenticado comprando para outro e-mail (mensagem neutra), limite atingido antes da sondagem, expires_at nulo e exatamente now. O callback de T02 só roda se houver criação permitida.

### Requisitos da revisão (transcrição para execução autônoma)

Bloquear a compra de curso com matrícula ativa e vigente · **REGRA NOVA (decisão do usuário em 2026-09-29)** · Severidade: Alta (evita cobrar por um acesso que o aluno já tem) · **agy**

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

## 6. Limites da entrega

Implementar e escrever apenas regressões mínimas do Worker; a suíte adversarial pertence ao Grok. Nenhuma task aprova produção. Linhas citadas na revisão são históricas: localizar os símbolos no código atual. Executar PHP/Artisan/testes somente via Sail com RTK. Não comitar `.env` ou registrar segredos.
