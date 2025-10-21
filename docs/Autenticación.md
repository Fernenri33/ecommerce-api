# Autenticación

## Credenciales necesarias

Cada usuario dispone de un correo electrónico y una contraseña.
Al iniciar sesión, el servidor genera un token de acceso (Bearer Token) mediante Laravel Sanctum.

Este token debe almacenarse de forma segura en el almacenamiento del navegador (por ejemplo, localStorage o sessionStorage) y enviarse en el header de autorización en todas las solicitudes HTTP posteriores.

## Registrarse

En el endpont público de registro será necesario que se confirme el correo en el body de la solicitud, además solo se pueden crear usuarios con rol de ``customer``.

```bash
https://tankesv.xyz/ecommerce/api/login
```

### Ejemplo de intento de registro

```js
fetch('https://tankesv.xyz/ecommerce/api/register', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    name: 'Juan',
    last_name: 'Pérez',
    email: `test@example.com`,
    password: 'Password123!',
    password_confirmation: 'Password123!'
  }),
})
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('Registro exitoso');
      console.log('Token:', data.content.token);
    } else {
      console.error('Error:', data.message);
    }
  })
  .catch(error => console.error('Error de red:', error));

```

### Creación de usuario exitosa

```json
{
  "success" : true,
  "message": "Usuario registrado exitosamente",
  "content"{
    "token": "1|etnEdF67SevCZ28PZisK2h7se8eriLSBG1dPdAbp5996e932",
    "token_type": "Bearer",
    "expires_in": 604800
  }
}
```

### Errores comunes

```json
{
  "success": false,
  "message": "Errores de validación"
  "errors"{
    "password"{
      "Las contraseñas no coinciden"
    }
  }
}
```

```json
{
  "success": false,
  "message": "Errores de validación"
  "errors"{
    "password"{
      "El email ya está registrado"
    }
  }
}
```

## Inicio de sesión

```bash
https://tankesv.xyz/ecommerce/api/login
```

### Ejemplo de intento de login

```js
fetch('https://tankesv.xyz/ecommerce/api/login', {
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

## Cerrar Sesión

Es necesario que luego de inicair sesión se guarde el Token en el navegador, con el token podrá acceder al resto de endpoins protegidos al incluír el token en el header de la consulta, el de cerrar sesión no es excepción.

```bash
https://tankesv.xyz/ecommerce/api/logout
```

```js
// ejemplo fetch
const token = localStorage.getItem('auth_token');

fetch('/api/logout', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${token}`
  },
  body: JSON.stringify({})
})
.then(res => {
  if (res.ok) {
    localStorage.removeItem('auth_token');
  }
})
.catch(console.error);
```

### Logout exitoso

```json
{
  "success": true,
  "message": "Logout exitoso"
}
```
