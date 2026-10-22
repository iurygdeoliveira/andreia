#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Script de instalação para preparar o projeto após clonagem/uso via Laravel Installer.
 *
 * Passos:
 * - Verifica e instala automaticamente: PHP 8.5, extensões PHP, Composer
 * - Remove Apache2 se instalado (conflito com Nginx do Sail)
 * - Orienta instalação manual de: Node.js e Docker
 * - Configura permissões Docker automaticamente
 * - Cria .env a partir de .env.example (se necessário)
 * - Executa composer install para gerar vendor/bin/sail
 * - Inicia containers Sail
 * - Executa migrations e seeders
 * - Instala dependências NPM e build dos assets
 */
$basePath = __DIR__;

/**
 * Executa um comando e exibe o resultado.
 */
function run(string $command): void
{
    echo "\n> {$command}\n";

    $exitCode = 0;
    passthru($command, $exitCode);

    if ($exitCode !== 0) {
        echo "\nComando falhou com código {$exitCode}. Abortando.\n";
        exit($exitCode);
    }
}

/**
 * Verifica se um comando está disponível no PATH atual.
 */
function commandExists(string $command): bool
{
    $which = shell_exec(sprintf('command -v %s 2>/dev/null', escapeshellarg($command)));

    return is_string($which) && trim($which) !== '';
}

/**
 * Verifica se PHP 8.5+ está instalado.
 */
function checkPhpVersion(): bool
{
    if (! commandExists('php')) {
        return false;
    }

    $version = shell_exec('php -v');
    if (! is_string($version)) {
        return false;
    }

    // Extrair versão do PHP
    if (preg_match('/PHP (\d+\.\d+)/', $version, $matches)) {
        $versionNumber = (float) $matches[1];

        return $versionNumber >= 8.5;
    }

    return false;
}

/**
 * Instala PHP 8.5 automaticamente.
 */
function installPhp85(): void
{
    echo "📥 Instalando PHP 8.5 automaticamente...\n";

    // Instalar software-properties-common
    run('sudo apt install software-properties-common -y');

    // Adicionar PPA do Ondrej
    run('LC_ALL=C.UTF-8 sudo add-apt-repository ppa:ondrej/php -y');

    // Atualizar pacotes
    run('sudo apt update');

    // Instalar PHP 8.5
    run('sudo apt install php8.5 php8.5-cli php8.5-fpm -y');

    echo "✅ PHP 8.5 instalado com sucesso!\n";
}

/**
 * Verifica se todas as extensões PHP necessárias estão instaladas.
 */
function checkPhpExtensions(): array
{
    $requiredExtensions = [
        // Essenciais do Laravel
        'mbstring',
        'xml',
        'pdo',
        'tokenizer',
        'openssl',
        'fileinfo',
        'ctype',
        'json',
        'bcmath',
        'curl',
        // Imagens/Arquivos
        'gd',
        'zip',
        // XML/DOM
        'dom',
        'xmlwriter',
        'xmlreader',
        'simplexml',
        // Bancos de dados
        'pgsql',
        'sqlite3',
        // Recomendadas
        'tidy',
        'intl',
    ];

    $installedExtensions = [];
    $missingExtensions = [];

    foreach ($requiredExtensions as $extension) {
        $output = shell_exec("php -m | grep -i '^{$extension}$'");
        if (is_string($output) && trim($output) === $extension) {
            $installedExtensions[] = $extension;
        } else {
            $missingExtensions[] = $extension;
        }
    }

    return [
        'installed' => $installedExtensions,
        'missing' => $missingExtensions,
    ];
}

/**
 * Instala extensões PHP faltantes automaticamente.
 */
function installPhpExtensions(array $missingExtensions): void
{
    if (empty($missingExtensions)) {
        echo "✅ Todas as extensões PHP necessárias já estão instaladas.\n";

        return;
    }

    echo '📥 Instalando extensões PHP faltantes: '.implode(', ', $missingExtensions)."\n";

    $extensionPackages = [];
    foreach ($missingExtensions as $extension) {
        $extensionPackages[] = "php8.5-{$extension}";
    }

    $command = 'sudo apt install '.implode(' ', $extensionPackages).' -y';
    run($command);

    echo "✅ Extensões PHP instaladas com sucesso!\n";
}

