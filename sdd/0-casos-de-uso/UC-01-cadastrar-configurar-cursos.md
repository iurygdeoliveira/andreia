# UC-01: Cadastrar e Configurar Cursos

**Ator Principal:** Organization Owner (ex: Celso)
**RFs Vinculados:** RF-03, RF-05

## 1. Descrição
Permite ao Organization Owner criar e editar a estrutura do curso no painel `/user/{organization:slug}`, incluindo informações visuais (capa, título) e os detalhes de preço, que irão compor a oferta para os alunos.

## 2. Pré-condições
- O usuário deve estar autenticado como Organization Owner na sua Organização.
- Deve existir pelo menos uma `Organization` à qual o usuário pertence.

## 3. Fluxo Principal (Happy Path)
1. O Organization Owner acessa o menu "Cursos" no painel Filament (`/user/{organization:slug}`).
2. O sistema lista os cursos apenas da sua Organização.
3. O Organization Owner clica em "Criar Curso".
4. O Organization Owner preenche os dados:
   - Título (Obrigatório)
   - Descrição (Opcional)
   - Upload de Imagem de Capa (Opcional)
   - Preço de Venda (Obrigatório)
   - Status: Rascunho ou Publicado.
5. O Organization Owner clica em salvar.
6. O sistema persiste as informações atrelando o `organization_id` automaticamente via Filament Tenancy.
7. O sistema retorna à listagem com o novo curso criado e exibe a URL/ID gerado para o checkout.

## 4. Fluxos de Exceção
- **E-01: Formulário Incompleto:**
  1. No passo 5, o sistema verifica se Título ou Preço estão vazios.
  2. Se sim, barra o envio e destaca os campos obrigatórios em vermelho.

## 5. Pós-condições
- O curso é criado no banco de dados e vinculado estritamente à Organização do usuário atual.
- O curso passa a estar disponível (se status=Publicado) para exibição na área logada do aluno ou na tela de checkout da landing page.
