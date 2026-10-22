<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Changelog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class ChangelogSeeder extends Seeder
{
    /**
     * Seed the application's changelog database table with user-friendly release descriptions and coherent pre-release versioning (v0.x).
     */
    public function run(): void
    {
        $changelogs = [
            [
                'version' => 'v0.9.3',
                'released_at' => date('Y-m-d'),
                'description' => 'Aprimoramento na organização inteligente de arquivos, garantindo que capas de cursos e documentos sejam sempre salvos de forma isolada e segura nas pastas exclusivas de cada instituição, mesmo quando enviados pela administração global.',
                'sort' => -2,
            ],
            [
                'version' => 'v0.9.2',
                'released_at' => '2026-07-31',
                'description' => 'Melhoria na interface de upload e gerenciamento dos materiais em PDF das aulas, oferecendo uma experiência mais fluida, estável e com recursos visuais aprimorados para os anexos.',
                'sort' => -1,
            ],
            [
                'version' => 'v0.9.1',
                'released_at' => '2026-07-31',
                'description' => 'Ajuste visual no contador de notificações para melhor alinhamento, posicionando-o no canto superior esquerdo do ícone, deixando a barra superior mais harmônica e de fácil visualização.',
                'sort' => 0,
            ],
            [
                'version' => 'v0.9.0',
                'released_at' => '2026-07-30',
                'description' => 'Liberação da visualização das atualizações do sistema para todos os usuários com leitura direta e simplificada na tabela, mantendo as opções de edição exclusivas para administradores.',
                'sort' => 1,
            ],
            [
                'version' => 'v0.8.0',
                'released_at' => '2026-07-30',
                'description' => 'Aprimoramento no design do botão de navegação e retorno, deixando a transição entre telas mais clara, rápida e intuitiva no painel de controle.',
                'sort' => 2,
            ],
            [
                'version' => 'v0.7.0',
                'released_at' => '2026-07-30',
                'description' => 'Inclusão de canal de feedback em todas as telas do sistema, permitindo que os usuários enviem sugestões e relatem dúvidas ou problemas diretamente da tela em que estão.',
                'sort' => 3,
            ],
            [
                'version' => 'v0.6.0',
                'released_at' => '2026-07-30',
                'description' => 'Atualização da Central de Notificações e Histórico de Novidades, permitindo acompanhar o lançamento de recursos e avisos importantes de forma organizada.',
                'sort' => 4,
            ],
            [
                'version' => 'v0.5.0',
                'released_at' => '2026-07-27',
                'description' => 'Lançamento da página de novidades do sistema e integração do sininho de notificações para avisar sobre atualizações e comunicados gerais.',
                'sort' => 5,
            ],
            [
                'version' => 'v0.4.0',
                'released_at' => '2026-07-24',
                'description' => 'Gestão completa de Organizações e Vínculos de Usuários, simplificando a organização de equipes e o controle de permissões por instituição.',
                'sort' => 6,
            ],
            [
                'version' => 'v0.3.9',
                'released_at' => '2026-07-24',
                'description' => 'Garantia de exibição e visualização contínua de arquivos e mídias, evitando falhas ao abrir anexos em diferentes ambientes de armazenamento.',
                'sort' => 7,
            ],
            [
                'version' => 'v0.3.8',
                'released_at' => '2026-07-24',
                'description' => 'Melhoria na estabilidade de uploads de imagens e mídias, assegurando que o sistema funcione com segurança mesmo em armazenamento local.',
                'sort' => 8,
            ],
            [
                'version' => 'v0.3.7',
                'released_at' => '2026-07-24',
                'description' => 'Proteção no envio de arquivos em formulários de Cursos, garantindo maior rapidez e prevenção de erros durante o upload de materiais.',
                'sort' => 9,
            ],
            [
                'version' => 'v0.3.6',
                'released_at' => '2026-07-24',
                'description' => 'Integração com monitoramento contínuo de desempenho do sistema, garantindo alta estabilidade e resposta ágil da plataforma.',
                'sort' => 10,
            ],
            [
                'version' => 'v0.3.5',
                'released_at' => '2026-07-24',
                'description' => 'Simplificação da listagem de cursos, proporcionando uma visualização mais limpa, direta e um carregamento mais veloz das informações.',
                'sort' => 11,
            ],
            [
                'version' => 'v0.3.4',
                'released_at' => '2026-07-24',
                'description' => 'Reforço nas travas de segurança do banco de dados em produção, prevenindo a perda acidental de informações valiosas do sistema.',
                'sort' => 12,
            ],
            [
                'version' => 'v0.3.3',
                'released_at' => '2026-07-24',
                'description' => 'Ajuste interno na execução de atualizações de banco de dados para permitir migrações e a constante evolução da estrutura de dados.',
                'sort' => 13,
            ],
            [
                'version' => 'v0.3.2',
                'released_at' => '2026-07-24',
                'description' => 'Otimização de tolerância no preenchimento de cadastros, garantindo maior flexibilidade na inclusão de dados no sistema.',
                'sort' => 14,
            ],
            [
                'version' => 'v0.3.1',
                'released_at' => '2026-07-24',
                'description' => 'Padronização no cadastro de contas de administração, garantindo acesso seguro e simplificado na implantação da plataforma.',
                'sort' => 15,
            ],
            [
                'version' => 'v0.3.0',
                'released_at' => '2026-07-24',
                'description' => 'Criação de rotinas automáticas de testes nos módulos de cursos, assegurando o funcionamento correto de cadastros, edições e exclusões.',
                'sort' => 16,
            ],
            [
                'version' => 'v0.2.4',
                'released_at' => '2026-07-20',
                'description' => 'Melhoria de compatibilidade no carregamento de telas, eliminando travamentos ou inconsistências visuais ao navegar pelo painel.',
                'sort' => 17,
            ],
            [
                'version' => 'v0.2.3',
                'released_at' => '2026-07-20',
                'description' => 'Estruturação de segurança nos arquivos de sessão, garantindo que o login dos usuários se mantenha ativo e seguro sem desconexões inesperadas.',
                'sort' => 18,
            ],
            [
                'version' => 'v0.2.2',
                'released_at' => '2026-07-20',
                'description' => 'Organização do sistema de memória cache rápida, permitindo que a navegação e abertura de páginas continuem sempre ágeis.',
                'sort' => 19,
            ],
            [
                'version' => 'v0.2.1',
                'released_at' => '2026-07-20',
                'description' => 'Otimização na inicialização da aplicação para que o sistema carregue em tempo reduzido ao ser acessado pelos usuários.',
                'sort' => 20,
            ],
            [
                'version' => 'v0.2.0',
                'released_at' => '2026-07-20',
                'description' => 'Estruturação inicial do projeto, definindo a arquitetura básica de cursos, mídias e gestão de usuários.',
                'sort' => 21,
            ],
            [
                'version' => 'v0.1.1',
                'released_at' => '2026-07-08',
                'description' => 'Ajustes de desempenho e refinamento do layout das telas principais do sistema.',
                'sort' => 22,
            ],
            [
                'version' => 'v0.1.0',
                'released_at' => '2026-07-08',
                'description' => 'Criação do repositório e estrutura base para o desenvolvimento da plataforma EAFS.',
                'sort' => 23,
            ],
        ];

        // Se houver registros antigos com hashes de commit, limpa a tabela para aplicar o novo padrão v0.x
        Changelog::truncate();

        foreach ($changelogs as $item) {
            Changelog::create([
                'version' => $item['version'],
                'released_at' => $item['released_at'],
                'description' => $item['description'],
                'sort' => $item['sort'],
            ]);
        }

        Cache::forget('changelog:latest_date');
    }
}
