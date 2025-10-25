<?php
// app/DTOs/PriceDTO.php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use App\Enums\Status;

class PriceDTO
{
    public readonly string $name;
    public readonly int $product_id;
    public readonly string $description;
    public readonly int $quantity;
    public readonly float $price;
    public readonly int $discount_id;
    public readonly Status $status;

    public function __construct(array $data)
    {
        $this->validate($data);

        $this->name        = trim($data['name']);
        $this->product_id  = (int) $data['product_id'];
        $this->description = trim($data['description']);
        $this->quantity    = (int) $data['quantity'];
        $this->price       = (float) $data['price'];
        $this->discount_id = (int) $data['discount_id'];
        $this->status      = Status::from($data['status'] ?? 'active');
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
            'name'         => $this->name,
            'product_id'   => $this->product_id,
            'description'  => $this->description,
            'quantity'     => $this->quantity,
            'price'        => $this->price,
            'discount_id'  => $this->discount_id,
            'status'       => $this->status->value,
        ];
    }

    private function validate(array $data): void
    {
        $validator = Validator::make($data, [
            'name'        => 'required|string|max:100',
            'product_id'  => 'required|integer|exists:products,id',
            'description' => 'required|string',
            'quantity'    => 'required|integer|min:1',
            'price'       => 'required|numeric|min:0',
            'discount_id' => 'required|integer|exists:discounts,id',
            'status'      => 'nullable|in:active,inactive,hidden',
        ], [
            'name.required'        => 'El nombre es obligatorio',
            'name.max'             => 'El nombre no puede exceder 100 caracteres',
            'product_id.required'  => 'El producto es obligatorio',
            'product_id.exists'    => 'El producto seleccionado no existe',
            'description.required' => 'La descripción es obligatoria',
            'quantity.required'    => 'La cantidad es obligatoria',
            'quantity.min'         => 'La cantidad debe ser al menos 1',
            'price.required'       => 'El precio es obligatorio',
            'price.min'            => 'El precio no puede ser negativo',
            'discount_id.required' => 'El descuento es obligatorio',
            'discount_id.exists'   => 'El descuento seleccionado no existe',
            'status.in'            => 'El estado debe ser: active, inactive o hidden',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
