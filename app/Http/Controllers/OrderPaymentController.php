<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

final class OrderPaymentController
{
    public function show(string $pedido, CheckoutService $checkout): View
    {
        $order = Order::query()
            ->with(['items.course'])
            ->where('order_number', $pedido)
            ->firstOrFail();

        $checkout->recoverPixQrCode($order);

        $course = $order->items->first()?->course;

        return view('public.pages.pagamento', [
            'course' => $course,
            'order' => $order,
        ]);
    }

    public function status(string $pedido): JsonResponse
    {
        $order = Order::query()
            ->with(['items.course'])
            ->where('order_number', $pedido)
            ->firstOrFail();

        $course = $order->items->first()?->course;

        return response()->json([
            'status' => $order->payment_status->value,
            'pago' => $order->grantsAccess(),
            'redirect' => $order->grantsAccess()
                ? ($course ? route('curso.obrigado', $course) : route('home'))
                : null,
        ]);
    }
}
