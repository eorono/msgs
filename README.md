<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About MSGS

Es un app sencilla que emula el envio sencillo y masivo atraves de laravel

Incluye el paquete SAIL que hace que sea muy sencillo hacer un deploy en un entorno de desarrollo
Solo necesitas correr los siguientes comandos

docker run --rm \
-u "$(id -u):$(id -g)" \
-v "$(pwd):/var/www/html" \
-w /var/www/html \
laravelsail/php83-composer:latest \
composer install --ignore-platform-reqs

./vendor/bin/sail artisan migrate --seed (Ya esto si quieres datos de pruebas, si no eliminar el "--seed")

./vendor/bin/sail up

./vendor/bin/sail npm run dev


## Frontend

Utilice Breeze con Blade por su praticidad, se me hace mas facil (a mi personalmente) trabar con Blade para proyectos sencillos

## Backend

Maneje una carpeta Service donde estan los 4 servicios a donde se pueden enviar mensajes

La app guarda todos los mensajes en una tabla con su destrinatario y su status, hay un seeder que genera varios de manera aleatoria

Tambien guarda todos los mensajes en el log de laravel "storage/logs/laravel.log"

## Rutas
Son solo 3 rutas de interes para la app.

"/" que es donde esta el dashboard, para enviar los mensajes directamente

"/sent" muestra el historial de mensajes enviados

"/send" es la ruta POST que "envia" el mensaje




