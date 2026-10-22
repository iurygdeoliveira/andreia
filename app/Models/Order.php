<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\MoneyCast;
use App\Enums\OrderPaymentMethod;
use App\Enums\OrderStatus;
use App\Enums\PurchaseType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\RouteKey;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Pedido de compra de um curso pela landing publica (UC-04).
 *
 * O comprador chega anonimo: buyer_* guarda os dados informados no checkout,
 * e user_id so e preenchido quando o pagamento e confirmado (DEC-01).
 *
 * @property int $id
 * @property int $organization_id
 * @property int|null $user_id
 * @property int|null $lead_id
 * @property string $order_number
 * @property string $buyer_name
 * @property string $buyer_email
 * @property string $buyer_phone
 * @property string $buyer_document
 * @property float $total_amount
 * @property PurchaseType $purchase_type
 * @property OrderPaymentMethod $payment_method
 * @property OrderStatus $payment_status
 * @property string|null $asaas_customer_id
 * @property string|null $payment_id
 * @property string|null $payment_qr_code_image
 * @property string|null $payment_qr_code_payload
 * @property Carbon|null $payment_qr_code_expiration_date
 * @property Carbon|null $paid_at
 * @property bool $granted_new_user
 * @property Carbon|null $access_mail_queued_at
 * @property-read Organization $organization
 * @property-read User|null $user
 * @property-read Lead|null $lead
 * @property-read Collection<int, OrderItem> $items
 * @property-read Collection<int, Course> $courses
 */
#[Fillable([
    'organization_id',
    'user_id',
    'lead_id',
    'order_number',
    'buyer_name',
    'buyer_email',
    'buyer_phone',
    'buyer_document',
    'total_amount',
    'purchase_type',
    'payment_method',
    'payment_status',
    'asaas_customer_id',
    'payment_id',
    'payment_qr_code_image',
    'payment_qr_code_payload',
    'payment_qr_code_expiration_date',
    'paid_at',
    'granted_new_user',
    'access_mail_queued_at',
])]
#[RouteKey('order_number')]
class Order extends Model
{
    use HasFactory;

    #[\Override]
    protected function casts(): array
    {
        return [
            'total_amount' => MoneyCast::class,
            'purchase_type' => PurchaseType::class,
            'payment_method' => OrderPaymentMethod::class,
            'payment_status' => OrderStatus::class,
            'payment_qr_code_expiration_date' => 'datetime',
            'paid_at' => 'datetime',
            'granted_new_user' => 'boolean',
            'access_mail_queued_at' => 'datetime',
        ];
    }

    #[\Override]
    protected static function booted(): void
    {
        static::creating(function (Order $order): void {
            if (empty($order->order_number)) {
                $order->order_number = strtoupper((string) Str::ulid());
            }
        });
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Lead, $this>
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return BelongsToMany<Course, $this>
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'order_items')
            ->withPivot('unit_price')
            ->withTimestamps();
    }

    public function isPaid(): bool
    {
        return $this->payment_status->isPaid();
    }

    public function grantsAccess(): bool
    {
        return $this->payment_status->grantsAccess();
    }
}
