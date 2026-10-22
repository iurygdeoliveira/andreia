<?php

declare(strict_types=1);

use App\Models\Course;

describe('sitemap que os buscadores leem', function (): void {
    it('entrega o sitemap em xml com a home e a política de privacidade', function (): void {
        $this->travelTo('2026-09-07 12:00:00');

        $this->get(route('sitemap'))
            ->assertSuccessful()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<loc>'.route('home').'</loc>', false)
            ->assertSee('<loc>'.route('privacidade').'</loc>', false)
            ->assertSee('<lastmod>'.now()->startOfMonth()->toAtomString().'</lastmod>', false);
    });

    it('não inclui rascunhos nem urls de pagamento ou inscrição no sitemap', function (): void {
        $draft = Course::factory()->create();
        $published = Course::factory()->published()->create();

        $this->get(route('sitemap'))
            ->assertSuccessful()
            ->assertDontSee('/curso/'.$draft->checkout_code, false)
            ->assertDontSee('/curso/'.$published->checkout_code.'/checkout', false)
            ->assertDontSee('/pagamento/', false)
            ->assertDontSee('/inscricao', false);
    });
});
