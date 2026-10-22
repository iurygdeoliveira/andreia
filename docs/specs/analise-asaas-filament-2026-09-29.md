# Asaas e Filament — análise das pendências de checkout

Data: 29/09/2026. Inspeção estática do working tree, incluindo alterações locais existentes. Consulta ao MCP Asaas (`search`, `fetch`, `get_endpoint`) e aplicação das skills `laravel-best-practices`, `filament-blueprint` e `filament-security-audit`. Este documento subsidia o planejamento; não constitui aprovação de auditoria SDD nem comprovação por teste de exploração.

## 1. Resumo e escopo

As pendências podem ser corrigidas. Asaas fornece consulta por referência externa, identificação de eventos e dados de estornos; autorização de pedidos e direitos de matrícula continuam sob responsabilidade do EAFS. Escopo: checkout público Filament, apresentação de pedido, serviços de pagamento, webhook e concessão/revogação de acesso; checagens direcionadas no OrderResource. Não foi realizada auditoria global de todos os painéis. Nenhuma requisição financeira foi executada.

Contagem do catálogo Filament no recorte inspecionado: A=0, B=0, C=0, D=0, E=0 achados confirmados. Os seis problemas abaixo são de domínio/integração e não devem ser classificados artificialmente como vulnerabilidades específicas do Filament.

## 2. Pendências e contratos de correção

### F-01 — Retomada e visualização sem comprovação de titularidade

Locais: `app/Services/CheckoutService.php:66`, `:80`, `:216`; `app/Http/Controllers/OrderPaymentController.php:14`, `:31`; `app/Filament/Checkout/Pages/Checkout.php:55`, `:131`.

O fingerprint e a busca de pedidos usam cursos, e-mail e CPF, sem vínculo à sessão. As rotas de pagamento e status resolvem o pedido apenas por `order_number`. O `canAccess()` da página apenas identifica o painel público. E-mail/CPF conhecidos permitem retomar pedido de outra sessão pelo próprio checkout; o identificador da URL não resolve essa falha.

Correção proposta: preservar o checkout de visitante e vincular a intenção de compra a um segredo de sessão gerado no servidor. Exigir prova de posse na retomada, exibição, polling e recuperação do QR Code. Para acesso autenticado, usar titularidade verificada, nunca apenas o e-mail digitado. Recuperação em outro dispositivo exige fluxo próprio de comprovação. Aplicar a regra no serviço e nos endpoints, inclusive quando `confirmar()` for invocado diretamente via Livewire.

Verificar: duas sessões com os mesmos dados não compartilham pedidos; o comprador legítimo consegue retomar; chamada direta Livewire não contorna o contrato.

### F-02 — Concorrência e resposta inconclusiva podem criar cobranças repetidas

Locais: `app/Services/CheckoutService.php:126`, `:146`, `:160`; `app/Services/AsaasService.php:26`.

O lock dura 15 segundos, mas cada chamada HTTP admite 30 segundos e há chamadas sequenciais. Após erro, o pedido pode permanecer sem `payment_id`; nova submissão cria outra intenção. A primeira cobrança pode ter sido criada apesar do timeout.

Correção proposta: persistir uma intenção única por checkout autorizado antes da chamada; reutilizar pedido e `externalReference`; serializar criação/reconciliação; após resposta inconclusiva, consultar `GET /v3/payments?externalReference=...`. Conferir cliente, valor bruto, moeda aplicável e tipo PIX antes de vincular. Resultado ambíguo permanece em reconciliação; múltiplas cobranças encontradas exigem tratamento explícito. Não repetir o POST imediatamente após busca vazia. Dimensionar a exclusão mútua para todo o trabalho e manter unicidade/estado persistente, sem depender apenas do TTL do cache.

`externalReference` é um campo de correlação e busca: a documentação consultada não garante unicidade nem autoriza assumir suporte a um header genérico `Idempotency-Key` para criação de cobrança.

Verificar: duplo envio e timeout depois de criação remota preservam uma intenção e recuperam a cobrança existente.

### F-03 — Estorno parcial não é reconciliado pelos dados financeiros

Locais: `app/Http/Controllers/AsaasWebhookController.php:65`; `app/Enums/OrderStatus.php:76`.

O código depende de `payment.status` para produzir `PartiallyRefunded`. O contrato consultado distingue o evento `PAYMENT_PARTIALLY_REFUNDED` do status da cobrança; o enum de status da API consultada não inclui `PARTIALLY_REFUNDED`. O controller também ignora `refunds`.

Correção proposta: reconsultar a cobrança, persistir o estado dos estornos e somar em centavos somente entradas `DONE`, mantendo o valor original. Distinguir devolução parcial, integral e processamento pendente. Não confiar no nome do evento sozinho nem revogar por estorno `PENDING`/`CANCELLED`. Preservar a política existente de manter acesso em estorno parcial.

Verificar: vários estornos parciais concluídos, pendentes/cancelados e soma final integral; validar os payloads reais em Sandbox.

### F-04 — Recompra não reativa matrícula

Local: `app/Actions/GrantCourseAccessAction.php:125`, método `enroll()`.

`firstOrCreate()` devolve matrícula existente cancelada/expirada sem reativá-la.

