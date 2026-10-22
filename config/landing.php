<?php

declare(strict_types=1);

/**
 * Estatísticas da landing (quando não há models Project/Developer no app).
 *
 * .env: LANDING_STATS_PROJECTS_EVALUATION, LANDING_STATS_DEVELOPERS
 * Models opcionais: LANDING_PROJECT_MODEL, LANDING_DEVELOPER_MODEL
 */
return [
    'eloquent_models' => [
        'project' => env('LANDING_PROJECT_MODEL', 'App\Models\Project'),
        'developer' => env('LANDING_DEVELOPER_MODEL', 'App\Models\Developer'),
    ],

    'stats' => [
        'projects_in_evaluation' => env('LANDING_STATS_PROJECTS_EVALUATION'),
        'developers_involved' => env('LANDING_STATS_DEVELOPERS'),
    ],

    /*
     * Numero de WhatsApp que recebe os leads da landing, em formato E.164 sem
     * o "+" (ex.: 5588999998888). Enquanto estiver vazio, os blocos que
     * dependem dele nao sao renderizados - nunca cai para um numero fictício.
     */
    'whatsapp' => env('LANDING_WHATSAPP'),

    /*
     * Depoimentos exibidos acima do catalogo. Ficam vazios de proposito: o
     * briefing marca as citacoes como pendentes do cliente e prova social
     * inventada e um sinal de confianca falso. Assim que o Celso enviar as
     * frases reais, preencher aqui - a secao passa a renderizar sozinha.
     *
     * Formato de cada item:
     *   ['nome' => '', 'papel' => '', 'foto' => '', 'citacao' => '']
     */
    'depoimentos' => [],

    /*
     * Perfis sociais. Cada icone do rodape so e renderizado quando a URL
     * correspondente esta preenchida - link social quebrado custa mais
     * confianca do que a ausencia do icone.
     */
    'social' => [
        'instagram' => env('LANDING_INSTAGRAM'),
        'facebook' => env('LANDING_FACEBOOK'),
        'youtube' => env('LANDING_YOUTUBE'),
    ],
];
