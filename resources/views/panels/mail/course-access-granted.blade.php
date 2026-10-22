<x-mail::message>
# Pagamento confirmado

Olá, {{ $user->name }}.

@if ($courses->count() === 1)
Seu pagamento do curso **{{ $courses->first()->title }}** foi confirmado e o acesso já está liberado.
@else
Seu pagamento dos seguintes cursos foi confirmado e o acesso já está liberado:

@foreach ($courses as $c)
- **{{ $c->title }}**
@endforeach
@endif

@if ($passwordSetupUrl)
Para entrar na plataforma, defina sua senha:

<x-mail::button :url="$passwordSetupUrl">
Definir minha senha
</x-mail::button>

Se o botão não funcionar, copie e cole este endereço no navegador:
{{ $passwordSetupUrl }}
@else
O acesso já está disponível na sua conta. Basta entrar com o e-mail e a senha que você já usa:

<x-mail::button :url="route('filament.auth.auth.login')">
Acessar a plataforma
</x-mail::button>
@endif

Bons estudos!
</x-mail::message>

