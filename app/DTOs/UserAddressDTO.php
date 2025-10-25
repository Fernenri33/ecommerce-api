<?php
// app/DTOs/UserAddressDTO.php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class UserAddressDTO
{
    public readonly int $user_id;
    public readonly string $address;
    public readonly string $city;
    public readonly string $state;
    public readonly string $zip_code;
    public readonly string $country;
    public readonly bool $is_default;

    public function __construct(array $data)
    {
        $this->validate($data);

        $this->user_id    = (int) $data['user_id'];
        $this->address    = trim($data['address']);
        $this->city       = trim($data['city']);
        $this->state      = trim($data['state']);
        $this->zip_code   = trim($data['zip_code']);
        $this->country    = trim($data['country']);
        $this->is_default = filter_var($data['is_default'] ?? false, FILTER_VALIDATE_BOOLEAN);
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
            'user_id'    => $this->user_id,
            'address'    => $this->address,
            'city'       => $this->city,
            'state'      => $this->state,
            'zip_code'   => $this->zip_code,
            'country'    => $this->country,
            'is_default' => $this->is_default,
        ];
    }

    private function validate(array $data): void
    {
        $validator = Validator::make($data, [
            'user_id'    => 'required|integer|exists:users,id',
            'address'    => 'required|string|max:255',
            'city'       => 'required|string|max:100',
            'state'      => 'required|string|max:100',
            'zip_code'   => 'required|string|max:20',
            'country'    => 'required|string|max:100',
            'is_default' => 'nullable|boolean',
        ], [
            'user_id.required'    => 'El usuario es obligatorio.',
            'user_id.exists'      => 'El usuario no existe.',
            'address.required'    => 'La dirección es obligatoria.',
            'city.required'       => 'La ciudad es obligatoria.',
            'state.required'      => 'El estado o provincia es obligatorio.',
            'zip_code.required'   => 'El código postal es obligatorio.',
            'country.required'    => 'El país es obligatorio.',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
