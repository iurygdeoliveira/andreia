# PLAN — UC-04: correções Asaas

## 1. Escopo e base

Spec: `sdd/2-specs/uc-04-correcoes-asaas.md`. Fonte: `sdd/7-reviews/REVIEW-uc-04-analise-asaas-2026-09-29.md`. Preparado por Codex para implementação pelo agy; não é aprovação de produção. Pastas numeradas seguem a convenção existente, em lugar dos caminhos genéricos das skills. A distribuição antiga de agentes em `sdd/README.md` está superada pelo AGENTS.md: Codex 1–4, agy 5, Grok 6, Claude 7.

### 1.1 Discovery

Inspecionados CheckoutService, Checkout, AsaasWebhookController, GrantCourseAccessAction, RevokeCourseAccessAction, OrderStatus, OrderPaymentController e config/queue.php. O checkout ainda retoma sem escopo, usa lock de 15 s e busca QR dentro dele; `confirmar` é público; a concessão usa firstOrCreate sem reativação; course-access usa database. O working tree possui diversas mudanças preexistentes: preservar todas, reconciliando por símbolo, pois as linhas da revisão já diferem do checkout.

### 1.2 Limites

Sem novas migrations, models, Resources, dependências, workers ou infraestrutura. Não implementar CODEX-01/03, não alterar a composição do fingerprint nem o acesso por ULID às rotas show/status. A correção documental da vigência na ADR-0005 pertence ao Codex. O agy segue a regra de um ano expressa nesta spec e na revisão.

### 1.3 Capacidade operacional

`projeto.md` §7: equipe de 1–4 pessoas; best-effort/comercial; menos de 500 requisições/dia no admin; trajetória estável. UC-04 declara frequência baixa. Não extrapolar isso como medição do tráfego público. Escolha operacional registrada: sync, sem worker.

## 2. Fundação de status — AC-1 / T01

Adicionar grantsAccess no enum e no Order, e accessGranting no enum. Manter isPaid financeiro intacto. Migrar os chamadores listados no §2 da revisão: autorização da concessão, webhook/concessão e paid_at, recuperação de QR, JSON pago/redirect e as duas condições da view pagamento. A consulta de preservação de acesso fica em T07. Sem alterações de schema.

## 3. Retomada e reconciliação — AC-2/3 / T02–T03

### 3.1 Contrato de posse

Preservar os três parâmetros existentes de processPixCheckout e acrescentar ownedOrderIds (array, padrão vazio) como quarto. IDs vêm exclusivamente de `session('checkout.order_ids', [])`, nunca de campo Livewire ou request. Normalizar IDs para o mesmo tipo das chaves Eloquent antes da comparação estrita. Exigir Pending e posse nos dois caches e na consulta ao banco, preservando a correspondência de comprador/conjunto de cursos. Entrada vazia não autoriza retomada alguma.

### 3.2 Lacuna F-01 × F-02 resolvida neste plano

A revisão grava posse só no sucesso, mas F-02 exige recuperar um pedido cuja criação no Asaas falhou. Acrescentar um quinto argumento opcional onOrderCreated, callable recebendo Order, padrão null. Checkout passa uma closure do servidor que acrescenta o ID à lista de sessão (dez mais recentes, únicos). O serviço chama essa closure após confirmar a transação local de criação e **antes de qualquer HTTP**. A resposta de erro tratado preserva a sessão. A closure não é propriedade pública Livewire. Após sucesso, atualizar a mesma lista sem duplicar.

Isso preserva o retorno Order/null e evita acoplar CheckoutService diretamente à sessão. Chamadores sem callback continuam válidos, mas devem gerenciar explicitamente os IDs se quiserem retomar. A garantia cobre falhas HTTP tratadas; perda abrupta do processo/sessão não constitui durabilidade e não deve ser descrita como resolvida.

### 3.3 Fluxo dentro do lock

Cache é apenas indicação de candidato. Pedido órfão nunca sai por um retorno antecipado: precisa passar pela reconciliação. Dentro do lock, reler candidato e status antes de decidir criar/cobrar; não reutilizar um snapshot antigo da consulta. Pedidos com payment_id preservam o filtro de validade de QR existente. Órfãos precisam ser Pending, próprios, compatíveis e criados nos últimos 30 minutos.

