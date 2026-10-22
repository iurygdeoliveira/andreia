# UC-02: Estruturar Grade Curricular

**Ator Principal:** Organization Owner
**RFs Vinculados:** RF-04

## 1. Descrição
Permite ao Organization Owner organizar o conteúdo de um curso existente na sua Organização, criando Módulos e cadastrando Aulas em formato PDF.

## 2. Pré-condições
- O usuário deve estar autenticado como Organization Owner.
- Pelo menos um curso (UC-01) deve estar cadastrado na Organização.

## 3. Fluxo Principal (Happy Path)
1. O Organization Owner acessa a edição de um Curso existente no menu "Cursos".
2. Na aba de "Grade Curricular" (ou "Módulos"), o Owner clica em "Adicionar Módulo".
3. O Owner define o nome do Módulo (ex: "Semana 1 - Fundamentos") e salva.
4. Dentro do módulo recém-criado, o Owner clica em "Adicionar Aula".
5. O Owner preenche os dados da Aula:
   - Título da Aula
   - Upload do Arquivo (PDF).
6. O sistema realiza o upload de forma segura (disco privado/S3).
7. O sistema salva a aula e retorna para a visão do Módulo.

## 4. Fluxos de Exceção
- **E-01: Extensão Inválida no Upload:**
  1. No passo 5, o Owner tenta enviar um arquivo `.mp4` ou `.docx`.
  2. O sistema bloqueia a ação, baseando-se na regra RN-02 (Apenas PDFs são aceitos).
  3. O sistema exibe um erro de validação (ex: "O formato do arquivo é inválido. Envie apenas PDF.").

- **E-02: Curso não encontrado ou de outra Organização:**
  1. No passo 1, caso o ID do curso não pertença à Organização atual, o sistema bloqueia o acesso e exibe erro 403 ou 404 (Garantia do RF-02).

## 5. Pós-condições
- Módulos e aulas (PDF) atrelados ao curso e prontos para consumo por parte dos alunos matriculados no momento em que acessarem a área logada.
