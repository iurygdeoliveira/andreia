# T08 — Configurar as duas conexões sync

> Responsável: agy, estágio 5. Status: CONCLUÍDA. Critérios: AC-9.
> Spec: `sdd/2-specs/uc-04-correcoes-asaas.md`
> Plan: `sdd/3-plans/PLAN-uc-04-correcoes-asaas.md` §6
> Dependências: T07 concluída; executar sequencialmente pelo índice.

## 1. Descrição e objetivo

Configurar as duas conexões sync, conforme a revisão de 29/09 e o contrato detalhado abaixo. Não criar migrations ou implementar CODEX-01/03.

## 2. Justificativa técnica

Atende AC-9 e PLAN §6. Reutilizar os componentes existentes reduz o risco de divergência com o checkout atual. As complementações Codex abaixo resolvem lacunas de integração da revisão e prevalecem nesses pontos.

## 3. Critérios de pronto

- [x] Todos os comportamentos e cenários abaixo foram verificados.
- [x] Regressão mínima reproduziu o defeito antes e passou após a correção (quando aplicável).
- [x] Executar testes focados via `rtk ./vendor/bin/sail test`; registrar resultado real e impedimentos.
- [x] Preservar alterações locais alheias, dados e testes de outros agentes.
- [x] Sem migrations, alterações de ADR pelo Worker ou ampliação de escopo.

## 4. Arquivos afetados

- `.env`
- `.env.example`
- `config/queue.php`
- Regressões Worker: `tests/Feature/CheckoutAsaas/T08Test.php` (criar ou ampliar equivalente existente sem duplicar).

## 5. Contrato de implementação e verificação

### Complementações do plano (ler primeiro)

— AC-9 / T08

Alterar somente QUEUE_CONNECTION em `.env`/`.env.example` e driver/comentário de course-access em config/queue.php. Preservar nome da conexão, beforeCommit e composer dev. Não versionar `.env`. Atualizar cache de configuração do ambiente de teste via Sail antes de verificar valores efetivos.

Sync envia dentro da transação; falha SMTP faz rollback da concessão, mas **não de applyStatus**, que atualmente ocorre antes, fora dessa transação. Não prometer atomicidade do status com a matrícula. Webhook falha e depende de reentrega; SMTP indisponível continua risco de pausa do Asaas. E-mail enviado seguido de falha de commit também não é exatamente uma vez. NewDevice pode afetar login; NEW_DEVICE_NOTIFICATION=false é mitigação disponível, não mudança automática desta tarefa.

Jobs antigos não são consumidos por trocar driver: registrar contagem inicial/final, sem apagar/reprocessar silenciosamente. Em banco limpo, jobs continua vazio; em banco com backlog, não surgem novos jobs e o passivo exige operação separada. Cron de backup permanece pendente.

Após alteração, executar rtk ./vendor/bin/sail artisan config:clear no ambiente de teste; verificar config efetiva default=sync e connections.course-access.driver=sync. Não exibir configuração integral ou segredos. Smoke de e-mail usa destinatário controlado e Sandbox; não disparar mensagens para alunos reais. Não usar Mail::fake como prova de entrega SMTP. Registrar evidência ou impedimento; não declarar entrega sem verificar.

### Requisitos da revisão (transcrição para execução autônoma)

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

## 6. Limites da entrega

Implementar e escrever apenas regressões mínimas do Worker; a suíte adversarial pertence ao Grok. Nenhuma task aprova produção. Linhas citadas na revisão são históricas: localizar os símbolos no código atual. Executar PHP/Artisan/testes somente via Sail com RTK. Não comitar `.env` ou registrar segredos.
