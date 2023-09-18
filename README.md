# Instrucciones para el despliegue de la aplicación Notifications Real-time con Laravel

Estas instrucciones te guiarán a través del proceso de despliegue de una aplicación Notifications Real-time utilizando Docker.

## Prerequisitos

Asegúrate de tener instalado lo siguiente en tu máquina:

- Docker: [Instrucciones de instalación de Docker](https://docs.docker.com/get-docker/)
- Docker Compose: [Instrucciones de instalación de Docker Compose](https://docs.docker.com/compose/install/)

## Pasos de despliegue

1. Agrega la siguiente línea a tu archivo `hosts` para configurar el dominio local:

```bash
127.0.0.1 notifications.local
```


2. Ejecuta el siguiente comando para copiar el archivo de configuracion ejemplo y luego configura el '.env':

```bash
cp .env.example .env
```


3. Ejecuta el siguiente comando para levantar los contenedores Docker:

```bash
docker-compose up -d
```


4. Accede al contenedor del mysql para crear las bases de datos:

```bash
docker exec -it database sh
mysql -u root -p
CREATE DATABASE cintelink;
CREATE DATABASE cintelink_test;
exit
exit
```


5. Accede al contenedor de la aplicación e ingresa a la raiz del proyecto:

```bash
docker exec -it notifications sh
cd notifications/
```


6. Dentro del contenedor, instala las dependencias de Composer:

```bash
composer install
```


7. A continuación, instala las dependencias de npm:

```bash
npm install
```


8. Compila los activos de la aplicación:

```bash
npm run build
```


9. Ejecuta las migraciones de la base de datos para crear las tablas:

```bash
php artisan migrate
```


10. Ejecuta el siguiente comando, para cargar datos de inicio:

```bash
php artisan db:seed
```


11. Ahora, puedes acceder a tu aplicación en el siguiente enlace:

```bash
http://notifications.local/
```


¡Tu aplicación Laravel está ahora desplegada y lista para su uso!

## Notas adicionales
Asegúrate de que los archivos de configuración de Notifications Real-time, como .env, estén correctamente configurados para tu entorno.
Puedes personalizar la configuración de Docker en el archivo docker-compose.yml según tus necesidades.
