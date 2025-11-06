# Autenticación

Esta guía resume los endpoints de autenticación que estás testeando en **Laravel** (con **Sanctum**). Incluye ejemplos de uso en **JavaScript (fetch)** y respuestas **JSON**

> **Formato**: todos los endpoints son **POST** y aceptan/retornan **JSON**.

---

## Base URL

Define una constante para tu API (ajústala a tu entorno):

```js
const BASE_URL = 'https://tankesv.xyz/ecommerce/api'; // cámbialo si usas otro host
```

---

## Autorización

* Tras un **login/registro** exitoso, recibirás un **Bearer Token**.
* Inclúyelo en el header `Authorization` para endpoints protegidos (p. ej., **logout**):

```
Authorization: Bearer <TOKEN>
```

> El token caduca en **604800** segundos (7 días).

---

## Resumen de Endpoints

| Acción           | Método | Ruta        | Auth |
| ---------------- | ------ | ----------- | ---- |
| Registro         | POST   | `/register` | ❌    |
| Inicio de sesión | POST   | `/login`    | ❌    |
| Cerrar sesión    | POST   | `/logout`   | ✅    |

---

## 1) Registro — `POST /register`

Crea un usuario (rol **customer**).

### Body (JSON)

```json
{
  "name": "Juan",
  "last_name": "Pérez",
  "email": "test@example.com",
  "password": "Password123!",
  "password_confirmation": "Password123!"
}
```

### Ejemplo JS (fetch)

```js
fetch(`${BASE_URL}/register`, {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    name: 'Juan',
    last_name: 'Pérez',
    email: 'test@example.com',
    password: 'Password123!',
    password_confirmation: 'Password123!'
  })
})
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      localStorage.setItem('auth_token', data.content.token);
      console.log('Registro exitoso');
    } else {
      console.error('Error:', data.message, data.errors || {});
    }
  })
  .catch(console.error);
```

### Respuestas

**Éxito**

```json
{
  "success": true,
  "message": "Usuario registrado exitosamente",
  "content": {
    "token": "1|jmWI6F5mRijszw6yVlAbjor9oQ0dBOLFYVNgXQBa1d8e7857",
    "token_type": "Bearer",
    "expires_in": 604800
  }
}
```

**Errores de validación (no coinciden contraseñas)**

```json
{
  "success": false,
  "message": "Errores de validación",
  "errors": {
    "password": [
      "Las contraseñas no coinciden"
    ]
  }
}
```

**Errores de validación (email duplicado)**

```json
{
  "success": false,
  "message": "Errores de validación",
  "errors": {
    "email": [
      "El email ya está registrado"
    ]
  }
}
```

---

## 2) Inicio de sesión — `POST /login`

Autentica un usuario y retorna token.

### Body (JSON)

```json
{
  "email": "test@example.com",
  "password": "Password123!"
}
```

### Ejemplo JS (fetch)

```js
fetch(`${BASE_URL}/login`, {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    email: 'test@example.com',
    password: 'Password123!'
  })
})
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      localStorage.setItem('auth_token', data.content.token);
      console.log('Login exitoso');
    } else {
      console.error('Error:', data.message);
    }
  })
  .catch(console.error);
```

### Respuestas

**Éxito**

```json
{
  "success": true,
  "message": "Login exitoso",
  "content": {
    "token": "1|nRPUNypWn3lCERQbaRPYhIWCTAklIOTtdcuq94y6e1eadb4c",
    "token_type": "Bearer",
    "expires_in": 604800
  }
}
```

**Credenciales incorrectas**

```json
{
  "success": false,
  "message": "Credenciales incorrectas"
}
```

---

## 3) Cerrar sesión — `POST /logout`

Invalida el token activo. **Requiere Auth**.

### Headers

```
Authorization: Bearer <TOKEN>
```

### Body (JSON)

```json
{}
```

### Ejemplo JS (fetch)

```js
const token = localStorage.getItem('auth_token');

fetch(`${BASE_URL}/logout`, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${token}`
  },
  body: JSON.stringify({})
})
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      localStorage.removeItem('auth_token');
      console.log('Logout exitoso');
    } else {
      console.error('Error de logout:', data.message);
    }
  })
  .catch(console.error);
```

### Respuesta

```json
{
  "success": true,
  "message": "Logout exitoso"
}
```

---

## Manejo del Token

* Guarda el token en `localStorage` o `sessionStorage`.
* Envía el header `Authorization: Bearer <TOKEN>` en endpoints protegidos.
* Valida expiración (`expires_in: 604800` segundos).

---

## Respuestas de ejemplo (copiar y pegar)

### Registro — éxito

```json
{
  "success": true,
  "message": "Usuario registrado exitosamente",
  "content": {
    "token": "1|jmWI6F5mRijszw6yVlAbjor9oQ0dBOLFYVNgXQBa1d8e7857",
    "token_type": "Bearer",
    "expires_in": 604800
  }
}
```

### Registro — error (password_confirmation)

```json
{
  "success": false,
  "message": "Errores de validación",
  "errors": {
    "password": [
      "Las contraseñas no coinciden"
    ]
  }
}
```

### Registro — error (email duplicado)

```json
{
  "success": false,
  "message": "Errores de validación",
  "errors": {
    "email": [
      "El email ya está registrado"
    ]
  }
}
```

### Login — éxito

```json
{
  "success": true,
  "message": "Login exitoso",
  "content": {
    "token": "1|nRPUNypWn3lCERQbaRPYhIWCTAklIOTtdcuq94y6e1eadb4c",
    "token_type": "Bearer",
    "expires_in": 604800
  }
}
```

### Login — error

```json
{
  "success": false,
  "message": "Credenciales incorrectas"
}
```

### Logout — éxito

```json
{
  "success": true,
  "message": "Logout exitoso"
}
```
