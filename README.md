<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## BaldeCash API - Desafío

Esta API RESTful, desarrollada con Laravel, proporciona los servicios necesarios para la aplicación frontend de BaldeCash. Se encarga de la gestión de usuarios, autenticación, autorización y otras funcionalidades relacionadas con la prueba.

> **_NOTA:_** Este repositorio cuenta con un archivo docker para levantar la Base de datos previamente configurado el cual crea una DB llamada **testing**.

### Tecnologías
- Framework: Laravel 11
- Base de datos: MySQL
- Docker

### Instalación y Configuración

1. Clonar el repositorio

2. Instalar dependencias
	`composer install`

3. Levanta el contenedor Docker

    `docker compose up -d --build`

4. Crea un archivo .env en la raíz del proyecto y configura las variables de entorno necesarias, como la conexión a la base de datos, claves secretas, etc.

5. Ejecutar las migraciones

    `php artisan migrate`

6. ejecutamos los seeders

    `php artisan db:seed`

7. Iniciar el servidor de desarrollo

    `php artisan serve`