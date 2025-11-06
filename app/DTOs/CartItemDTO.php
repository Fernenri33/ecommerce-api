<?php
// app/DTOs/CartItemDTO.php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class CartItemDTO
{
    public readonly int $cart_id;
    public readonly int $price_id;

    public function __construct(array $data)
    {
        $this->validate($data);

        $this->cart_id  = (int) $data['cart_id'];
        $this->price_id = (int) $data['price_id'];
    }

    public static function fromRequest(Request $request): self
    {
        return new self($request->all());
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return [
            'cart_id'  => $this->cart_id,
            'price_id' => $this->price_id,
        ];
    }

    private function validate(array $data): void
    {
        $validator = Validator::make($data, [
            'cart_id'  => 'required|integer|exists:carts,id',
            'price_id' => [
                'required','integer','exists:prices,id',
                // Evitar duplicar el mismo price dentro del mismo cart (opcional pero recomendado)
                Rule::unique('cart_items')->where(fn($q) =>
                    $q->where('cart_id', $data['cart_id'] ?? null)
                      ->where('price_id', $data['price_id'] ?? null)
                ),
            ],
        ], [
            'cart_id.required'  => 'El carrito es obligatorio',
            'cart_id.exists'    => 'El carrito no existe',
            'price_id.required' => 'El precio es obligatorio',
            'price_id.exists'   => 'El precio no existe',
            'price_id.unique'   => 'Este producto/price ya está en el carrito',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
