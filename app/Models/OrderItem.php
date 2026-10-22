<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\MoneyCast;
use Database\Factories\OrderItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Item de pedido representando a compra de uma matrícula em um curso (ADR-0003).
 *
 * @property int $id
 * @property int $order_id
 * @property int $course_id
 * @property float $unit_price
 * @property-read Order $order
 * @property-read Course $course
 */
#[Fillable([
    'order_id',
    'course_id',
    'unit_price',
])]
class OrderItem extends Model
{
    /** @use HasFactory<OrderItemFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    #[\Override]
    protected function casts(): array
    {
        return [
            'unit_price' => MoneyCast::class,
        ];
    }
}
