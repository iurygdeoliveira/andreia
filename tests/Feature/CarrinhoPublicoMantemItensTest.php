<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\Organization;
use App\Services\CartService;
use LukePOLO\LaraCart\LaraCart;

describe('regras do carrinho público', function (): void {
    beforeEach(function (): void {
        $this->cartService = app(CartService::class);
        $this->cartService->clear();
    });

    it('começa vazio, sem itens e com total zero', function (): void {
        expect($this->cartService->isEmpty())->toBeTrue()
            ->and($this->cartService->isNotEmpty())->toBeFalse()
            ->and($this->cartService->count())->toBe(0)
            ->and($this->cartService->total())->toBe(0.0)
            ->and($this->cartService->totalInCents())->toBe(0);
    });

    it('soma o preço de vários cursos adicionados', function (): void {
        $organization = Organization::factory()->create();

        $courseA = Course::factory()->published()->for($organization)->create([
            'title' => 'Curso de Fisiologia do Exercício',
            'price_in_cents' => 150.0,
        ]);

        $courseB = Course::factory()->published()->for($organization)->create([
            'title' => 'Curso de Biomecânica',
            'price_in_cents' => 200.0,
        ]);

        $this->cartService->add($courseA);
        $this->cartService->add($courseB);

        expect($this->cartService->count())->toBe(2)
            ->and($this->cartService->totalQuantity())->toBe(2)
            ->and($this->cartService->total())->toBe(350.0)
            ->and($this->cartService->totalInCents())->toBe(35000)
            ->and($this->cartService->has($courseA))->toBeTrue()
            ->and($this->cartService->has($courseB->getKey()))->toBeTrue();
    });

    it('não duplica o mesmo curso se o visitante clicar de novo em adicionar', function (): void {
        $course = Course::factory()->published()->create([
            'price_in_cents' => 100.0,
        ]);

        $this->cartService->add($course);
        $this->cartService->add($course);

        expect($this->cartService->count())->toBe(1)
            ->and($this->cartService->total())->toBe(100.0);
    });

    it('multiplica o total quando a quantidade de um curso aumenta', function (): void {
        $course = Course::factory()->published()->create([
            'price_in_cents' => 100.0,
        ]);

        $this->cartService->add($course);

        $this->cartService->updateQty((string) $course->id, 3);

        expect($this->cartService->totalQuantity())->toBe(3)
            ->and($this->cartService->total())->toBe(300.0);
    });

    it('tira o curso do carrinho quando a quantidade cai para zero', function (): void {
        $course = Course::factory()->published()->create();

        $this->cartService->add($course);
        expect($this->cartService->count())->toBe(1);

        $this->cartService->updateQty((string) $course->id, 0);

        expect($this->cartService->isEmpty())->toBeTrue();
    });

    it('remove um curso pelo id ou pelo identificador interno do item', function (): void {
        $courseA = Course::factory()->published()->create(['price_in_cents' => 50.0]);
        $courseB = Course::factory()->published()->create(['price_in_cents' => 75.0]);

        $this->cartService->add($courseA);
        $this->cartService->add($courseB);

        expect($this->cartService->count())->toBe(2);

        $this->cartService->remove((string) $courseA->id);
        expect($this->cartService->count())->toBe(1)
            ->and($this->cartService->has($courseA))->toBeFalse()
            ->and($this->cartService->has($courseB))->toBeTrue();

        $items = $this->cartService->getItems();
        $hashB = array_key_first($items);
        $this->cartService->remove($hashB);

        expect($this->cartService->isEmpty())->toBeTrue();
    });

    it('esvazia o carrinho de uma vez', function (): void {
        $course = Course::factory()->published()->create();
        $this->cartService->add($course);

        expect($this->cartService->count())->toBe(1);

        $this->cartService->clear();

        expect($this->cartService->isEmpty())->toBeTrue();
    });

    it('mantém o motor interno do carrinho disponível para o adapter', function (): void {
        expect($this->cartService->getLaraCart())->toBeInstanceOf(LaraCart::class);
    });
});
