<?php
// app/DTOs/OrderDTO.php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class OrderDTO
{
    public readonly int $user_id;
    public readonly int $cart_id;

    public function __construct(array $data)
    {
        $this->validate($data);

        $this->user_id = (int) $data['user_id'];
        $this->cart_id = (int) $data['cart_id'];
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
            'user_id' => $this->user_id,
            'cart_id' => $this->cart_id,
        ];
    }

    private function validate(array $data): void
    {
        $validator = Validator::make($data, [
            'user_id' => ['required','integer','exists:users,id'],
            'cart_id' => [
                'required','integer','exists:carts,id',
                // Evita que un carrito genere más de una orden:
                Rule::unique('orders', 'cart_id'),
            ],
        ], [
            'user_id.required' => 'El usuario es obligatorio.',
            'user_id.exists'   => 'El usuario no existe.',
            'cart_id.required' => 'El carrito es obligatorio.',
            'cart_id.exists'   => 'El carrito no existe.',
            'cart_id.unique'   => 'Ese carrito ya tiene una orden asociada.',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