Novo pedido: criar transação local → callback → cliente/cobrança. Órfão: GET por externalReference **antes de qualquer POST**, inclusive de cliente → uma cobrança compatível vincula seus IDs → zero permite cliente/cobrança com a referência original → qualquer ambiguidade/falha interrompe. Comparar centavos arredondados a inteiros. Não confiar em unicidade remota ou inventar header de idempotência.

AsaasService retorna os itens não deleted da listagem. Fazer uma consulta com limit máximo; se hasMore indicar lista incompleta, falhar para reconciliação manual, sem POST. Essa opção conservadora impede decidir unicidade pela primeira página e mantém o orçamento de chamadas do lock. Não adicionar paginação ilimitada ou retries sob o lock.

Timeout HTTP 10 s/connect 5 s; lock 75 s, espera 5 s. Tratar esses valores como configuração exigida, não prova absoluta de ausência de corridas. Depois de liberar o lock, recuperar QR e retornar pedido atualizado. Erro de QR não perde cobrança. Estender os testes a cache de órfão, GET falho, divergência e lista incompleta.

## 4. Webhook — AC-4/5 / T04

Manter autenticação por token e reconsulta como fonte da verdade. Antes de applyStatus, exigir referência exata, PIX, não deleted e centavos iguais ao total local. Divergência: log com IDs/campo, 200, zero mutações; não registrar token, CPF ou payload integral.

Para status financeiro pago, somar cada refund DONE em centavos. Soma positiva menor que value define PartiallyRefunded; soma maior/igual define Refunded. Não alterar total_amount. grantsAccess aciona concessão inclusive quando a primeira entrega já é parcial; Refunded revoga; manter log parcial e paid_at preenchido apenas se nulo. REFUND_REQUESTED/IN_PROGRESS continuam sem mapeamento. Atualizar contrato PHPDoc de getPayment com billingType, deleted e refunds.

## 5. Matrícula e interface — AC-6/7/8 / T05–T07

### 5.1 Reativação

Na transação e sob lock do pedido, capturar firstGrantForOrder antes de qualquer escrita, usando access_mail_queued_at nulo. Passar para enroll. Somente primeira concessão reativa registro já existente não Active ou expirado; prazo now + um ano. Vigente mantém prazo. Aviso de redundância deve ocorrer na primeira concessão, não em reentrega rotineira. Caminho de recuperação após violação unique deve aplicar a mesma decisão. Não alterar credenciais de conta existente. Preservar o marcador e e-mail único por pedido.

### 5.2 Bloqueio antes da retomada

Criar CourseAlreadyActiveException carregando a coleção de cursos. Serviço resolve User por buyer_email e matrículas dos cursos/organizações correspondentes, nunca pelo usuário autenticado. Active e expires_at nulo ou maior que now bloqueia o carrinho inteiro. Fixar a fronteira: expires_at igual a now é expirado, também na reativação.

Calcular a chave IP do RateLimiter e verificar o limite **antes** da consulta de matrículas; só incrementar não impediria sondagem contínua. Bloqueio por matrícula incrementa a mesma chave (dez/60 s) antes da exceção; criação/reconciliação mantém a limitação existente, sem contar duas vezes a mesma tentativa.

### 5.3 Contrato Filament

Reutilizar `App\Filament\Checkout\Pages\Checkout` e `Filament\Actions\Action` gerarCobranca, pública a visitantes no painel checkout. A closure da Action chama confirmar protegido; dados continuam validados pelo schema existente. Nenhum scaffold, Resource, campo, layout ou alteração de estilo.

Capturar a exceção e enviar `Filament\Notifications\Notification`, tipo flash warning ao comprador. Titular autenticado cujo e-mail corresponde ao comprador: “Você já tem acesso ativo a: {títulos}. Remova-os do carrinho para continuar.” Visitante/terceiro: “Não foi possível concluir a compra com este e-mail. Se você já é aluno, entre na sua conta para ver seus cursos.” Sem títulos nos demais canais da resposta. Preservar carrinho em qualquer falha; limpar e redirecionar apenas com Order válido. Null mantém mensagem genérica atual.

### 5.4 Revogação

Em RevokeCourseAccessAction, substituir apenas a lista Received/Confirmed pelo helper accessGranting; conservar os filtros existentes de usuário, curso e exclusão do próprio pedido. Não modelar direitos manuais.

