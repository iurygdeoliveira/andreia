<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrderPaymentMethod;
use App\Enums\OrderStatus;
use App\Models\Course;
use App\Models\Order;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'user_id' => null,
            'lead_id' => null,
            'order_number' => strtoupper((string) Str::ulid()),
            'buyer_name' => fake()->name(),
            'buyer_email' => fake()->safeEmail(),
            'buyer_phone' => '88999998888',
            'buyer_document' => '52998224725',
            // MoneyCast recebe reais e grava centavos.
            'total_amount' => fake()->randomFloat(2, 10, 999),
            'payment_method' => OrderPaymentMethod::Pix,
            'payment_status' => OrderStatus::Pending,
            'asaas_customer_id' => 'cus_'.fake()->numerify('##########'),
            'payment_id' => 'pay_'.fake()->numerify('##########'),
            'payment_qr_code_image' => null,
            'payment_qr_code_payload' => null,
            'payment_qr_code_expiration_date' => now()->addMinutes(30),
            'paid_at' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Order $order): void {
            if ($order->items()->doesntExist()) {
                $course = Course::factory()->create([
                    'organization_id' => $order->organization_id,
                ]);

                $order->items()->create([
                    'course_id' => $course->getKey(),
                    'unit_price' => $course->price_in_cents,
                ]);
            }
        });
    }

    public function withCourses(Course ...$courses): static
    {
        return $this->afterCreating(function (Order $order) use ($courses): void {
            $courses = $courses ?: [Course::factory()->create(['organization_id' => $order->organization_id])];

            $order->items()->delete();

            foreach ($courses as $course) {
                $order->items()->create([
                    'course_id' => $course->getKey(),
                    'unit_price' => $course->price_in_cents,
                ]);
            }

            $order->update([
                'total_amount' => (float) collect($courses)->sum(fn (Course $c): float => (float) $c->price_in_cents),
            ]);
        });
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes): array => [
            'payment_status' => OrderStatus::Received,
            'paid_at' => now(),
        ]);
    }
}
