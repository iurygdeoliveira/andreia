<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CourseStatus;
use App\Enums\PurchaseType;
use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;
use LukePOLO\LaraCart\CartItem;
use LukePOLO\LaraCart\LaraCart;

class CartService
{
    public function __construct(
        private readonly LaraCart $laraCart,
    ) {}

    /**
     * @return array<string, array{
     *     id: int,
     *     hash: string,
     *     title: string,
     *     description: string,
     *     price: float,
     *     qty: int,
     *     cover_url: string|null,
     *     checkout_code: string|null,
     *     purchase_type: string|null,
     * }>
     */
    public function getItems(): array
    {
        $items = [];

        /** @var CartItem $item */
        foreach ($this->laraCart->getItems() as $hash => $item) {
            $items[(string) $hash] = [
                'id' => (int) ($item->options['id'] ?? 0),
                'hash' => (string) $hash,
                'title' => (string) ($item->options['title'] ?? $item->options['name'] ?? ''),
                'description' => (string) ($item->options['description'] ?? ''),
                'price' => (float) ($item->options['price'] ?? 0.0),
                'qty' => (int) ($item->options['qty'] ?? 1),
                'cover_url' => $item->options['cover_url'] ?? null,
                'checkout_code' => $item->options['checkout_code'] ?? null,
                'purchase_type' => isset($item->options['purchase_type']) && is_string($item->options['purchase_type'])
                    ? $item->options['purchase_type']
                    : null,
            ];
        }

        return $items;
    }

    /**
     * Resolve os itens do carrinho em cursos publicados, preservando a ordem
     * de exibição. Retorna somente o que ainda está à venda (RN-02): curso
     * despublicado depois de adicionado não segue para o checkout.
     *
     * @return Collection<int, Course>
     */
    public function getCourses(): Collection
    {
        $ids = collect($this->getItems())->pluck('id')->filter()->values();

        if ($ids->isEmpty()) {
            return new Collection;
        }

        $courses = Course::query()
            ->where('status', CourseStatus::Published)
            ->whereKey($ids)
            ->get()
            ->keyBy(fn (Course $course): int => (int) $course->getKey());

        // whereKey não preserva a ordem do carrinho; reordenar pela sequência
        // em que o comprador montou.
        $ordered = $ids
            ->map(fn (int|string $id): ?Course => $courses->get((int) $id))
            ->filter()
            ->values();

        return new Collection($ordered->all());
    }

    public function add(Course $course, int $qty = 1, ?PurchaseType $purchaseType = null): void
    {
        $courseId = (int) $course->getKey();

        if ($this->has($courseId)) {
            if (! $purchaseType instanceof PurchaseType) {
                return;
            }

            $existingPurchaseType = $this->getPurchaseTypeForCourse($courseId);
            if ($existingPurchaseType === $purchaseType) {
                return;
            }

            $this->remove($courseId);
        }

        $price = $purchaseType instanceof PurchaseType
            ? $purchaseType->price()
            : (float) $course->price_in_cents;

        $title = $purchaseType instanceof PurchaseType
            ? "{$course->title} — {$purchaseType->getShortLabel()}"
            : (string) $course->title;

        $description = $purchaseType instanceof PurchaseType
            ? $purchaseType->description()
            : (string) ($course->description ?: 'Capacitação completa com certificado aceito pelo MEC.');

        $this->laraCart->add(
            $courseId,
            /** @phpstan-ignore argument.type */
            $title,
            $qty,
            (string) $price,
            [
                'id' => $courseId,
                'title' => $title,
                'description' => $description,
                'cover_url' => $course->cover_url,
                'checkout_code' => $course->checkout_code,
                'purchase_type' => $purchaseType?->value,
            ]
        );
    }

    public function getPurchaseTypeForCourse(Course|int $course): ?PurchaseType
    {
        $targetId = $course instanceof Course ? (int) $course->getKey() : (int) $course;

        foreach ($this->getItems() as $item) {
            if ($item['id'] === $targetId && is_string($item['purchase_type'])) {
                return PurchaseType::tryFrom($item['purchase_type']);
            }
        }

        return null;
    }

    public function updateQty(string|int $idOrHash, int $qty): void
    {
        $hash = $this->resolveHash($idOrHash);

        if ($hash === null) {
            return;
        }

        if ($qty < 1) {
            $this->remove($hash);

            return;
        }

        $this->laraCart->updateItem($hash, 'qty', $qty);
    }

    public function remove(string|int $idOrHash): void
    {
        $hash = $this->resolveHash($idOrHash);

        if ($hash !== null) {
            $this->laraCart->removeItem($hash);
        }
    }

    public function has(Course|int|string $course): bool
    {
        $target = $course instanceof Course ? (string) $course->getKey() : (string) $course;

        /** @var CartItem $item */
        foreach ($this->laraCart->getItems() as $hash => $item) {
            $itemId = (string) ($item->options['id'] ?? '');
            if ((string) $hash === $target || $itemId === $target) {
                return true;
            }
        }

        return false;
    }

    public function count(): int
    {
        return count($this->laraCart->getItems());
    }

    public function totalQuantity(): int
    {
        $total = 0;

        /** @var CartItem $item */
        foreach ($this->laraCart->getItems() as $item) {
            $total += (int) ($item->options['qty'] ?? 1);
        }

        return $total;
    }

    public function total(): float
    {
        return round((float) $this->laraCart->total(format: false), 2);
    }

    public function totalInCents(): int
    {
        return (int) round($this->total() * 100);
    }

    public function isEmpty(): bool
    {
        return empty($this->laraCart->getItems());
    }

    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    public function clear(): void
    {
        $this->laraCart->destroyCart();
    }

    public function getLaraCart(): LaraCart
    {
        return $this->laraCart;
    }

    private function resolveHash(string|int $idOrHash): ?string
    {
        $target = (string) $idOrHash;
        $items = $this->laraCart->getItems();

        if (isset($items[$target])) {
            return $target;
        }

        /** @var CartItem $item */
        foreach ($items as $hash => $item) {
            $itemId = (string) ($item->options['id'] ?? '');
            if ($itemId === $target) {
                return (string) $hash;
            }
        }

        return null;
    }
}
