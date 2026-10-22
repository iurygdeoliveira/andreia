{{--
    Schema JSON-LD Maximalista — EAFS
    ============================================================
    INSTRUÇÕES DE PREENCHIMENTO:
    Substitua todos os campos marcados com [PLACEHOLDER] pelos
    dados reais antes de publicar.

    Validar em:
      - https://validator.schema.org/
      - https://search.google.com/test/rich-results
    ============================================================
--}}
@php
    $domain   = rtrim(config('app.url', 'https://eafs.com.br'), '/');
    $orgId    = $domain . '/#organization';
    $siteId   = $domain . '/#website';
    $pageId   = $domain . '/#webpage';
    $personId = $domain . '/#professor-celso';
    $faqId    = $domain . '/#faq';

    $graph = [
        // ── 1. ORGANIZATION ──────────────────────────────────────
        [
            '@type'         => 'EducationalOrganization',
            '@id'           => $orgId,
            'name'          => 'EAFS — Escola de Avaliação Física e Saúde',
            'alternateName' => 'EAFS',
            'url'           => $domain . '/',
            'logo'          => [
                '@type'   => 'ImageObject',
                '@id'     => $domain . '/#logo',
                'url'     => asset('images/og-cover.jpg'),
                'width'   => 1200,
                'height'  => 630,
                'caption' => 'EAFS — Escola de Avaliação Física e Saúde',
            ],
            'image'         => ['@id' => $domain . '/#logo'],
            'description'   => 'A maior plataforma de atualização profissional e cursos online para áreas da saúde com certificação reconhecida pelo MEC. Mais de 8.000 alunos formados.',
            'founder'       => ['@id' => $personId],
            'contactPoint'  => [
                '@type'           => 'ContactPoint',
                'contactType'     => 'customer support',
                'email'           => '[SEU_EMAIL_DE_SUPORTE]',
                'areaServed'      => 'BR',
                'availableLanguage' => 'pt-BR',
            ],
            // TODO: preencher com perfis reais
            'sameAs' => [
                // 'https://www.instagram.com/[SEU_INSTAGRAM]',
                // 'https://www.facebook.com/[SEU_FACEBOOK]',
                // 'https://www.youtube.com/@[SEU_YOUTUBE]',
                // 'https://www.linkedin.com/company/[SEU_LINKEDIN]',
                // 'https://www.wikidata.org/wiki/[Q_NUMBER]',   ← máxima autoridade
            ],
            // TODO: preencher com CNPJ real
            // 'taxID' => 'XX.XXX.XXX/0001-XX',
        ],

        // ── 2. WEBSITE ───────────────────────────────────────────
        [
            '@type'       => 'WebSite',
            '@id'         => $siteId,
            'url'         => $domain . '/',
            'name'        => 'EAFS — Escola de Avaliação Física e Saúde',
            'description' => 'Cursos online de Educação Física com Certificado MEC',
            'publisher'   => ['@id' => $orgId],
            'inLanguage'  => 'pt-BR',
            // SearchAction removido: a busca da landing é um filtro client-side,
            // não há endpoint /?q= real. Declarar capacidade inexistente é schema falso.
        ],

        // ── 3. WEBPAGE (home) ─────────────────────────────────────
        [
            '@type'            => 'WebPage',
            '@id'              => $pageId,
            'url'              => $domain . '/',
            'name'             => 'Cursos de Educação Física com Certificado MEC | EAFS',
            'description'      => 'Capacite-se com cursos práticos de Educação Física, Avaliação Física e Fisiologia do Exercício. Certificado aceito pelo MEC. Mais de 8.000 alunos formados.',
            'isPartOf'         => ['@id' => $siteId],
            'about'            => ['@id' => $orgId],
            'primaryImageOfPage' => ['@id' => $domain . '/#logo'],
            'inLanguage'       => 'pt-BR',
            'datePublished'    => '2024-01-01T00:00:00-03:00',
            'dateModified'     => now()->toIso8601String(),
            'breadcrumb'       => ['@id' => $domain . '/#breadcrumb'],
        ],

        // ── 4. BREADCRUMB ─────────────────────────────────────────
        [
            '@type' => 'BreadcrumbList',
            '@id'   => $domain . '/#breadcrumb',
            'itemListElement' => [
                [
                    '@type'  => 'ListItem',
                    'position' => 1,
                    'name'   => 'Início',
                    'item'   => $domain . '/',
                ],
            ],
        ],

        // ── 5. PERSON (Professor Celso) ───────────────────────────
        [
            '@type'      => 'Person',
            '@id'        => $personId,
            'name'       => 'Professor Celso',
            'jobTitle'   => 'Especialista em Educação Física, Avaliação Física e Fisiologia do Exercício',
            'worksFor'   => ['@id' => $orgId],
            'image'      => asset('images/professor-celso.webp'),
            'knowsAbout' => [
                'Educação Física',
                'Avaliação Física',
                'Fisiologia do Exercício',
                'Biomecânica',
                'Gestão de Academia',
            ],
            'alumniOf' => [
                ['@type' => 'EducationalOrganization', 'name' => 'Pós-graduação em Educação Física Escolar'],
                ['@type' => 'EducationalOrganization', 'name' => 'Pós-graduação em Fisiologia do Exercício'],
                ['@type' => 'EducationalOrganization', 'name' => 'Pós-graduação em Gestão de Academia'],
            ],
            // TODO: preencher com perfis reais do professor
            'sameAs' => [
                // 'https://www.linkedin.com/in/[LINKEDIN_PROF_CELSO]',
            ],
        ],

        // ── 6. FAQPAGE ───────────────────────────────────────────
        [
            '@type' => 'FAQPage',
            '@id'   => $faqId,
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name'  => 'O certificado da EAFS é válido na minha faculdade para horas complementares?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => 'Sim. O certificado emitido pela EAFS segue os termos do Decreto Presidencial Nº 5.124/2004 e a Resolução CNE Nº 04/99 (MEC), sendo amplamente aceito em Instituições de Ensino Superior de todo o Brasil como horas complementares ou de extensão para cursos de Educação Física e áreas da saúde.',
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name'  => 'Como e quando recebo o acesso aos cursos da EAFS?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => 'O acesso é imediato após a confirmação do pagamento. Você receberá login e senha diretamente no e-mail cadastrado e já poderá entrar na área do aluno e começar o primeiro módulo. Não há período de espera — assim que o pagamento for processado, a conta é ativada automaticamente.',
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name'  => 'Por quanto tempo tenho acesso aos cursos?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => 'O acesso à plataforma é válido por 1 ano completo a partir da data da matrícula. Durante esse período você pode assistir e revisitar as aulas quantas vezes quiser, baixar os materiais em PDF e emitir o certificado sem custos adicionais.',
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name'  => 'Os cursos da EAFS oferecem suporte ao aluno?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => 'Sim. O suporte é realizado de forma rápida e direta, tanto para dúvidas técnicas sobre a plataforma quanto para dúvidas de conteúdo das aulas. O atendimento acontece em horário comercial e a equipe se compromete a responder em até 24 horas úteis.',
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name'  => 'Há garantia de reembolso caso eu não goste do curso?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => 'Sim. Oferecemos 7 dias de garantia incondicional, conforme o Código de Defesa do Consumidor (Art. 49). Se por qualquer motivo você não ficar satisfeito nos primeiros 7 dias após a compra, faremos o reembolso integral, sem perguntas.',
                    ],
                ],
            ],
        ],
    ];

    // ── 7. COURSES (dados reais do catálogo publicado) ────────
    // Cada curso vira um nó Course + Offer, ancorado na Organization
    // via 'provider'. Só entram cursos publicados com preço definido.
    $catalogo = (isset($courses) && count($courses))
        ? $courses
        : \App\Models\Course::query()->where('status', \App\Enums\CourseStatus::Published)->get();

    foreach ($catalogo as $course) {
        $courseId = $domain . '/#curso-' . $course->slug;

        $graph[] = [
            '@type'       => 'Course',
            '@id'         => $courseId,
            'name'        => $course->title,
            'description' => $course->description
                ?: 'Curso online de capacitação profissional oferecido pela EAFS com certificado aceito pelo MEC.',
            'url'         => $domain . '/#cursos',
            'inLanguage'  => 'pt-BR',
            'provider'    => ['@id' => $orgId],
            'isAccessibleForFree' => false,
            'educationalLevel'    => 'Professional',
            'teaches'     => $course->tags->pluck('name')->all() ?: null,
            // courseWorkload NÃO é declarado: a carga horária real não está
            // modelada no banco. Preencher com valor arbitrário seria dado falso.
            'hasCourseInstance' => [
                '@type'      => 'CourseInstance',
                'courseMode' => 'online',
                'inLanguage' => 'pt-BR',
                'instructor' => ['@id' => $personId],
            ],
            'offers' => [
                '@type'         => 'Offer',
                'price'         => number_format((float) $course->price_in_cents, 2, '.', ''),
                'priceCurrency' => 'BRL',
                'availability'  => 'https://schema.org/InStock',
                'category'      => 'Curso online',
                'url'           => $domain . '/#cursos',
                'seller'        => ['@id' => $orgId],
            ],
        ];
    }

    // Remove chaves nulas dos nós de curso (ex.: 'teaches' sem tags)
    $graph = array_map(
        fn (array $node): array => array_filter($node, fn ($v): bool => $v !== null),
        $graph
    );
@endphp

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => $graph,
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
