<?php
// app/DTOs/OrderUpdateDTO.php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class OrderUpdateDTO
{
    public readonly ?int $user_id;
    public readonly ?int $cart_id;

    public function __construct(array $data, ?int $orderId = null)
    {
        $this->validate($data, $orderId);

        $this->user_id = isset($data['user_id']) ? (int) $data['user_id'] : null;
        $this->cart_id = isset($data['cart_id']) ? (int) $data['cart_id'] : null;
    }

    public static function fromRequest(Request $request, ?int $orderId = null): self
    {
        return new self($request->all(), $orderId);
    }

    public function toArray(): array
    {
        $out = [];
        if ($this->user_id !== null) $out['user_id'] = $this->user_id;
        if ($this->cart_id !== null) $out['cart_id'] = $this->cart_id;
        return $out;
    }

    private function validate(array $data, ?int $orderId): void
    {
        $rules = [];
        $messages = [
            'user_id.exists' => 'El usuario no existe.',
            'cart_id.exists' => 'El carrito no existe.',
            'cart_id.unique' => 'Ese carrito ya tiene una orden asociada.',
        ];

        if (array_key_exists('user_id', $data)) {
            $rules['user_id'] = ['integer','exists:users,id'];
        }

        if (array_key_exists('cart_id', $data)) {
            $rules['cart_id'] = [
                'integer','exists:carts,id',
                // Mantén la unicidad de cart_id ignorando la orden actual
                Rule::unique('orders', 'cart_id')->ignore($orderId),
            ];
        }

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