/**
 * Verifica se Composer está instalado.
 */
function checkComposer(): bool
{
    return commandExists('composer');
}

/**
 * Instala Composer automaticamente.
 */
function installComposer(): void
{
    echo "📥 Instalando Composer automaticamente...\n";

    // Baixar o instalador
    run('php -r "copy(\'https://getcomposer.org/installer\', \'composer-setup.php\');"');

    // Executar instalador (sem verificação de hash por simplicidade)
    run('sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer');

    // Remover instalador
    run('php -r "unlink(\'composer-setup.php\');"');

    echo "✅ Composer instalado com sucesso!\n";
}

/**
 * Verifica se Node.js 18+ e NPM estão instalados.
 */
function checkNodeNpm(): bool
{
    if (! commandExists('node') || ! commandExists('npm')) {
        return false;
    }

    $nodeVersion = shell_exec('node --version');
    if (! is_string($nodeVersion)) {
        return false;
    }

    // Extrair versão do Node.js
    if (preg_match('/v(\d+)/', $nodeVersion, $matches)) {
        $versionNumber = (int) $matches[1];

        return $versionNumber >= 18;
    }

    return false;
}

/**
 * Verifica se Docker está instalado.
 */
function checkDocker(): bool
{
    return commandExists('docker');
}

/**
 * Verifica se Docker está rodando.
 */
function checkDockerRunning(): bool
{
    if (! checkDocker()) {
        return false;
    }

    $output = shell_exec('docker ps 2>/dev/null');

    return is_string($output) && ! empty(trim($output));
}

/**
 * Verifica se Docker Compose v2+ está disponível.
 */
function checkDockerCompose(): bool
{
    if (! commandExists('docker')) {
        return false;
    }

    $output = shell_exec('docker compose version 2>/dev/null');
    if (! is_string($output)) {
        return false;
    }

    // Verificar se é versão 2 ou superior
    if (preg_match('/version v?(\d+)/i', $output, $matches)) {
        return (int) $matches[1] >= 2;
    }

    return false;
}

/**
 * Verifica se Apache2 está instalado.
 */
function checkApache2(): bool
{
    $output = shell_exec('dpkg -l | grep apache2');

    return is_string($output) && ! empty(trim($output));
}

/**
 * Verifica se Apache2 está ativo e rodando.
 */
function checkApache2Running(): bool
{
    if (! checkApache2()) {
        return false;
    }

    $output = shell_exec('systemctl is-active apache2 2>/dev/null');

    // Se o comando falhar ou retornar erro, considerar como não rodando
    if (! is_string($output)) {
        return false;
    }

    $status = trim($output);

    // Considerar como rodando apenas se o status for 'active'
    return $status === 'active';
}

/**
 * Remove Apache2 automaticamente.
 */
function removeApache2(): void
{
    echo "⚠️  Removendo Apache2 (conflita com Nginx usado pelo Sail)...\n";

    // Se estiver rodando, parar primeiro
    if (checkApache2Running()) {
        echo "🛑 Parando serviço Apache2...\n";
        shell_exec('sudo systemctl stop apache2 2>/dev/null');
    }

    // Desabilitar o serviço (ignorar erro se não existir)
    echo "🚫 Desabilitando serviço Apache2...\n";
    shell_exec('sudo systemctl disable apache2 2>/dev/null');

    // Remover completamente
    echo "🗑️  Removendo pacotes Apache2...\n";
    shell_exec('sudo apt remove --purge apache2 apache2-utils apache2-bin -y 2>/dev/null');

    // Limpar dependências
    echo "🧹 Limpando dependências...\n";
    shell_exec('sudo apt autoremove -y 2>/dev/null');

    echo "✅ Apache2 removido com sucesso!\n";
}

/**
 * Configura permissões Docker automaticamente.
 */
