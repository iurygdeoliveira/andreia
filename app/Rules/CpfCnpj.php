<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valida CPF ou CNPJ pelos digitos verificadores.
 *
 * Conferir o digito aqui evita que um documento invalido so seja recusado la
 * na frente pelo Asaas: como falha de gateway vira mensagem generica
 * (DEC-09), o comprador nao descobriria qual campo esta errado.
 */
class CpfCnpj implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digits = preg_replace('/\D/', '', (string) $value) ?? '';

        $valid = match (strlen($digits)) {
            11 => $this->isValidCpf($digits),
            14 => $this->isValidCnpj($digits),
            default => false,
        };

        if (! $valid) {
            $fail('Informe um CPF ou CNPJ válido.');
        }
    }

    private function isValidCpf(string $cpf): bool
    {
        // 111.111.111-11 e afins passam no calculo do digito, mas nao existem.
        if (preg_match('/^(\d)\1{10}$/', $cpf) === 1) {
            return false;
        }

        foreach ([9, 10] as $position) {
            $sum = 0;

            for ($i = 0; $i < $position; $i++) {
                $sum += (int) $cpf[$i] * (($position + 1) - $i);
            }

            $remainder = ($sum * 10) % 11;
            $digit = $remainder === 10 ? 0 : $remainder;

            if ($digit !== (int) $cpf[$position]) {
                return false;
            }
        }

        return true;
    }

    private function isValidCnpj(string $cnpj): bool
    {
        if (preg_match('/^(\d)\1{13}$/', $cnpj) === 1) {
            return false;
        }

        foreach ([12, 13] as $position) {
            $sum = 0;
            $weight = $position - 7;

            for ($i = 0; $i < $position; $i++) {
                $sum += (int) $cnpj[$i] * $weight;
                $weight = $weight === 2 ? 9 : $weight - 1;
            }

            $remainder = $sum % 11;
            $digit = $remainder < 2 ? 0 : 11 - $remainder;

            if ($digit !== (int) $cnpj[$position]) {
                return false;
            }
        }

        return true;
    }
}
