<?php
// app/DTOs/OrderItemUpdateDTO.php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class OrderItemUpdateDTO
{
    public readonly ?int $order_id;
    public readonly ?int $product_id;

    public function __construct(array $data, ?int $orderItemId = null, ?int $currentOrderId = null)
    {
        $this->validate($data, $orderItemId, $currentOrderId);

        $this->order_id   = isset($data['order_id']) ? (int) $data['order_id'] : null;
        $this->product_id = isset($data['product_id']) ? (int) $data['product_id'] : null;
    }

    public static function fromRequest(Request $request, ?int $orderItemId = null, ?int $currentOrderId = null): self
    {
        return new self($request->all(), $orderItemId, $currentOrderId);
    }

    public function toArray(): array
    {
        $out = [];
        if ($this->order_id   !== null) $out['order_id']   = $this->order_id;
        if ($this->product_id !== null) $out['product_id'] = $this->product_id;
        return $out;
    }

    private function validate(array $data, ?int $orderItemId, ?int $currentOrderId): void
    {
        $rules = [];
        $messages = [
            'order_id.exists'   => 'La orden seleccionada no existe.',
            'product_id.exists' => 'El producto seleccionado no existe.',
            'product_id.unique' => 'Este producto ya fue agregado a la orden.',
        ];

        if (array_key_exists('order_id', $data)) {
            $rules['order_id'] = ['integer','exists:orders,id'];
        }

        if (array_key_exists('product_id', $data)) {
            $rules['product_id'] = [
                'integer','exists:products,id',
                Rule::unique('order_items', 'product_id')
                    ->ignore($orderItemId)
                    ->where(fn($q) =>
                        $q->where('order_id', $data['order_id'] ?? $currentOrderId)
                    ),
            ];
        }

        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
