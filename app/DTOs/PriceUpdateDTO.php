<?php
// app/DTOs/PriceUpdateDTO.php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Status;

class PriceUpdateDTO
{
    public readonly ?string $name;
    public readonly ?int $product_id;
    public readonly ?string $description;
    public readonly ?int $quantity;
    public readonly ?float $price;
    public readonly ?int $discount_id;
    public readonly ?Status $status;

    public function __construct(array $data, int $priceId)
    {
        $this->validate($data, $priceId);

        $this->name        = isset($data['name']) ? trim($data['name']) : null;
        $this->product_id  = isset($data['product_id']) ? (int) $data['product_id'] : null;
        $this->description = isset($data['description']) ? trim($data['description']) : null;
        $this->quantity    = isset($data['quantity']) ? (int) $data['quantity'] : null;
        $this->price       = array_key_exists('price', $data) && $data['price'] !== null ? (float) $data['price'] : null;
        $this->discount_id = isset($data['discount_id']) ? (int) $data['discount_id'] : null;
        $this->status      = isset($data['status']) ? Status::from($data['status']) : null;
    }

    public static function fromRequest(Request $request, int $priceId): self
    {
        return new self($request->all(), $priceId);
    }

    public function toArray(): array
    {
        $out = [];

        if ($this->name !== null)        $out['name'] = $this->name;
        if ($this->product_id !== null)  $out['product_id'] = $this->product_id;
        if ($this->description !== null) $out['description'] = $this->description;
        if ($this->quantity !== null)    $out['quantity'] = $this->quantity;
        if ($this->price !== null)       $out['price'] = $this->price;
        if ($this->discount_id !== null) $out['discount_id'] = $this->discount_id;
        if ($this->status !== null)      $out['status'] = $this->status->value;

        return $out;
    }

    private function validate(array $data, $priceId): void
    {
        $rules = [];
        $messages = [
            'name.max'         => 'El nombre no puede exceder 100 caracteres',
            'product_id.exists'=> 'El producto seleccionado no existe',
            'description.string'=> 'La descripción debe ser texto',
            'quantity.integer' => 'La cantidad debe ser un número entero',
            'quantity.min'     => 'La cantidad debe ser al menos 1',
            'price.numeric'    => 'El precio debe ser un número',
            'price.min'        => 'El precio no puede ser negativo',
            'discount_id.exists'=> 'El descuento seleccionado no existe',
            'status.in'        => 'El estado debe ser: active, inactive o hidden',
        ];

        if (array_key_exists('name', $data)) {
            $rules['name'] = 'string|max:100';
        }
        if (array_key_exists('product_id', $data)) {
            $rules['product_id'] = 'integer|exists:products,id';
        }
        if (array_key_exists('description', $data)) {
            $rules['description'] = 'string';
        }
        if (array_key_exists('quantity', $data)) {
            $rules['quantity'] = 'integer|min:1';
        }
        if (array_key_exists('price', $data)) {
            $rules['price'] = 'numeric|min:0';
        }
        if (array_key_exists('discount_id', $data)) {
            $rules['discount_id'] = 'integer|exists:discounts,id';
        }
        if (array_key_exists('status', $data)) {
            $rules['status'] = 'in:active,inactive,hidden';
        }

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
