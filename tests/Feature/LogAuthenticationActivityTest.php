<?php

declare(strict_types=1);

use App\Models\AuthenticationLog;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('LogAuthenticationActivity Listener', function () {
    it('ignora evento Failed quando usuário for nulo sem lançar violação de integridade', function () {
        $initialCount = AuthenticationLog::count();

        event(new Failed('web', null, [
            'email' => 'email-inexistente@labsis.dev.br',
            'password' => 'senhaqualquer',
        ]));

        expect(AuthenticationLog::count())->toBe($initialCount);
    });

    it('registra evento Failed quando usuário existir no banco', function () {
        $user = User::factory()->create(['email' => 'usuario@eafs.com.br']);

        event(new Failed('web', $user, [
            'email' => $user->email,
            'password' => 'senhaincorreta',
        ]));

        $log = AuthenticationLog::latest('id')->first();

        expect($log)->not->toBeNull();
        expect($log->authenticatable_id)->toBe($user->id);
        expect($log->authenticatable_type)->toBe($user::class);
        expect($log->login_successful)->toBeFalse();
    });

    it('registra evento Login com sucesso', function () {
        $user = User::factory()->create(['email' => 'usuario@eafs.com.br']);

        event(new Login('web', $user, false));

        $log = AuthenticationLog::latest('id')->first();

        expect($log)->not->toBeNull();
        expect($log->authenticatable_id)->toBe($user->id);
        expect($log->login_successful)->toBeTrue();
    });

    it('atualiza logout_at no evento Logout', function () {
        $user = User::factory()->create(['email' => 'usuario@eafs.com.br']);

        event(new Login('web', $user, false));
        $log = AuthenticationLog::latest('id')->first();
        expect($log->logout_at)->toBeNull();

        event(new Logout('web', $user));

        expect($log->fresh()->logout_at)->not->toBeNull();
    });
});
