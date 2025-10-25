<?php
// app/DTOs/UserAddressUpdateDTO.php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class UserAddressUpdateDTO
{
    public readonly ?int $user_id;
    public readonly ?string $address;
    public readonly ?string $city;
    public readonly ?string $state;
    public readonly ?string $zip_code;
    public readonly ?string $country;
    public readonly ?bool $is_default;

    public function __construct(array $data)
    {
        $this->validate($data);

        $this->user_id    = isset($data['user_id']) ? (int) $data['user_id'] : null;
        $this->address    = isset($data['address']) ? trim($data['address']) : null;
        $this->city       = isset($data['city']) ? trim($data['city']) : null;
        $this->state      = isset($data['state']) ? trim($data['state']) : null;
        $this->zip_code   = isset($data['zip_code']) ? trim($data['zip_code']) : null;
        $this->country    = isset($data['country']) ? trim($data['country']) : null;
        $this->is_default = array_key_exists('is_default', $data)
            ? filter_var($data['is_default'], FILTER_VALIDATE_BOOLEAN)
            : null;
    }

    public static function fromRequest(Request $request): self
    {
        return new self($request->all());
    }

    public function toArray(): array
    {
        $out = [];

        if ($this->user_id    !== null) $out['user_id']    = $this->user_id;
        if ($this->address    !== null) $out['address']    = $this->address;
        if ($this->city       !== null) $out['city']       = $this->city;
        if ($this->state      !== null) $out['state']      = $this->state;
        if ($this->zip_code   !== null) $out['zip_code']   = $this->zip_code;
        if ($this->country    !== null) $out['country']    = $this->country;
        if ($this->is_default !== null) $out['is_default'] = $this->is_default;

        return $out;
    }

    private function validate(array $data): void
    {
        $rules = [];
        $messages = [
            'user_id.exists' => 'El usuario no existe.',
        ];

        if (array_key_exists('user_id', $data))    $rules['user_id']    = 'integer|exists:users,id';
        if (array_key_exists('address', $data))    $rules['address']    = 'string|max:255';
        if (array_key_exists('city', $data))       $rules['city']       = 'string|max:100';
        if (array_key_exists('state', $data))      $rules['state']      = 'string|max:100';
        if (array_key_exists('zip_code', $data))   $rules['zip_code']   = 'string|max:20';
        if (array_key_exists('country', $data))    $rules['country']    = 'string|max:100';
        if (array_key_exists('is_default', $data)) $rules['is_default'] = 'boolean';

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
