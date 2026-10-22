<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\User;

describe('home pública e catálogo de cursos', function (): void {
    it('mostra a home para quem visita o site sem login', function (): void {
        $this->get(route('home'))
            ->assertSuccessful()
            ->assertViewIs('public.pages.home')
            ->assertSee('Horas complementares garantidas', false)
            ->assertSee('Já sou aluno', false);
    });

    it('continua mostrando a home pública depois que a pessoa entra na conta', function (): void {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('home'))
            ->assertSuccessful()
            ->assertViewIs('public.pages.home')
            ->assertSee('Horas complementares garantidas', false);
    });

    it('lista no catálogo só cursos publicados e esconde rascunhos', function (): void {
        Course::factory()->create(['title' => 'Rascunho Secreto XYZ']);
        Course::factory()->published()->create(['title' => 'Curso Publicado ABC']);

        $this->get(route('home'))
            ->assertSuccessful()
            ->assertSee('Curso Publicado ABC')
            ->assertDontSee('Rascunho Secreto XYZ');
    });

    it('mostra que não há cursos quando nenhum está publicado', function (): void {
        Course::factory()->create(['title' => 'Ainda em rascunho']);

        $this->get(route('home'))
            ->assertSuccessful()
            ->assertSee('Nenhum curso disponível no momento.')
            ->assertDontSee('Ainda em rascunho');
    });

    it('não deixa um título malicioso quebrar o json-ld da home', function (): void {
        Course::factory()->published()->create([
            'title' => '</script><script>alert(1)</script>',
            'description' => '</script><script>alert(2)</script>',
        ]);

        $content = $this->get(route('home'))->assertSuccessful()->getContent();

        preg_match('/<script type="application\/ld\+json">\s*(.*?)\s*<\/script>/s', $content, $matches);

        expect($matches[1] ?? null)->not->toBeNull();

        $decoded = json_decode($matches[1], true);

        expect(json_last_error())->toBe(JSON_ERROR_NONE)
            ->and($decoded)->toBeArray()
            ->and($decoded['@graph'] ?? null)->toBeArray();
    });

    it('escapa html perigoso no título e na descrição do catálogo', function (): void {
        Course::factory()->published()->create([
            'title' => '<img src=x onerror=alert(1)>',
            'description' => '<b>bold</b>',
        ]);

        $this->get(route('home'))
            ->assertSuccessful()
            ->assertSee('&lt;img src=x onerror=alert(1)&gt;', false)
            ->assertSee('&lt;b&gt;bold&lt;/b&gt;', false);
    });

    it('pede cache de borda na home para acelerar visitas repetidas', function (): void {
        $this->get(route('home'))
            ->assertSuccessful()
            ->assertHeader('Cache-Control', 'max-age=3600, public, s-maxage=86400');
    });

    it('autoriza buscadores a indexar a home pública', function (): void {
        $this->get(route('home'))
            ->assertSuccessful()
            ->assertSee('name="robots" content="index, follow', false);
    });
});
