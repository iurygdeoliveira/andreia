<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\Lead;
use App\Models\Organization;
use Illuminate\Support\Str;

describe('página pública de um curso', function (): void {
    it('mostra título e descrição de um curso publicado para quem visita sem login', function (): void {
        $course = Course::factory()->published()->create([
            'title' => 'Fisiologia do Exercício',
            'description' => 'Base científica para o treino.',
        ]);

        $this->get(route('curso.show', $course))
            ->assertSuccessful()
            ->assertViewIs('public.pages.curso')
            ->assertSee('Fisiologia do Exercício')
            ->assertSee('Base científica para o treino.');
    });

    it('esconde curso em rascunho mesmo quando o visitante conhece o código público da url', function (): void {
        $course = Course::factory()->create();

        $this->get('/curso/'.$course->checkout_code)
            ->assertNotFound()
            ->assertViewIs('public.errors.404');
    });

    it('esconde um curso publicado que já foi excluído', function (): void {
        $course = Course::factory()->published()->create();
        $codigoPublico = $course->checkout_code;
        $course->delete();

        $this->get('/curso/'.$codigoPublico)
            ->assertNotFound()
            ->assertViewIs('public.errors.404');
    });

    it('não abre o curso pelo slug amigável, só pelo código público da url', function (): void {
        $organizationA = Organization::factory()->create();
        $organizationB = Organization::factory()->create();

        Course::factory()->published()->for($organizationA)->create([
            'slug' => 'fisiologia',
            'title' => 'Fisiologia Org A',
        ]);
        Course::factory()->published()->for($organizationB)->create([
            'slug' => 'fisiologia',
            'title' => 'Fisiologia Org B',
        ]);

        $this->get('/curso/fisiologia')
            ->assertNotFound()
            ->assertDontSee('Fisiologia Org A')
            ->assertDontSee('Fisiologia Org B');
    });

    it('não abre o curso pelo id numérico do banco', function (): void {
        $course = Course::factory()->published()->create();

        $this->get('/curso/'.$course->getKey())
            ->assertNotFound()
            ->assertDontSee($course->title);
    });

    it('rejeita códigos públicos inventados ou maliciosos na url do curso', function (string $chave): void {
        Course::factory()->published()->create();

        $this->get('/curso/'.$chave)
            ->assertNotFound()
            ->assertViewIs('public.errors.404');
    })->with([
        'uuid inexistente' => 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee',
        'fragmento sql' => "1' OR '1'='1",
        'travessia de caminho' => '..%2Fadmin',
        'uuid maiúsculo de curso inexistente' => Str::upper((string) Str::uuid()),
    ]);

    it('não executa script injetado no título ou na descrição do curso', function (): void {
        $course = Course::factory()->published()->create([
            'title' => '<script>alert("xss")</script>',
            'description' => '<img src=x onerror=alert(1)>',
        ]);

        $this->get(route('curso.show', $course))
            ->assertSuccessful()
            ->assertDontSee('<script>alert("xss")</script>', false)
            ->assertDontSee('<img src=x onerror=alert(1)>', false);
    });

    it('pede aos buscadores que não indexem a página do curso', function (): void {
        $course = Course::factory()->published()->create();

        $this->get(route('curso.show', $course))
            ->assertSuccessful()
            ->assertSee('name="robots" content="noindex, follow"', false);
    });
});

