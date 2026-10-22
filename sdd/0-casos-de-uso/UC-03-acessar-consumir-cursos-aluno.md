# UC-03: Acessar e Consumir Cursos (Aluno)

**Ator Principal:** Aluno (Student)
**RFs Vinculados:** RF-06

## 1. Descrição
Permite ao Aluno acessar o `StudentPanelProvider` (`/student/{organization:slug}`), visualizar a lista de cursos em que possui Matrícula (`Enrollment`) ativa, navegar pela estrutura de Módulos e Aulas, e realizar a visualização/download seguro dos arquivos em PDF.

## 2. Pré-condições
- O Aluno deve possuir conta cadastrada e autenticada.
- O Aluno deve possuir pelo menos uma Matrícula (`Enrollment`) ativa no curso dentro da Organização atual.

## 3. Fluxo Principal (Happy Path)
1. O Aluno acessa a URL do portal da Organização (`/student/eafs`).
2. O sistema exibe a página principal ("Meus Cursos") com a lista de cursos matriculados.
3. O Aluno clica em um curso desejado.
4. O sistema exibe a página de detalhes do Curso, organizada por Módulos e Aulas.
5. O Aluno clica sobre uma Aula para abrir/visualizar.
6. O sistema valida que a aula pertence a um curso matriculado e gera a rota de acesso seguro ao PDF.
7. O Aluno visualiza o documento PDF.

## 4. Fluxos de Exceção
- **E-01: Aluno sem Matrícula no Curso:**
  1. O Aluno tenta acessar diretamente a URL de um curso sem ter `Enrollment` ativo.
  2. O sistema barra o acesso no passo 4 e exibe uma mensagem "Você não possui acesso a este curso" ou redireciona para a vitrine/checkout.

- **E-02: Tentativa de Acesso a Conteúdo de outra Organização:**
  1. O Aluno tenta alterar o `slug` da organização na URL (`/student/outra-org`).
  2. O sistema verifica o isolamento por Organização (RN-01) e barra a requisição com erro 403.

## 5. Pós-condições
- O Aluno consome com sucesso os arquivos didáticos (PDFs) com total garantia de isolamento entre Organizações.
