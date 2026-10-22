# 5. Idempotência do checkout por conjunto ordenado de cursos

Date: 2026-09-09
Status: accepted

## Context

O `CheckoutService` atual protege contra cobranças duplicadas (double-click, reenvio) com um fingerprint mono-curso:

```php
$fingerprint = $idempotencyKey ?: hash('sha256', "course:{$course->getKey()}|buyer:{$buyer->email}|doc:{$cleanDoc}");
```

Esse formato deixa de funcionar num pedido multi-item por duas razões:

1. Não existe mais **um** `course_id` a interpolar.
2. Se a chave fosse derivada apenas do comprador, dois carrinhos legitimamente diferentes do mesmo comprador colidiriam — a segunda compra devolveria o pedido da primeira.

O oposto também falha: se a chave dependesse da **ordem de inserção** dos itens no carrinho, o mesmo carrinho montado em ordem diferente produziria fingerprints distintos, e o double-click voltaria a gerar dois pedidos.

Esta é uma exigência de design do pedido multi-item, **não** remediação dos achados R1–R5 da auditoria `docs/specs/audit-carrinho-publico.md` — explicitamente fora de escopo nesta rodada por decisão do usuário, com nova auditoria prevista.

## Decision

Derivar o fingerprint de idempotência do **conjunto ordenado de IDs de curso** somado à identidade do comprador.

- Coletar os `course_id` do carrinho, **ordená-los numericamente** e serializá-los de forma canônica.
- Compor a chave com essa serialização mais e-mail e documento normalizado do comprador, mantendo o hash SHA-256 já em uso.
- Carrinhos com o mesmo conjunto de cursos produzem a mesma chave, independentemente da ordem de montagem.
- Carrinhos com conjuntos diferentes produzem chaves diferentes, permitindo compras sucessivas legítimas.
- Preservar o parâmetro `$idempotencyKey` explícito já existente na assinatura, que continua tendo precedência quando fornecido.

## Consequences

**Prós**
- Dupla submissão do mesmo carrinho gera um único pedido e uma única cobrança PIX (CA-08), independentemente da ordem dos itens.
- O comprador pode fazer uma segunda compra com outro conjunto de cursos sem receber o pedido anterior.
- Mantém a estrutura de idempotência já existente, sem introduzir mecanismo novo — coerente com a capacidade solo/best-effort da seção 7.

**Contras e custos**
- Atualização de 2026-09-29 (CODEX-02, decisão registrada na revisão UC-04): o acesso é válido por **um ano**. Matrícula ativa e vigente impede nova compra; matrícula cancelada ou expirada permite recompra com reativação por um ano. Retomada idempotente aplica-se somente a pedido pendente autorizado para a sessão, conforme F-01/F-02 e `sdd/3-plans/PLAN-uc-04-correcoes-asaas.md`; a composição do fingerprint permanece inalterada.
- A ordenação canônica precisa ser aplicada consistentemente em todos os caminhos que calculam a chave, sob pena de reintroduzir a duplicação. É ponto de atenção para os testes (RP-02).

**Fora de escopo**
- Os achados R1 (recuperação de pedido por dados cadastrais) e R2 (lock expirando antes da operação) da auditoria permanecem abertos e **não** são endereçados por esta ADR. Esta decisão define apenas como a chave é composta no novo formato multi-item; a política de autorização e a duração do lock seguem como estão, para tratamento na auditoria futura.
