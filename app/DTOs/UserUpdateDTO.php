<?php
// app/DTOs/UserUpdateDTO.php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class UserUpdateDTO
{
    public readonly ?string $name;
    public readonly ?string $last_name;
    public readonly ?string $email;
    public readonly ?string $password;
    public readonly ?int $rol_id;

    public function __construct(array $data, ?int $userId = null)
    {
        $this->validate($data, $userId);

        $this->name       = isset($data['name']) ? trim($data['name']) : null;
        $this->last_name  = isset($data['last_name']) ? trim($data['last_name']) : null;
        $this->email      = isset($data['email']) ? strtolower(trim($data['email'])) : null;
        $this->password   = $data['password'] ?? null;
        $this->rol_id     = $data['rol_id'] ?? null ? (int) $data['rol_id'] : null;
    }

    public static function fromRequest(Request $request, ?int $userId = null): self
    {
        return new self($request->all(), $userId);
    }

    public function toArray(): array
    {
        $out = [];
        if ($this->name       !== null) $out['name']       = $this->name;
        if ($this->last_name  !== null) $out['last_name']  = $this->last_name;
        if ($this->email      !== null) $out['email']      = $this->email;
        if ($this->password   !== null) $out['password']   = $this->password;
        if ($this->rol_id     !== null) $out['rol_id']     = $this->rol_id;
        return $out;
    }

    private function validate(array $data, ?int $userId): void
    {
        $rules = [];
        $messages = [
            'email.email'      => 'El correo no tiene un formato válido.',
            'email.unique'     => 'Ya existe un usuario con ese correo.',
            'password.min'     => 'La contraseña debe tener al menos 8 caracteres.',
            'rol_id.exists'    => 'El rol seleccionado no existe.',
        ];

        if (array_key_exists('name', $data))       $rules['name'] = 'string|max:100';
        if (array_key_exists('last_name', $data))  $rules['last_name'] = 'string|max:100';
        if (array_key_exists('email', $data)) {
            $rules['email'] = [
                'email','max:150',
                Rule::unique('users', 'email_hash')->ignore($userId),
            ];
        }
        if (array_key_exists('password', $data))   $rules['password'] = 'string|min:8';
        if (array_key_exists('rol_id', $data))     $rules['rol_id'] = 'integer|exists:roles,id';

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
