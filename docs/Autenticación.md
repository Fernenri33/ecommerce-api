# Autenticación

## Credenciales necesarias

Cada usuario dispone de un correo electrónico y una contraseña.
Al iniciar sesión, el servidor genera un token de acceso (Bearer Token) mediante Laravel Sanctum.

Este token debe almacenarse de forma segura en el almacenamiento del navegador (por ejemplo, localStorage o sessionStorage) y enviarse en el header de autorización en todas las solicitudes HTTP posteriores.

## Inicio de sesión

### Endpoint

```bash
https://tankesv/ecommerce/api/login
```

### Ejemplo de intento de login

```js
fetch('https://tankesv/ecommerce/api/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    email: 'test@ejemplo.com',
    password: 'password123'
  }),
})
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('Login exitoso');
      console.log('Token:', data.content.token);
    } else {
      console.error('Error:', data.message);
    }
  })
  .catch(error => console.error('Error de red:', error));

```

### Login exitoso

```json
{
  "success": true,
  "message": "Login exitoso",
  "content": {
    "token": "1|DBxVJB95cSpaZ3CAlrrt7MYemA6NzUfAPxb6MwOb32d6f10c",
    "token_type": "Bearer",
    "expires_in": 604800
  }
}
```

### Credenciales incorrectas

```json
{
  "success": false,
  "message": "Credenciales incorrectas"
}
```

## Signup

## Registrarse