function configureDockerPermissions(): void
{
    echo "⚙️  Configurando permissões Docker...\n";

    // Verificar se usuário está no grupo docker
    $groups = shell_exec('groups');
    if (! is_string($groups) || ! str_contains($groups, 'docker')) {
        // Criar grupo docker (ignorar erro se já existir)
        shell_exec('sudo groupadd docker 2>/dev/null');

        // Adicionar usuário ao grupo docker
        run('sudo usermod -aG docker $USER');

        echo "⚙️  Usuário adicionado ao grupo docker.\n";
        echo "IMPORTANTE: Você precisa fazer logout e login novamente (ou reiniciar)\n";
        echo "para que as mudanças tenham efeito.\n";
        echo "Alternativamente, execute: newgrp docker\n";

        // Tentar ativar com newgrp
        shell_exec('newgrp docker');
    } else {
        echo "✅ Usuário já está no grupo docker.\n";
    }

    // Verificar se Docker está rodando
    if (! checkDockerRunning()) {
        echo "⚙️  Tentando iniciar o daemon Docker...\n";

        // Tentar iniciar com systemctl
        shell_exec('sudo systemctl start docker 2>/dev/null');

        // Se não funcionar, tentar com service
        if (! checkDockerRunning()) {
            shell_exec('sudo service docker start 2>/dev/null');
        }

        if (checkDockerRunning()) {
            echo "✅ Docker daemon iniciado com sucesso!\n";
        } else {
            echo "⚠️  Não foi possível iniciar o Docker daemon automaticamente.\n";
            echo "Por favor, inicie manualmente: sudo systemctl start docker\n";
        }
    } else {
        echo "✅ Docker daemon já está rodando.\n";
    }
}

/**
 * Exibe mensagem de erro e aborta para dependências que devem ser instaladas manualmente.
 */
function abortWithInstructions(string $dependency, string $url, string $message): void
{
    echo "\n❌ {$dependency} não encontrado!\n\n";
    echo "{$message}\n";
    echo "🔗 {$url}\n\n";
    echo "Após a instalação, execute este script novamente.\n";
    exit(1);
}

/**
 * Cria arquivo .env a partir de .env.example se não existir.
 */
function createEnvFile(string $basePath): void
{
    $envPath = $basePath.'/.env';
    $envExamplePath = $basePath.'/.env.example';

    if (! file_exists($envPath) && file_exists($envExamplePath)) {
        if (! copy($envExamplePath, $envPath)) {
            echo "❌ Arquivo .env não pôde ser criado a partir de .env.example\n";
            exit(1);
        }

        echo "✅ Arquivo .env criado a partir de .env.example\n";
    }
}

/**
 * Executa a instalação via Sail.
 */
