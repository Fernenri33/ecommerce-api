# Tankesv API Ecommerce

## Dependencias

- Laravel 12
- PHP 8.2
- Laravel Sanctum – autenticación para APIs y SPAs
- Laravel Tinker – consola interactiva
- Laravel Pint – formateo de código
- Laravel Sail – entorno Docker (opcional)
- FakerPHP / Mockery / PHPUnit – pruebas y generación de datos falsos
- Laravel Pail – logs en tiempo real

## Entorno local

### Base de datos

1. Crear una base de datos en MySql llamada ``ecommerce_tankesv``
2. Ingresar al archivo ``.env`` ubicado en ``tankesv-api\.env``
3. Cambiar este bloque de codigo por las credenciales MySql

``` php
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_tankesv
DB_USERNAME=root
DB_PASSWORD=
```

### Migraciones

Ir a la carpeta raíz del proyecto y ejecutar el siguiente comando

``` bash
php artisan migrate
```

Esto creará las tablas en la base de datos.

### Ejecución

Ir a la carpeta raíz del proyecto y ejecutar el siguiente comando

``` bash
php artisan serve
```

Esto abrirá el puerto local 8000
