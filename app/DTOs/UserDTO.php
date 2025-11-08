<?php
// app/DTOs/UserDTO.php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class UserDTO
{
    public readonly string $name;
    public readonly string $last_name;
    public readonly string $email;
    public readonly ?string $password;
    public readonly ?int $rol_id;

    public function __construct(array $data)
    {
        $this->validate($data);

        $this->name       = trim($data['name']);
        $this->last_name  = trim($data['last_name']);
        $this->email      = strtolower(trim($data['email']));
        $this->password   = $data['password'] ?? null;
        $this->rol_id     = $data['rol_id'] ?? null ? (int) $data['rol_id'] : null;
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
            'name'      => $this->name,
            'last_name' => $this->last_name,
            'email'     => $this->email,
            'password'  => $this->password,
            'rol_id'    => $this->rol_id,
        ];
    }

    private function validate(array $data): void
    {
        $validator = Validator::make($data, [
            'name'       => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => [
                'required', 'email', 'max:150',
                Rule::unique('users', 'email_hash') // valida usando email_hash (ya que email está cifrado)
            ],
            'password'   => 'required|string|min:8',
            'rol_id'     => 'nullable|integer|exists:roles,id',
        ], [
            'name.required'       => 'El nombre es obligatorio.',
            'last_name.required'  => 'El apellido es obligatorio.',
            'email.required'      => 'El correo es obligatorio.',
            'email.email'         => 'El correo no tiene un formato válido.',
            'email.unique'        => 'Ya existe un usuario con ese correo.',
            'password.required'   => 'La contraseña es obligatoria.',
            'password.min'        => 'La contraseña debe tener al menos 8 caracteres.',
            'rol_id.exists'       => 'El rol seleccionado no existe.',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
