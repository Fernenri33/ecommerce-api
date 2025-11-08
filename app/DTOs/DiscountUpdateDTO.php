<?php
// app/DTOs/DiscountUpdateDTO.php

namespace App\DTOs;

use App\Enums\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class DiscountUpdateDTO
{
    public readonly ?string $name;
    public readonly ?string $description;
    public readonly ?int $quantity;
    public readonly ?Status $status;

    public function __construct(array $data)
    {
        $this->validate($data);

        $this->name        = isset($data['name']) ? trim($data['name']) : null;
        $this->description = $data['description'] ?? null;
        $this->quantity    = isset($data['quantity']) ? (int) $data['quantity'] : null;
        $this->status      = isset($data['status']) ? Status::from($data['status']) : null;
    }

    public static function fromRequest(Request $request): self
    {
        return new self($request->all());
    }

    public function toArray(): array
    {
        $out = [];

        if ($this->name        !== null) $out['name']        = $this->name;
        if ($this->description !== null) $out['description'] = $this->description;
        if ($this->quantity    !== null) $out['quantity']    = $this->quantity;
        if ($this->status      !== null) $out['status']      = $this->status->value;

        return $out;
    }

    private function validate(array $data): void
    {
        $rules = [];
        $messages = [
            'name.max'         => 'El nombre no puede exceder 100 caracteres.',
            'description.max'  => 'La descripción no puede exceder 500 caracteres.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min'     => 'La cantidad debe ser al menos 1.',
            'status.in'        => 'El estado debe ser: active, inactive o hidden.',
        ];

        if (array_key_exists('name', $data))        $rules['name']        = 'string|max:100';
        if (array_key_exists('description', $data)) $rules['description'] = 'nullable|string|max:500';
        if (array_key_exists('quantity', $data))    $rules['quantity']    = 'integer|min:1';
        if (array_key_exists('status', $data))      $rules['status']      = 'in:active,inactive,hidden';

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
