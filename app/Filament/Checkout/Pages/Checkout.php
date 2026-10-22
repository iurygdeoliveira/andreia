<?php

declare(strict_types=1);

namespace App\Filament\Checkout\Pages;

use App\DTOs\Asaas\BuyerData;
use App\Exceptions\CourseAlreadyActiveException;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use App\Rules\CpfCnpj;
use App\Services\CartService;
use App\Services\CheckoutService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

/**
 * Página Filament de checkout para carrinho multi-curso (ADR-0004, RF-04).
 * Suporta tanto compradores anônimos quanto alunos autenticados.
 *
 * @property Schema $form
 */
class Checkout extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = '';

    protected string $view = 'filament.checkout.pages.checkout';

    public ?array $data = [];

    public static function getRoutePath(Panel $panel): string
    {
        return '/';
    }

    public static function getRelativeRouteName(Panel $panel): string
    {
        return 'checkout';
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::SevenExtraLarge;
    }

    public static function canAccess(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'checkout';
    }

    public function mount(CartService $cart): void
    {
        if ($cart->isEmpty()) {
            $this->redirect(route('home'));

            return;
        }

        $user = auth()->user();
        if ($user instanceof User) {
            $this->data['name'] = $user->name;
            $this->data['email'] = $user->email;
            $this->data['document'] = $user->document ?? '';
            $this->data['phone'] = $user->phone ?? '';
        }

        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Seus dados para matrícula')
                    ->description('Informe seus dados para emissão do certificado MEC e liberação de acesso.')
                    ->icon(Heroicon::OutlinedUser)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome completo')
                            ->placeholder('Como deve constar no seu certificado')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->placeholder('Onde você receberá seu acesso')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('document')
                            ->label('CPF')
                            ->placeholder('000.000.000-00')
                            ->mask('999.999.999-99')
                            ->rules([new CpfCnpj])
                            ->required(),

                        TextInput::make('phone')
                            ->label('WhatsApp / Telefone')
                            ->placeholder('(00) 00000-0000')
                            ->mask('(99) 99999-9999')
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function gerarCobrancaAction(): Action
    {
        return Action::make('gerarCobranca')
            ->label('Gerar cobrança PIX')
            ->icon(Heroicon::OutlinedQrCode)
            ->color('primary')
            ->size('lg')
            ->extraAttributes([
                'class' => 'w-full py-4 text-base font-bold shadow-lg',
            ])
            ->action(fn (CartService $cart, CheckoutService $checkout) => $this->confirmar($cart, $checkout));
    }

    protected function confirmar(CartService $cart, CheckoutService $checkout): void
    {
        $formData = $this->form->getState();

        $courses = $cart->getCourses();
        if ($courses->isEmpty()) {
            Notification::make()
                ->title('Seu carrinho está vazio')
                ->warning()
                ->send();

            $this->redirect(route('home'));

            return;
        }

        $totalAmount = (float) $courses->sum(fn (Course $course): float => (float) $course->price_in_cents);
        if ($totalAmount < 5.00) {
            Notification::make()
                ->title('Valor mínimo não atingido')
                ->body('O valor total do pedido deve ser de pelo menos R$ 5,00 para geração de cobrança PIX.')
                ->warning()
                ->send();

            return;
        }

        $buyerData = new BuyerData(
            name: (string) $formData['name'],
            email: (string) $formData['email'],
            document: (string) preg_replace('/\D/', '', (string) $formData['document']),
            phone: (string) preg_replace('/\D/', '', (string) $formData['phone']),
        );

        $ownedOrderIds = session('checkout.order_ids', []);

        $onOrderCreated = function (Order $order): void {
            $ids = session('checkout.order_ids', []);
            if (! is_array($ids)) {
                $ids = [];
            }
            array_unshift($ids, $order->getKey());
            $ids = array_values(array_unique($ids));
            session(['checkout.order_ids' => array_slice($ids, 0, 10)]);
        };

        try {
            $order = $checkout->processPixCheckout(
                $courses,
                $buyerData,
                ownedOrderIds: is_array($ownedOrderIds) ? $ownedOrderIds : [],
                onOrderCreated: $onOrderCreated,
            );
        } catch (CourseAlreadyActiveException $e) {
            $user = auth()->user();
            $buyerEmail = (string) $formData['email'];

            if ($user instanceof User && hash_equals(mb_strtolower($user->email), mb_strtolower($buyerEmail))) {
                $titles = $e->getCourses()->pluck('title')->implode(', ');
                Notification::make()
                    ->title('Cursos já ativos')
                    ->body("Você já tem acesso ativo a: {$titles}. Remova-os do carrinho para continuar.")
                    ->warning()
                    ->send();
            } else {
                Notification::make()
                    ->title('Não foi possível concluir a compra')
                    ->body('Não foi possível concluir a compra com este e-mail. Se você já é aluno, entre na sua conta para ver seus cursos.')
                    ->warning()
                    ->send();
            }

            return;
        }

        if (! $order instanceof Order) {
            Notification::make()
                ->title('Não conseguimos gerar sua cobrança agora')
                ->body('Confira os dados e tente novamente em alguns instantes.')
                ->danger()
                ->send();

            return;
        }

        $ids = session('checkout.order_ids', []);
        if (! is_array($ids)) {
            $ids = [];
        }
        array_unshift($ids, $order->getKey());
        $ids = array_values(array_unique($ids));
        session(['checkout.order_ids' => array_slice($ids, 0, 10)]);

        $cart->clear();

        $this->redirect(route('pedido.pagamento', ['pedido' => $order->order_number]));
    }

    public function getTitle(): string
    {
        return 'Finalizar Matrícula';
    }

    public function getHeading(): string
    {
        return 'Finalizar Matrícula';
    }
}