describe('formulário de interesse na página do curso', function (): void {
    it('grava o contato do visitante e o leva à página de obrigado', function (): void {
        $course = Course::factory()->published()->create();

        $this->from(route('curso.show', $course))
            ->post(route('curso.lead', $course), [
                'name' => 'Ana Silva',
                'email' => 'ana@example.com',
                'phone' => '88999998888',
                'profile' => 'Estudante',
            ])
            ->assertRedirect(route('curso.obrigado', $course))
            ->assertSessionHas('lead_name', 'Ana Silva');

        $this->assertDatabaseHas('leads', [
            'course_id' => $course->getKey(),
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
            'phone' => '88999998888',
            'profile' => 'Estudante',
        ]);
    });

    it('grava o contato no curso da url mesmo se o envio tentar apontar outro curso', function (): void {
        $course = Course::factory()->published()->create();
        $other = Course::factory()->published()->create();

        $this->from(route('curso.show', $course))
            ->post(route('curso.lead', $course), [
                'name' => 'Ana Silva',
                'email' => 'ana@example.com',
                'phone' => '88999998888',
                'course_id' => $other->getKey(),
                'id' => 999_999,
            ])
            ->assertRedirect(route('curso.obrigado', $course));

        $this->assertDatabaseHas('leads', [
            'email' => 'ana@example.com',
            'course_id' => $course->getKey(),
        ]);
        $this->assertDatabaseMissing('leads', [
            'email' => 'ana@example.com',
            'course_id' => $other->getKey(),
        ]);
        expect(Lead::query()->whereKey(999_999)->exists())->toBeFalse();
    });

    it('não grava contato de um curso em rascunho', function (): void {
        $course = Course::factory()->create();

        $this->post(route('curso.lead', $course), [
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
            'phone' => '88999998888',
        ])->assertNotFound();

        $this->assertDatabaseCount('leads', 0);
    });

    it('devolve as mensagens públicas quando faltam nome, e-mail ou telefone', function (): void {
        $course = Course::factory()->published()->create();

        $this->from(route('curso.show', $course))
            ->post(route('curso.lead', $course), [])
            ->assertRedirect(route('curso.show', $course))
            ->assertInvalid([
                'name' => 'Precisamos do seu nome para emitir o certificado corretamente.',
                'email' => 'Informe o e-mail onde você quer receber o acesso.',
                'phone' => 'Informe um WhatsApp para liberarmos seu acesso.',
            ]);

        $this->assertDatabaseCount('leads', 0);
    });

    it('rejeita e-mail incompleto com a mensagem pública', function (): void {
        $course = Course::factory()->published()->create();

        $this->from(route('curso.show', $course))
            ->post(route('curso.lead', $course), [
                'name' => 'Ana Silva',
                'email' => 'ana-sem-dominio',
                'phone' => '88999998888',
            ])
            ->assertRedirect(route('curso.show', $course))
            ->assertInvalid([
                'email' => 'Esse e-mail parece incompleto. Confira e tente de novo.',
            ]);

        $this->assertDatabaseCount('leads', 0);
    });

    it('rejeita telefone com mais de 20 caracteres', function (): void {
        $course = Course::factory()->published()->create();

        $this->from(route('curso.show', $course))
            ->post(route('curso.lead', $course), [
                'name' => 'Ana Silva',
                'email' => 'ana@example.com',
                'phone' => str_repeat('9', 21),
            ])
            ->assertRedirect(route('curso.show', $course))
            ->assertInvalid(['phone']);

        $this->assertDatabaseCount('leads', 0);
    });

    it('bloqueia o 11º envio anônimo de contato no mesmo curso', function (): void {
        $course = Course::factory()->published()->create();
        $url = route('curso.lead', $course);

        foreach (range(1, 10) as $attempt) {
            $this->from(route('curso.show', $course))
                ->post($url, [
                    'name' => 'Ana Silva',
                    'email' => "ana{$attempt}@example.com",
                    'phone' => '88999998888',
                ])
                ->assertRedirect(route('curso.obrigado', $course));
        }

        $this->post($url, [
            'name' => 'Ana Silva',
            'email' => 'ana-final@example.com',
            'phone' => '88999998888',
        ])->assertTooManyRequests();

        $this->assertDatabaseCount('leads', 10);
        $this->assertDatabaseMissing('leads', ['email' => 'ana-final@example.com']);
    });
});

describe('página de obrigado depois do contato', function (): void {
    it('mostra a página de obrigado só depois de o visitante enviar o contato', function (): void {
        $course = Course::factory()->published()->create(['title' => 'Fisiologia do Exercício']);

        $this->from(route('curso.show', $course))
            ->post(route('curso.lead', $course), [
                'name' => 'Ana Silva',
                'email' => 'ana@example.com',
                'phone' => '88999998888',
            ])
            ->assertRedirect(route('curso.obrigado', $course));

        $this->get(route('curso.obrigado', $course))
            ->assertSuccessful()
            ->assertViewIs('public.pages.obrigado')
            ->assertSee('Parabéns pela decisão, Ana!', false)
            ->assertSee('Fisiologia do Exercício')
            ->assertSee('name="robots" content="noindex, nofollow"', false);
    });

    it('volta para a página do curso se o visitante abrir obrigado sem ter enviado o contato', function (): void {
        $course = Course::factory()->published()->create();

        $this->get(route('curso.obrigado', $course))
            ->assertSuccessful()
            ->assertViewIs('public.pages.curso')
            ->assertDontSee('Parabéns pela decisão', false);
    });

    it('não executa script injetado no nome do visitante na página de obrigado', function (): void {
        $course = Course::factory()->published()->create();

        $this->from(route('curso.show', $course))
            ->post(route('curso.lead', $course), [
                'name' => '<script>alert("xss")</script>',
                'email' => 'ana@example.com',
                'phone' => '88999998888',
            ])
            ->assertRedirect(route('curso.obrigado', $course));

        $this->get(route('curso.obrigado', $course))
            ->assertSuccessful()
            ->assertDontSee('<script>alert("xss")</script>', false);
    });

    it('não mostra a página de obrigado de um curso em rascunho', function (): void {
        $course = Course::factory()->create();

        $this->withSession(['lead_name' => 'Ana'])
            ->get(route('curso.obrigado', $course))
            ->assertNotFound();
    });
});
