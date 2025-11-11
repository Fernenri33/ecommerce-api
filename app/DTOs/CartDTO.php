<?php
// app/DTOs/CartDTO.php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use App\Enums\CartStatus;

class CartDTO
{
    public readonly int $user_id;
    public readonly CartStatus $status;

    public function __construct(array $data)
    {
        $this->validate($data);

        $this->user_id = (int) $data['user_id'];
        $this->status  = CartStatus::from($data['status'] ?? 'active');
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
            'status'  => $this->status->value,
        ];
    }

    private function validate(array $data): void
    {
        $validator = Validator::make($data, [
            'user_id' => 'required|integer|exists:users,id',
            // ajusta a los valores de tu enum CartStatus
            'status'  => 'nullable|in:active,inactive,hidden',
        ], [
            'user_id.required' => 'El usuario es obligatorio',
            'user_id.exists'   => 'El usuario seleccionado no existe',
            'status.in'        => 'Estado inválido',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
