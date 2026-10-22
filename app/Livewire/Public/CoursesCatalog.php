<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Enums\CourseStatus;
use App\Filament\Checkout\Pages\Checkout;
use App\Models\Course;
use App\Services\CartService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class CoursesCatalog extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public string $search = '';

    /**
     * @return Collection<int, Course>
     */
    #[Computed]
    public function courses(): Collection
    {
        $query = Course::query()
            ->where('status', CourseStatus::Published)
            ->with(['media', 'tags'])
            ->latest();

        if (filled($this->search)) {
            $term = '%'.trim(mb_strtolower($this->search)).'%';
            $query->where(function ($q) use ($term): void {
                $q->whereRaw('LOWER(title) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(description) LIKE ?', [$term]);
            });
        }

        return $query->get();
    }

    #[On('open-cart')]
    public function openCart(CartService $cart): void
    {
        $this->dispatch('cart-updated', count: $cart->count());
        $this->mountAction('cart');
    }

    public function addToCartAction(): Action
    {
        return Action::make('addToCart')
            ->label('Adicionar ao Carrinho')
            ->icon(Heroicon::OutlinedShoppingCart)
            ->size(Size::Medium)
            ->extraAttributes(['class' => 'w-full'])
            ->color('primary')
            ->action(function (array $arguments, array $data, CartService $cart): void {
                $course = $this->resolveCourse($arguments['course'] ?? $data['course'] ?? null);
                $cart->add($course);

                $this->dispatch('cart-updated', count: $cart->count());

                Notification::make()
                    ->title('Curso adicionado ao carrinho!')
                    ->success()
                    ->send();

                $this->replaceMountedAction('cart');
            });
    }

    public function cartAction(): Action
    {
        return Action::make('cart')
            ->label('Carrinho')
            ->icon(Heroicon::OutlinedShoppingCart)
            ->modal()
            ->modalWidth(Width::Large)
            ->slideOver()
            ->stickyModalHeader()
            ->stickyModalFooter()
            ->modalIcon(Heroicon::OutlinedShoppingCart)
            ->modalHeading('Carrinho de Cursos')
            ->modalCancelAction(fn (Action $action): Action => $action
                ->label('Continuar Comprando')
                ->color('gray')
                ->icon(Heroicon::OutlinedArrowLeft)
                ->extraAttributes(['class' => 'w-full'])
            )
            ->modalSubmitAction(fn (Action $action, CartService $cart) => $action
                ->visible(fn (): bool => $cart->isNotEmpty())
                ->icon(Heroicon::OutlinedCheckCircle)
                ->extraAttributes(['class' => 'w-full'])
                ->label(function () use ($cart): string {
                    return count($cart->getItems()) > 1
                        ? 'Finalizar compra de cursos'
                        : 'Finalizar compra';
                })
            )
            ->fillForm(fn (CartService $cart): array => [
                'courses' => array_values(array_map(fn (array $item): array => [
                    'hash' => $item['hash'],
                    'id' => $item['id'],
                    'title' => $item['title'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'description' => $item['description'],
                ], $cart->getItems())),
            ])
            ->schema([
                Repeater::make('courses')
                    ->hiddenLabel()
                    ->columns(3)
                    ->reorderable(false)
                    ->addable(false)
                    ->visible(fn (CartService $cart): bool => $cart->isNotEmpty())
                    ->itemLabel(fn (array $state): string => (string) ($state['title'] ?? 'Curso'))
                    ->deleteAction(fn (Action $action) => $action
                        ->requiresConfirmation()
                        ->modalHeading('Remover curso')
                        ->modalDescription('Tem certeza que deseja remover este curso do carrinho?')
                        ->overlayParentActions()
                        ->before(function (array $arguments, Repeater $component, CartService $cart): void {
                            $item = $component->getRawItemState($arguments['item']);
                            if (is_array($item)) {
                                $cart->remove($item['hash'] ?? $item['id']);
                                $this->dispatch('cart-updated', count: $cart->count());
                            }
                        })
                        ->after(function (CartService $cart): void {
                            $this->dispatch('cart-updated', count: $cart->count());
                        })
                    )
                    ->schema([
                        TextEntry::make('description')
                            ->hiddenLabel()
                            ->size(TextSize::ExtraSmall)
                            ->columnSpan(2),
                        TextEntry::make('price')
                            ->hiddenLabel()
                            ->money('BRL', locale: 'pt_BR')
                            ->weight(FontWeight::Bold)
                            ->alignEnd(),
                    ]),

                TextEntry::make('empty')
                    ->hiddenLabel()
                    ->icon(Heroicon::ShoppingCart)
                    ->alignCenter()
                    ->state(fn (CartService $cart): string => $cart->isEmpty() ? 'Seu carrinho está vazio. Comece adicionando cursos!' : '')
                    ->visible(fn (CartService $cart): bool => $cart->isEmpty()),

                TextEntry::make('total')
                    ->money('BRL', locale: 'pt_BR')
                    ->weight(FontWeight::Bold)
                    ->size(TextSize::Large)
                    ->color('success')
                    ->alignEnd()
                    ->inlineLabel()
                    ->state(fn (CartService $cart): float => $cart->total())
                    ->visible(fn (CartService $cart): bool => $cart->isNotEmpty()),
            ])
            ->action(function (CartService $cart): void {
                if ($cart->isEmpty()) {
                    return;
                }

                $this->redirect(Checkout::getUrl(panel: 'checkout'));
            });
    }

    public function buyAction(): Action
    {
        return Action::make('buyAction')
            ->label('Garantir Meu Acesso')
            ->icon(Heroicon::OutlinedSparkles)
            ->size(Size::Large)
            ->extraAttributes(['class' => 'w-full'])
            ->color('primary')
            ->action(function (array $arguments, CartService $cart): void {
                $course = $this->resolveCourse($arguments['course'] ?? null);
                $cart->add($course);
                $this->redirect(Checkout::getUrl(panel: 'checkout'));
            });
    }

    public function render(CartService $cart): View
    {
        $this->dispatch('cart-updated', count: $cart->count());

        return view('livewire.public.courses-catalog', [
            'courses' => $this->courses,
        ]);
    }

    protected function resolveCourse(mixed $course): Course
    {
        $query = Course::query()->where('status', CourseStatus::Published);

        if ($course instanceof Course) {
            return (clone $query)->findOrFail($course->getKey());
        }

        if (is_array($course)) {
            if (! empty($course['id'])) {
                return (clone $query)->findOrFail($course['id']);
            }

            if (! empty($course['checkout_code'])) {
                return (clone $query)->where('checkout_code', $course['checkout_code'])->firstOrFail();
            }
        }

        if (is_numeric($course)) {
            return (clone $query)->findOrFail((int) $course);
        }

        if (is_string($course)) {
            return (clone $query)->where('checkout_code', $course)->firstOrFail();
        }

        throw new \InvalidArgumentException('Curso inválido fornecido para a action.');
    }
}