## 6. Configuração sync — AC-9 / T08

Alterar somente QUEUE_CONNECTION em `.env`/`.env.example` e driver/comentário de course-access em config/queue.php. Preservar nome da conexão, beforeCommit e composer dev. Não versionar `.env`. Atualizar cache de configuração do ambiente de teste via Sail antes de verificar valores efetivos.

Sync envia dentro da transação; falha SMTP faz rollback da concessão, mas **não de applyStatus**, que atualmente ocorre antes, fora dessa transação. Não prometer atomicidade do status com a matrícula. Webhook falha e depende de reentrega; SMTP indisponível continua risco de pausa do Asaas. E-mail enviado seguido de falha de commit também não é exatamente uma vez. NewDevice pode afetar login; NEW_DEVICE_NOTIFICATION=false é mitigação disponível, não mudança automática desta tarefa.

Jobs antigos não são consumidos por trocar driver: registrar contagem inicial/final, sem apagar/reprocessar silenciosamente. Em banco limpo, jobs continua vazio; em banco com backlog, não surgem novos jobs e o passivo exige operação separada. Cron de backup permanece pendente.

## 7. Verificação e sequência — AC-10 / T09

T01 → T02 → T03 → T04 → T05 → T06 → T07 → T08 → T09. Cada task contém regressões mínimas do Worker; não substituem a crítica. Usar Pest, Http::fake e bloqueio de chamadas não simuladas; fixtures locais, PostgreSQL de teste e dados sintéticos. Testar serviços e a Action real do Livewire, sem tentar chamar confirmar protegido.

agy: testes focados por tarefa; ao final `rtk ./vendor/bin/sail test` e `rtk ./vendor/bin/sail pint --dirty`. Registrar falhas, skips e impedimentos, sem declarar verde por ausência de execução. Não instalar frontend nem rodar build para documentação/backend sem mudança de assets. Grok: concorrência PostgreSQL, dois cliques, jornada real catálogo→checkout→pagamento→login, mensagens visíveis, carrinho, modais/reatividade e assertNoJavaScriptErrors; Sandbox para estornos. Depois Claude, estágio 7.

## 8. Decisões e riscos

laravel-best-practices (architecture/http-client/config): manter Services/Actions existentes, timeouts explícitos e sem retry automático de POST. filament-blueprint (custom-pages/notifications/checklist): modificar Action e notificação existentes. pest-testing: regressões Feature e posterior navegador do Grok. grill-me: decisões de negócio recuperadas da revisão; sem nova política de produto. ai-memory-retrieval/handoff: continuidade com escopo explícito iury/eafs.

### 8.1 Contrapontos arquiteturais

Não introduzir Repository/DTO/evento: não há novo consumidor nem segunda reação a desacoplar. Callback estreito resolve a posse sem novo subsistema. Exceção de domínio é justificada por resposta de UI diferente de null. Page existente cobre o fluxo; nenhuma nova autorização/RBAC ou API. Cache serve ao lock já existente, não a otimização de consultas. Sync atende a decisão operacional, mas não resolve durabilidade. CODEX-03 precisa de ADR próprio e solução operacional compatível antes de produção.

## 9. Fontes e pendências

Revisão de 29/09 prevalece sobre E-09 e texto vitalício legado. RFs locais citados têm definição resumida no UC; arquivo de requisitos ausente não foi recriado por inferência. Referências verificadas: [listagem Asaas](https://docs.asaas.com/reference/listar-cobrancas), [refunds](https://docs.asaas.com/docs/estornos), [notificações Filament](https://filamentphp.com/docs/5.x/notifications/overview). O filtro externalReference retorna uma lista paginada; a recusa de lista incompleta é decisão conservadora deste plano.

## 10. Sustentabilidade

Q1 problema: achados identificados; Q2 alternativa simples: componentes existentes; Q3 manutenção: equipe 1–4; Q4 operação: sync e riscos explícitos; Q5 volume: sem nova infraestrutura; Q6 reversibilidade: configuração por conexão; Q7 falhas: null/exceção/HTTP diferenciados; Q8 testes: AC por task; Q9 segurança: sessão do servidor e notificação neutra; Q10 evolução: CODEX-01/03 separados, sem abstrações antecipadas. Nenhum item é evidência de aprovação do código.