function runSailInstallation(string $basePath): void
{
    echo "\n📦 Iniciando instalação via Sail...\n";

    // 1. Criar .env
    createEnvFile($basePath);

    // 2. Instalar dependências Composer primeiro (necessário para gerar vendor/bin/sail)
    echo "📦 Instalando dependências Composer...\n";
    run('composer install --no-interaction --prefer-dist');

    // 3. Iniciar containers Sail
    echo "🚀 Iniciando containers Sail...\n";
    run('./vendor/bin/sail up -d');

    // 4. Aguardar containers e serviços estarem prontos
    echo "⏳ Aguardando serviços estarem prontos (PostgreSQL, MinIO)...\n";

    $maxAttempts = 30; // 30 tentativas x 2 segundos = 60 segundos máximo
    $attempt = 0;
    $allHealthy = false;

    while ($attempt < $maxAttempts && ! $allHealthy) {
        $attempt++;

        // Verificar health check dos containers críticos
        $pgsqlHealth = shell_exec('docker inspect --format="{{.State.Health.Status}}" $(docker compose ps -q pgsql) 2>/dev/null');
        $minioHealth = shell_exec('docker inspect --format="{{.State.Health.Status}}" $(docker compose ps -q minio) 2>/dev/null');

        $pgsqlHealthy = is_string($pgsqlHealth) && trim($pgsqlHealth) === 'healthy';
        $minioHealthy = is_string($minioHealth) && trim($minioHealth) === 'healthy';

        if ($pgsqlHealthy && $minioHealthy) {
            $allHealthy = true;
            echo "✅ Todos os serviços estão prontos!\n";
        } else {
            echo "   Tentativa {$attempt}/{$maxAttempts}: ";
            echo 'PostgreSQL: '.($pgsqlHealthy ? '✓' : '⏳').' | ';
            echo 'MinIO: '.($minioHealthy ? '✓' : '⏳')."\n";
            sleep(2);
        }
    }

    if (! $allHealthy) {
        echo "⚠️  Timeout aguardando serviços. Tentando continuar mesmo assim...\n";
        echo "Você pode verificar os logs com: ./vendor/bin/sail logs\n";
        // Aguardar mais 5 segundos antes de tentar
        sleep(5);
    }

    // Verificar se containers estão rodando
    $containers = shell_exec('docker ps | grep laravel');
    if (! is_string($containers) || empty(trim($containers))) {
        echo "❌ Containers Sail não estão rodando. Verifique os logs: ./vendor/bin/sail logs\n";
        exit(1);
    }

    echo "✅ Containers Sail iniciados com sucesso!\n";

    // 5. Executar setup automatizado (key:generate, migrate, storage:init, filament:optimize, npm)
    echo "📦 Executando setup do projeto...\n";
    run('./vendor/bin/sail artisan key:generate --ansi');
    run('./vendor/bin/sail artisan migrate --graceful --ansi');
    run('./vendor/bin/sail artisan storage:init');
    run('./vendor/bin/sail artisan filament:optimize');

    echo "📦 Instalando dependências e buildando assets...\n";
    run('./vendor/bin/sail npm install');
    run('./vendor/bin/sail npm run build');

    // 6. Popular banco de dados
    echo "🌱 Populando banco de dados...\n";
    run('./vendor/bin/sail artisan db:seed');

    // 7. Criar link de storage
    echo "🔗 Criando link de storage...\n";
    run('./vendor/bin/sail artisan storage:link');

    echo "✅ Instalação via Sail concluída com sucesso!\n";
}

/**
 * Remove o arquivo context7.json se existir.
 */
function removeContext7File(string $basePath): void
{
    $contextFile = $basePath.'/context7.json';

    if (file_exists($contextFile)) {
        echo "\n🧹 Limpando arquivos de indexação...\n";

        if (unlink($contextFile)) {
            echo "ℹ️  O arquivo context7.json foi removido.\n";
            echo "💡 Este arquivo serve apenas para indexação na ferramenta Context7 do repositório original.\n";
            echo "📝 Caso deseje indexar seu próprio projeto, acesse a página oficial do Context7 para obter informações atualizadas.\n";
        }
    }
}

/**
 * Exibe resumo final com informações importantes.
 */
function showFinalSummary(): void
{
    echo "\n".str_repeat('=', 60)."\n";
    echo "🎉 INSTALAÇÃO CONCLUÍDA COM SUCESSO!\n";
    echo str_repeat('=', 60)."\n\n";

    echo "🔐 CREDENCIAIS DE ACESSO:\n";
    echo "┌─────────────────────────────────────────────────────────┐\n";
    echo "│ Admin (Escopo Global):                                  │\n";
    echo "│   Email: admin@labsis.dev.br                            │\n";
    echo "│   Senha: mudar123                                       │\n";
    echo "│   Painel: http://localhost/admin                        │\n";
    echo "├─────────────────────────────────────────────────────────┤\n";
    echo "│ Sicrano (Tenant A - Owner, Tenant B - User):            │\n";
    echo "│   Email: sicrano@labsis.dev.br                          │\n";
    echo "│   Senha: mudar123                                       │\n";
    echo "│   Painel: http://localhost/user                          │\n";
    echo "├─────────────────────────────────────────────────────────┤\n";
    echo "│ Beltrano (Tenant A - User, Tenant B - Owner):           │\n";
    echo "│   Email: beltrano@labsis.dev.br                        │\n";
    echo "│   Senha: mudar123                                       │\n";
    echo "│   Painel: http://localhost/user                          │\n";
    echo "└─────────────────────────────────────────────────────────┘\n\n";

    echo "🛠️  COMANDOS ÚTEIS:\n";
    echo "┌─────────────────────────────────────────────────────────┐\n";
    echo "│ Parar containers:     ./vendor/bin/sail down           │\n";
    echo "│ Iniciar containers:   ./vendor/bin/sail up -d           │\n";
    echo "│ Ver logs:             ./vendor/bin/sail logs -f         │\n";
    echo "│ Executar comando:     ./vendor/bin/sail artisan [cmd]   │\n";
    echo "│ Shell do container:   ./vendor/bin/sail shell           │\n";
    echo "└─────────────────────────────────────────────────────────┘\n\n";

    echo "🌐 ACESSO ÀS APLICAÇÕES:\n";
    echo "┌─────────────────────────────────────────────────────────┐\n";
    echo "│ Painel Admin:         http://localhost/admin            │\n";
    echo "│ Painel Usuário:       http://localhost/user             │\n";
    echo "│ Website/Landing:      http://localhost                  │\n";
    echo "└─────────────────────────────────────────────────────────┘\n\n";

    echo "📚 DOCUMENTAÇÃO:\n";
    echo "Toda a documentação está disponível na pasta /docs\n";
    echo "Recomendamos a leitura para entender os recursos implementados.\n\n";

    echo "✨ Aproveite o LabSIS KIT!\n";
}

