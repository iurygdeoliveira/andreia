# Guia de Comandos Diários — Vite+ (`vp`)

O **Vite+** é um *toolchain* unificado para desenvolvimento web que consolida em uma única ferramenta (`vp`):
- **Vite & Rolldown**: Servidor de desenvolvimento ultra-rápido e empacotador de produção.
- **Vitest**: Executor de testes integrado.
- **Oxlint & Oxfmt**: Linter e formatador de código de alta performance escritos em Rust.
- **Vite Task**: Executor de scripts e tarefas com cache inteligente.
- **Gerenciador de Runtime**: Gerenciamento integrado do Node.js e gerenciador de pacotes (`npm`).

---

## ⚡ Fluxo de Trabalho Recomendado (Cheat Sheet)

```bash
# 1. Ao iniciar o dia ou após puxar novidades do Git
vp install

# 2. Durante o desenvolvimento (iniciar servidor local)
vp dev

# 3. Antes de commitar (validar formatação, lint e tipos)
vp check

# 4. Se houver avisos de formatação para corrigir automaticamente
vp check --fix

# 5. Executar a suíte de testes
vp test

# 6. Gerar build de produção para validação/deploy
vp build
```

---

## 🛠️ Comandos do Dia a Dia

### 1. Desenvolvimento e Build

#### `vp dev`
* **Objetivo:** Inicia o servidor local de desenvolvimento com Hot Module Replacement (HMR).
* **Quando usar:** Sempre que for programar e visualizar as alterações em tempo real no navegador.
* **Exemplo:**
  ```bash
  vp dev
  ```

#### `vp build`
* **Objetivo:** Compila e minifica todos os assets de CSS, JavaScript e temas (Tailwind CSS, Filament, etc.) para a pasta `public/build` usando o **Rolldown**.
* **Quando usar:** Antes de fazer deploy ou para testar como o projeto se comporta em modo de produção.
* **Exemplo:**
  ```bash
  vp build
  ```

#### `vp preview`
* **Objetivo:** Inicia um servidor local servindo os arquivos compilados da pasta de build para testar o resultado final de produção.
* **Quando usar:** Para inspecionar visualmente a build antes de enviar para o servidor.
* **Exemplo:**
  ```bash
  vp preview
  ```

---

### 2. Qualidade de Código (Lint, Formatação e Tipagem)

#### `vp check`
* **Objetivo:** Executa a verificação completa 3-em-1 no código:
  1. Formatação de código (**Oxfmt**).
  2. Análise estática e regras de qualidade (**Oxlint**).
  3. Checagem de tipos TypeScript.
* **Quando usar:** Antes de criar commits, abrir Pull Requests ou para validar a integridade do código.
* **Exemplo:**
  ```bash
  vp check
  ```

#### `vp check --fix`
* **Objetivo:** Executa a verificação e corrige automaticamente todos os problemas de formatação e regras de lint que possuem autofix.
* **Quando usar:** Sempre que `vp check` acusar problemas de espaçamento, quebras de linha ou formatação.
* **Exemplo:**
  ```bash
  vp check --fix
  ```

#### `vp fmt` (ou `vp format`)
* **Objetivo:** Executa apenas o formatador de código (**Oxfmt**) nos arquivos do projeto.
* **Quando usar:** Quando quiser apenas formatar os arquivos sem rodar a análise de lint/tipos.
* **Exemplo:**
  ```bash
  vp fmt
  ```

#### `vp lint`
* **Objetivo:** Executa apenas a análise do linter (**Oxlint**).
* **Quando usar:** Para inspecionar erros e boas práticas de código de forma isolada.
* **Exemplo:**
  ```bash
  vp lint
  ```

---

### 3. Testes

#### `vp test`
* **Objetivo:** Executa a suíte de testes JavaScript/TypeScript utilizando o **Vitest** embutido no Vite+.
* **Quando usar:** Ao escrever novos componentes, utilitários frontend ou refatorar código.
* **Exemplo:**
  ```bash
  vp test
  vp test --run      # Executa uma única vez (sem modo watch)
  ```

---

### 4. Gerenciamento de Pacotes e Dependências

#### `vp install` (ou `vp i`)
* **Objetivo:** Instala todas as dependências declaradas no `package.json` utilizando o gerenciador de pacotes configurado.
* **Quando usar:** Ao clonar o projeto ou após fazer `git pull` com novas dependências.
* **Exemplo:**
  ```bash
  vp install
  ```

#### `vp add <pacote>`
* **Objetivo:** Adiciona uma nova dependência de produção ao projeto.
* **Opções úteis:** Use `-D` para instalar como dependência de desenvolvimento (`devDependencies`).
* **Exemplos:**
  ```bash
  vp add lodash-es
  vp add -D @types/lodash-es
  ```

#### `vp remove <pacote>`
* **Objetivo:** Remove uma dependência do projeto e atualiza o `package.json` e o `package-lock.json`.
* **Exemplo:**
  ```bash
  vp remove axios
  ```

#### `vp update`
* **Objetivo:** Atualiza as dependências do projeto para versões mais recentes compatíveis.
* **Exemplo:**
  ```bash
  vp update
  ```

#### `vp why <pacote>`
* **Objetivo:** Mostra a árvore de dependências explicando por que determinado pacote está instalado no projeto.
* **Exemplo:**
  ```bash
  vp why tailwindcss
  ```

---

### 5. Execução de Tarefas e Scripts (`vp run` / `vpr`)

> ⚠️ **Conceito Importante:**
> - `vp <comando>` executa comandos nativos do Vite+ (como `vp dev`, `vp build`, `vp test`).
> - `vp run <script>` (ou o atalho **`vpr <script>`**) executa os scripts definidos na seção `"scripts"` do seu `package.json` com sistema de cache inteligente.

* **Exemplos:**
  ```bash
  vp run dev      # ou: vpr dev
  vp run build    # ou: vpr build
  ```

---

### 6. Manutenção e Diagnóstico

#### `vp toolchain`
* **Objetivo:** Exibe todas as versões dos componentes internos empacotados pelo Vite+ (Vite, Rolldown, Vitest, Oxlint, Oxfmt, tsdown).
* **Exemplo:**
  ```bash
  vp toolchain
  ```

#### `vp env`
* **Objetivo:** Exibe e gerencia a versão ativa do Node.js e gerenciador de pacotes controlados pelo Vite+.
* **Exemplo:**
  ```bash
  vp env
  vp env doctor   # Diagnóstico completo do ambiente caso haja algum problema
  ```

#### `vp hooks`
* **Objetivo:** Gerencia os hooks de Git configurados pelo Vite+ (que rodam verificações automáticas nos arquivos modificados antes de commitar).
* **Exemplos:**
  ```bash
  vp hooks status
  vp hooks enable
  vp hooks disable
  ```

#### `vp help`
* **Objetivo:** Mostra a lista completa de comandos disponíveis e opções de ajuda do CLI.
* **Exemplo:**
  ```bash
  vp help
  vp help <comando>   # Ex: vp help migrate
  ```