Correção proposta: ao confirmar uma nova compra legítima, registrar seu direito de acesso e recalcular matrícula ativa/prazo conforme a política do curso. Reentrega do mesmo pagamento não deve renovar o prazo repetidamente.

Verificar: estorno integral seguido de recompra restaura acesso; webhook duplicado não estende vigência.

### F-05 — Revogação não representa todas as origens de acesso

Local: `app/Actions/RevokeCourseAccessAction.php:69` e busca de matrícula subsequente.

A preservação consulta somente outros pedidos `Received`/`Confirmed`; não representa concessão independente e desconsidera pedidos parcialmente estornados, embora a política declarada mantenha seu acesso.

Correção proposta: representar a origem e vigência de cada direito de acesso, vinculando compras a itens de pedido e concessões independentes à sua origem. Revogar apenas o direito do pedido integralmente estornado; cancelar matrícula apenas quando nenhum direito válido restar. Essa modelagem pertence ao EAFS, não ao Asaas.

Verificar: estornar uma compra não remove acesso respaldado por concessão independente, outro pedido pago ou pedido parcialmente estornado válido.

### F-06 — Webhook sem recepção durável e deduplicação por evento

Locais: `app/Http/Controllers/AsaasWebhookController.php:53`, `:71`, `:103`.

O controller autentica o token e reconsulta a API, mas não persiste o ID do evento. Pagamento ainda sem vínculo local recebe 200 e é descartado; atualização financeira e efeitos de acesso não estão na mesma unidade transacional. Isso dificulta recuperar a corrida entre criação da cobrança e gravação de seu ID local.

Correção proposta: autenticar, persistir evento com ID único e estado pendente antes do 200; processar em worker com reconciliação e serialização por pagamento. Validar vínculo por `payment_id`, referência, cliente, tipo e valor; concluir estado e efeitos de forma transacional/idempotente. Eventos desconhecidos ficam disponíveis para reconciliação e classificação, sem bloquear indefinidamente a recepção. Monitorar eventos pendentes e falhas de processamento.

Verificar: duplicatas, eventos concorrentes, webhook antes da gravação de `payment_id`, falha após persistência e retomada pelo worker. Nenhum acesso ou e-mail duplicado.

## 3. Checagens e limites

| Checagem | Resultado no recorte |
|---|---|
| Autorização Filament/Livewire e exposição | Inspeção direcionada; falha de titularidade é F-01, não um bypass comprovado do framework |
| A1/A2/A4 | N/A no recorte: sem bulk actions, importadores ou colunas inline editáveis |
| A3/A6 | Pass no recorte: restrições constantes no OrderResource; mount do checkout sem efeito sensível antes da autorização |
| D1 | Pass no OrderResource: consulta herda a base e restringe organização quando há tenant |
| D2 | N/A no OrderResource: somente listagem; sem página/modal Edit/View que dispare este critério |
| A5/D3/D4/D5 | Não concluídos em todos os componentes/painéis; não inferir Pass global |
| Uploads e renderização perigosa nas páginas checkout/pagamento e OrderResource | Buscas sem ocorrência dos construtos pesquisados; sem achado confirmado |
| Catálogo completo em todos os painéis | Não executado; não há declaração de segurança global |
| E1 — dependências | `rtk ./vendor/bin/sail composer audit --format=plain`: nenhum aviso de vulnerabilidade; saída 1 devido a `symplify/rule-doc-generator-contracts` abandonado |
| Cobertura de pagamento | Arquivos atuais de testes cobrem site/carrinho; busca não encontrou testes de CheckoutService, webhook e ações de matrícula |
| Worker de e-mail | Supervisord versionado não declara `queue:work`; existência de worker externo não foi verificada |
| Testes funcionais/Sandbox/browser | Não executados nesta análise; nenhuma alteração em código de aplicação |

## 4. Ordem proposta e verificações

1. F-01: isolamento de pedidos e chamadas diretas Livewire.
2. F-02/F-06: intenção persistente, reconciliação e recepção durável, incluindo operação/monitoramento dos workers.
3. F-03/F-04/F-05: estado financeiro e direitos de acesso por origem.
4. Validar cenários acima com HTTP simulado, concorrência em PostgreSQL, Sandbox Asaas e jornada real no navegador (duplo clique, mensagens visíveis, recarga e ausência de erros JavaScript). Testes de navegador não substituem testes de corrida.

## 5. Fontes consultadas

- [Asaas: retries e idempotência](https://docs.asaas.com/docs/retries-e-idempotência).
- MCP `get_endpoint`, spec `Asaas`, `GET /v3/payments`: filtro `externalReference` e schemas de pagamentos/estornos.
- [Asaas: idempotência em webhooks](https://docs.asaas.com/docs/como-implementar-idempotencia-em-webhooks).
- [Asaas: eventos de cobranças](https://docs.asaas.com/docs/webhook-para-cobrancas).
- [Asaas: interpretação de estornos](https://docs.asaas.com/docs/estornos).
- [Filament: segurança](https://filamentphp.com/docs/5.x/advanced/security): URL de referência das skills; abertura direta indisponível nesta sessão. As conclusões locais se apoiam no código inspecionado, sem alegar consulta bem-sucedida dessa página.