// ============================================================================
// EXECUÇÃO PRINCIPAL
// ============================================================================

echo "🚀 LabSIS KIT - Script de Instalação\n";
echo str_repeat('=', 50)."\n";

// 1. Verificação e instalação do PHP 8.5
echo "\n🔍 Verificando PHP 8.5...\n";
if (! checkPhpVersion()) {
    installPhp85();
} else {
    echo "✅ PHP 8.5+ já está instalado.\n";
}

// 2. Verificação e instalação das extensões PHP
echo "\n🔍 Verificando extensões PHP...\n";
$extensions = checkPhpExtensions();
if (! empty($extensions['missing'])) {
    installPhpExtensions($extensions['missing']);
} else {
    echo "✅ Todas as extensões PHP necessárias estão instaladas.\n";
}

// 3. Verificação e instalação do Composer
echo "\n🔍 Verificando Composer...\n";
if (! checkComposer()) {
    installComposer();
} else {
    echo "✅ Composer já está instalado.\n";
}

// 4. Verificação do Node.js e NPM (apenas orientação)
echo "\n🔍 Verificando Node.js e NPM...\n";
if (! checkNodeNpm()) {
    abortWithInstructions(
        'Node.js 18+',
        'https://nodejs.org/',
        'Por favor, instale o Node.js a partir da documentação oficial:'
    );
} else {
    echo "✅ Node.js 18+ e NPM já estão instalados.\n";
}

// 5. Verificação do Docker (apenas orientação)
echo "\n🔍 Verificando Docker...\n";
if (! checkDocker()) {
    abortWithInstructions(
        'Docker',
        'https://docs.docker.com/engine/install/ubuntu/',
        'Por favor, instale o Docker a partir da documentação oficial:'
    );
} else {
    echo "✅ Docker já está instalado.\n";
}

// 6. Verificação do Docker Compose v2+ (apenas orientação)
echo "\n🔍 Verificando Docker Compose v2+...\n";
if (! checkDockerCompose()) {
    abortWithInstructions(
        'Docker Compose v2+',
        'https://docs.docker.com/compose/install/',
        'Por favor, atualize o Docker Compose para versão 2.0 ou superior:'
    );
} else {
    echo "✅ Docker Compose v2+ já está disponível.\n";
}

// 7. Verificação e remoção do Apache2
echo "\n🔍 Verificando Apache2...\n";
if (checkApache2()) {
    if (checkApache2Running()) {
        echo "⚠️  Apache2 está instalado e rodando (conflita com Nginx do Sail)\n";
    } else {
        echo "⚠️  Apache2 está instalado mas não está rodando (conflita com Nginx do Sail)\n";
    }
    removeApache2();
} else {
    echo "✅ Apache2 não está instalado.\n";
}

// 8. Configuração das permissões Docker
echo "\n⚙️  Configurando Docker...\n";
configureDockerPermissions();

// 9. Instalação via Sail
runSailInstallation($basePath);

// 10. Limpeza de arquivos específicos
removeContext7File($basePath);

// 11. Resumo final
showFinalSummary();
