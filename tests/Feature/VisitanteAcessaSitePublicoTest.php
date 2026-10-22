<?php

declare(strict_types=1);

describe('páginas institucionais abertas ao visitante', function (): void {
    it('mostra a política de privacidade para quem visita o site sem login', function (): void {
        $this->travelTo('2026-09-07 12:00:00');

        $this->get(route('privacidade'))
            ->assertSuccessful()
            ->assertViewIs('public.pages.privacidade')
            ->assertSee('Política de Privacidade')
            ->assertSee('Última atualização: 07/09/2026', false)
            ->assertSee('Lei nº 13.709/2018', false);
    });
});

describe('quando a url pública não existe', function (): void {
    it('mostra a página 404 pública em html, com link de volta à home e sem indexação', function (): void {
        $this->get('/esta-rota-nao-existe-xyz')
            ->assertNotFound()
            ->assertViewIs('public.errors.404')
            ->assertSee('Página não encontrada', false)
            ->assertSee('name="robots" content="noindex, follow"', false)
            ->assertSee('href="/"', false);
    });

    it('responde 404 em json sem o html público quando a requisição pede json', function (): void {
        $this->getJson('/esta-rota-nao-existe-xyz')
            ->assertNotFound()
            ->assertHeader('Content-Type', 'application/json');
    });
});

describe('o que o visitante pode e não pode abrir', function (): void {
    it('permite que o visitante abra a tela de login', function (): void {
        $this->get('/login')
            ->assertSuccessful();
    });

    it('redireciona o visitante para fora dos painéis admin, usuário e aluno', function (string $path): void {
        $this->get($path)->assertRedirect();
    })->with([
        'painel administrativo' => '/admin',
        'painel do usuário' => '/user',
        'painel do aluno' => '/student',
    ]);
});
