<?php
// app/DTOs/OrderItemDTO.php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class OrderItemDTO
{
    public readonly int $order_id;
    public readonly int $product_id;

    public function __construct(array $data)
    {
        $this->validate($data);

        $this->order_id   = (int) $data['order_id'];
        $this->product_id = (int) $data['product_id'];
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
            'order_id'   => $this->order_id,
            'product_id' => $this->product_id,
        ];
    }

    private function validate(array $data): void
    {
        $validator = Validator::make($data, [
            'order_id' => ['required','integer','exists:orders,id'],
            'product_id' => [
                'required','integer','exists:products,id',
                // Evita duplicados del mismo producto en la misma orden
                Rule::unique('order_items', 'product_id')
                    ->where(fn($q) => $q->where('order_id', $data['order_id'] ?? null)),
            ],
        ], [
            'order_id.required'  => 'La orden es obligatoria.',
            'order_id.exists'    => 'La orden seleccionada no existe.',
            'product_id.required'=> 'El producto es obligatorio.',
            'product_id.exists'  => 'El producto seleccionado no existe.',
            'product_id.unique'  => 'Este producto ya fue agregado a la orden.',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
