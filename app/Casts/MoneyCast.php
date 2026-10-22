<?php

declare(strict_types=1);

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class MoneyCast implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  Model  $model
     * @param  mixed  $value
     * @param  array<string, mixed>  $attributes
     */
    public function get($model, string $key, $value, array $attributes): float
    {
        // Transforma o inteiro armazenado no banco de dados em um float.
        return round(floatval($value) / 100, precision: 2);
    }

    /**
     * Transform the attribute to its underlying model value.
     *
     * @param  Model  $model
     * @param  mixed  $value
     * @param  array<string, mixed>  $attributes
     */
    public function set($model, string $key, $value, array $attributes): float
    {
        // Transforma o float em um inteiro para armazenamento.
        return round(floatval($value) * 100);
    }
}
