<?php

declare(strict_types=1);

use App\Enums\CourseStatus;
use App\Filament\Checkout\Pages\Checkout;
use App\Livewire\Public\CoursePurchase;
use App\Livewire\Public\CoursesCatalog;
use App\Models\Course;
use App\Models\Organization;
use App\Services\CartService;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

describe('adicionar cursos ao carrinho no site público', function (): void {
    it('mostra os cursos publicados com o botão de adicionar ao carrinho', function (): void {
        $organization = Organization::factory()->create();

        Course::factory()->published()->for($organization)->create([
            'title' => 'Curso de Fisiologia do Exercício',
            'price_in_cents' => 197.0,
        ]);

        Course::factory()->published()->for($organization)->create([
            'title' => 'Curso de Avaliação Física Funcional',
            'price_in_cents' => 197.0,
        ]);

        Livewire::test(CoursesCatalog::class)
            ->assertSuccessful()
            ->assertSee('Curso de Fisiologia do Exercício')
            ->assertSee('Curso de Avaliação Física Funcional')
            ->assertSee('Adicionar ao Carrinho')
            ->assertActionExists('addToCartAction')
            ->assertActionExists('cartAction');
    });

    it('coloca o curso no carrinho e abre o painel do carrinho', function (): void {
        $course = Course::factory()->published()->create([
            'title' => 'Curso de Biomecânica',
            'price_in_cents' => 250.0,
        ]);

        $cartService = app(CartService::class);
        $cartService->clear();

        expect($cartService->isEmpty())->toBeTrue();

        Livewire::test(CoursesCatalog::class)
            ->callAction(TestAction::make('addToCart')->arguments(['course' => $course]))
            ->assertActionMounted('cart');

        expect($cartService->count())->toBe(1);
        expect($cartService->has($course))->toBeTrue();
        expect($cartService->total())->toBe(250.0);
    });

    it('mostra no painel do carrinho os cursos já adicionados', function (): void {
        $cartService = app(CartService::class);
        $cartService->clear();

        $courseA = Course::factory()->published()->create([
            'title' => 'Curso A',
            'price_in_cents' => 100.0,
        ]);
        $courseB = Course::factory()->published()->create([
            'title' => 'Curso B',
            'price_in_cents' => 150.0,
        ]);

        $cartService->add($courseA);
        $cartService->add($courseB);

        expect($cartService->count())->toBe(2);
        expect($cartService->total())->toBe(250.0);

        Livewire::test(CoursesCatalog::class)
            ->mountAction('cart')
            ->assertActionMounted('cart')
            ->assertSee('Curso A')
            ->assertSee('Curso B');
    });

    it('exibe o texto correto no botão de finalizar conforme a quantidade de cursos', function (): void {
        $cartService = app(CartService::class);
        $cartService->clear();

        $courseA = Course::factory()->published()->create(['title' => 'Curso A']);
        $courseB = Course::factory()->published()->create(['title' => 'Curso B']);

        $cartService->add($courseA);

        $testCatalog = Livewire::test(CoursesCatalog::class);
        $action = $testCatalog->instance()->getAction('cart');
        expect($action->getModalSubmitAction()->getLabel())->toBe('Finalizar compra');

        $testPurchase = Livewire::test(CoursePurchase::class, ['course' => $courseA]);
        $actionPurchase = $testPurchase->instance()->getAction('cart');
        expect($actionPurchase->getModalSubmitAction()->getLabel())->toBe('Finalizar compra');

        $cartService->add($courseB);

        $testCatalog = Livewire::test(CoursesCatalog::class);
        $action = $testCatalog->instance()->getAction('cart');
        expect($action->getModalSubmitAction()->getLabel())->toBe('Finalizar compra de cursos');

        $testPurchase = Livewire::test(CoursePurchase::class, ['course' => $courseA]);
        $actionPurchase = $testPurchase->instance()->getAction('cart');
        expect($actionPurchase->getModalSubmitAction()->getLabel())->toBe('Finalizar compra de cursos');
    });

    it('carrega a página de checkout com o botão de gerar cobrança quando há cursos no carrinho', function (): void {
        $checkoutPanel = Filament::getPanel('checkout');
        Filament::setCurrentPanel($checkoutPanel);
        expect($checkoutPanel->hasNavigation())->toBeFalse();

        $cartService = app(CartService::class);
        $cartService->clear();

        $course = Course::factory()->published()->create();
        $cartService->add($course);

        Livewire::test(Checkout::class)
            ->assertSuccessful()
            ->assertSee('Gerar cobrança PIX')
            ->assertDontSee('Emails')
            ->assertActionExists('gerarCobranca');

        $this->get('/checkout')
            ->assertSuccessful()
            ->assertDontSee('mail-templates')
            ->assertDontSee('Emails');
    });

    it('remove um curso do carrinho', function (): void {
        $cartService = app(CartService::class);
        $cartService->clear();

        $course = Course::factory()->published()->create();
        $cartService->add($course);

        expect($cartService->count())->toBe(1);

        $cartService->remove((string) $course->id);

        expect($cartService->isEmpty())->toBeTrue();
    });

    it('também adiciona o curso ao carrinho a partir da página do curso', function (): void {
        $organization = Organization::factory()->create();
        $course = Course::factory()->published()->for($organization)->create([
            'title' => 'Fisiologia do Exercício Avançada',
            'price_in_cents' => 300.0,
        ]);

        $cartService = app(CartService::class);
        $cartService->clear();

        Livewire::test(CoursePurchase::class, ['course' => $course])
            ->assertActionExists('addToCartAction')
            ->assertActionExists('cartAction')
            ->callAction(TestAction::make('addToCart'))
            ->assertActionMounted('cart');

        expect($cartService->count())->toBe(1);
        expect($cartService->has($course))->toBeTrue();
    });

    it('não adiciona rascunho ao carrinho mesmo se o visitante enviar o id', function (): void {
        $organization = Organization::factory()->create();
        $draftCourse = Course::factory()->for($organization)->create([
            'status' => CourseStatus::Draft,
            'title' => 'Curso Confidencial em Rascunho',
        ]);

        $cartService = app(CartService::class);
        $cartService->clear();

        expect(fn () => Livewire::test(CoursesCatalog::class)
            ->callAction(TestAction::make('addToCart')->arguments(['course' => $draftCourse->id]))
        )->toThrow(\Throwable::class);

        expect($cartService->isEmpty())->toBeTrue();

        expect(fn () => Livewire::test(CoursesCatalog::class)
            ->callAction(TestAction::make('addToCart')->arguments(['course' => ['id' => $draftCourse->id]]))
        )->toThrow(\Throwable::class);

        expect($cartService->isEmpty())->toBeTrue();
    });

    it('bloqueia o carrinho na página do curso se o curso deixar de estar publicado', function (): void {
        $organization = Organization::factory()->create();

        $draft = Course::factory()->for($organization)->create([
            'status' => CourseStatus::Draft,
        ]);

        Livewire::test(CoursePurchase::class, ['course' => $draft])
            ->assertNotFound();

        $published = Course::factory()->published()->for($organization)->create();
        $livewireComponent = Livewire::test(CoursePurchase::class, ['course' => $published])
            ->assertSuccessful();

        $published->update(['status' => CourseStatus::Draft]);

        $livewireComponent->call('openCart')
            ->assertNotFound();
    });
});
