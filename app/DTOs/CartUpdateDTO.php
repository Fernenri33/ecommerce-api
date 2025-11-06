<?php
// app/DTOs/CartUpdateDTO.php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use CartStatus;

class CartUpdateDTO
{
    public readonly ?int $user_id;
    public readonly ?CartStatus $status;

    public function __construct(array $data)
    {
        $this->validate($data);

        $this->user_id = isset($data['user_id']) ? (int) $data['user_id'] : null;
        $this->status  = isset($data['status']) ? CartStatus::from($data['status']) : null;
    }

    public static function fromRequest(Request $request): self
    {
        return new self($request->all());
    }

    public function toArray(): array
    {
        $out = [];
        if ($this->user_id !== null) $out['user_id'] = $this->user_id;
        if ($this->status  !== null) $out['status']  = $this->status->value;
        return $out;
    }

    private function validate(array $data): void
    {
        $rules = [];
        $messages = [
            'user_id.exists' => 'El usuario seleccionado no existe',
            'status.in'      => 'Estado inválido',
        ];

        if (array_key_exists('user_id', $data)) {
            $rules['user_id'] = 'integer|exists:users,id';
        }
        if (array_key_exists('status', $data)) {
            // ajusta a los valores de tu enum CartStatus
            $rules['status']  = 'in:active,inactive,hidden';
        }

        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
