<?php

namespace App\DTOs;

class CartBulkUpdateDTO
{
    public function __construct(
        public readonly ?string $notes,
        /** @var array<int, array{item_id?:int, price_id:int, quantity:int, unit_price?:int}> */
        public readonly array $items = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            notes: $data['notes'] ?? null,
            items: $data['items'] ?? [],
        );
    }

    /** Campos editables del carrito (si existen en DB) */
    public function toCartArray(): array
    {
        return array_filter([
            'notes'    => $this->notes,
        ], fn($v) => !is_null($v));
    }
}
