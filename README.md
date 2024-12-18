# EzexStore Backend

Backend API para el sistema EzexStore, un sistema multi-tenant para gestión de inventario.

## Requisitos

- PHP 8.1+
- Composer
- MySQL 5.7+
- Laravel 10.x

## Instalación Local

1. Clonar el repositorio
git clone <https://github.com/solucorp-ti/ezexstore-back.git>
cd ezexstore-back

2. Instalar dependencias
composer install

3. Configurar el archivo .env
cp .env.example .env

    Configurar la conexión a la base de datos:
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=ezexstore
    DB_USERNAME=root
    DB_PASSWORD=

4. Generar la clave de la aplicación
php artisan key:generate

5. Ejecutar las migraciones y seeders
php artisan migrate --seed

6. Crear una API Key para pruebas
php artisan api-key:create 1 1 --name="Test API Key" --scopes=products:read,products:write,inventory:read,inventory:write

## Desarrollo

El proyecto usa:

- Laravel 10
- Filament 3 para el panel de administración
- Multi-tenancy con API Keys para autenticación

## Comandos Útiles

Generar una nueva API Key:
php artisan api-key:create {tenant_id} {user_id} --name="Key Name" --scopes=*

Refrescar la base de datos (en desarrollo):
php artisan migrate:fresh --seed
