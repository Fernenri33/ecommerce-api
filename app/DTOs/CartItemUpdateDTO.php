<?php
// app/DTOs/CartItemUpdateDTO.php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class CartItemUpdateDTO
{
    public readonly ?int $cart_id;
    public readonly ?int $price_id;

    public function __construct(array $data, ?int $cartItemId = null, ?int $currentCartId = null)
    {
        // Nota: si quieres validar unicidad también en update, pasa $cartItemId y el cart_id actual
        $this->validate($data, $cartItemId, $currentCartId);

        $this->cart_id  = isset($data['cart_id'])  ? (int) $data['cart_id']  : null;
        $this->price_id = isset($data['price_id']) ? (int) $data['price_id'] : null;
    }

    public static function fromRequest(Request $request, ?int $cartItemId = null, ?int $currentCartId = null): self
    {
        return new self($request->all(), $cartItemId, $currentCartId);
    }

    public function toArray(): array
    {
        $out = [];
        if ($this->cart_id  !== null) $out['cart_id']  = $this->cart_id;
        if ($this->price_id !== null) $out['price_id'] = $this->price_id;
        return $out;
    }

    private function validate(array $data, ?int $cartItemId, ?int $currentCartId): void
    {
        $rules = [];
        $messages = [
            'cart_id.exists'  => 'El carrito no existe',
            'price_id.exists' => 'El precio no existe',
            'price_id.unique' => 'Este producto/price ya está en el carrito',
        ];

        if (array_key_exists('cart_id', $data)) {
            $rules['cart_id'] = 'integer|exists:carts,id';
        }
        if (array_key_exists('price_id', $data)) {
            $rules['price_id'] = [
                'integer','exists:prices,id',
                // Unicidad compuesta (opcional): ignorando el propio registro
                Rule::unique('cart_items', 'price_id')
                    ->ignore($cartItemId)
                    ->where(fn($q) =>
                        $q->where('cart_id', $data['cart_id'] ?? $currentCartId)
                    ),
            ];
        }

        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
