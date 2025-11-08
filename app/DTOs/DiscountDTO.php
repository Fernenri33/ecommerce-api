<?php
// app/DTOs/DiscountDTO.php

namespace App\DTOs;

use App\Enums\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class DiscountDTO
{
    public readonly string $name;
    public readonly ?string $description;
    public readonly int $quantity;
    public readonly Status $status;

    public function __construct(array $data)
    {
        $this->validate($data);

        $this->name        = trim($data['name']);
        $this->description = $data['description'] ?? null;
        $this->quantity    = (int) $data['quantity'];
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
            'name'        => $this->name,
            'description' => $this->description,
            'quantity'    => $this->quantity,
            'status'      => $this->status->value,
        ];
    }

    private function validate(array $data): void
    {
        $validator = Validator::make($data, [
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'quantity'    => 'required|integer|min:1',
            'status'      => 'nullable|in:active,inactive,hidden',
        ], [
            'name.required'        => 'El nombre es obligatorio.',
            'name.max'             => 'El nombre no puede exceder 100 caracteres.',
            'description.max'      => 'La descripción no puede exceder 500 caracteres.',
            'quantity.required'    => 'La cantidad es obligatoria.',
            'quantity.integer'     => 'La cantidad debe ser un número entero.',
            'quantity.min'         => 'La cantidad debe ser al menos 1.',
            'status.in'            => 'El estado debe ser: active, inactive o hidden.',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
