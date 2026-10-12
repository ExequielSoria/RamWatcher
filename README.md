# Puesta en marcha #

Antes de levantar cualquier contenedor, es necesario crear una red de docker, ya que esta previamente especificada en los parametros de los contenedores con un nombre especifico.

Crear red docker "ramwatcher-network":

> docker network create ramwatcher-network

Luego se levanta uno de los 2 contenedores, el de desarrollo o el de produccion, la unica diferencia entre ambos es la imagen de phpmyadmin, que no se encuentra en el yml de produccion para no cargar innecesariamente el servidor, ya que se accede a mysql desde consola.

Es importante recordar que el contenedor de mysql viene con volumenes ya preconfigurados para crear la base de datos y los usuarios a la hora de construir el contenedor por primera vez, utilizando los datos especificados en el .ENV

Con el siguiente comando se construyen los contenedores por primera vez.
Con la flag -f se puede especificar el contenedor de desarrollo o el de produccion.

> sudo docker compose -f "docker-compose-dev.yml" up --build -d



API:
El funcionamiento de la api es bastante simple.

Todo comienza en el archivo .httaccess, que se encarga de redirigir todas las rutas aplicando ciertas reglas.
- Si detecta un "/api" lo redirige al archivo api.php y ahi lo gestiona.
- Si detecta un "/10" lo interpretamos como buscar el id de un producto. Asi que lo transformamos en la forma correcta y lo dirigimos al producto.
- Si no se cumplen estos casos, es decir, una ruta no esperada, se lo redirige al index. 

Una vez en el archivo "api.php", se carga el archivo "routes.php" para instanciar su clase y ejecutar el metodo "index", que se encarga de hacer las gestiones para dirigir al usuario al metodo concreto que esta solicitando.

Dentro de routes.php, se carga el controlador de la api, luego se analisa la url que pide el usuario para que mediante un simple SWITCH se determine el metodo a usar del controlador.

Los casos del switch son 2

- Si detecta ".../api/price/{id}" Muestra el producto individual y su historial completo de precios

- Si detecta ".../api/products" Muestra todos los productos guardados en la bdd, sin su historial de precios.

Luego, en el mismo caso del switch, se comprueba si especifico los precios, es decir, si detecta ".../api/products/prices" Muestra todos los productos con su ultimo precio registrado.

Ademas de imprimir los errores que pueda haber, en formato json.





.ENV de ejemplo, el mio es asi literal. Al realizar cambios en el .env el Docker debera reiniciarse 


PHP_PORT=8010 
MYSQL_PORT=8020 
PHPMYADMIN_PORT=8030

SQL_SERVER=app-db
PMA_HOST=app-db
MYSQL_HOST=app-db

MYSQL_USER=aku
MYSQL_PASSWORD=aku

MYSQL_ROOT_PASSWORD=root
DATABASE_NAME=ram_watcher

TZ=America/Argentina/Buenos_Aires




comando para darle persmisos al usuario de docker para que el crono funcione bien

sudo usermod -aG docker $USER



Comando para abrir el archivo de configuracion de cron (recomiendo usar nano)

crontab -e

Se pega esta linea abajo del todo para que se ejecute cada 24 hs
0 12 * * * docker exec ramwatcher-php-1 php /var/www/html/scripts/updater.php



Comando para añadir producto de Mexx.com (pronto api)
php addProductMexx.php "https://www.mexx.com.ar/productos-rubro/memorias-ram/40388-ejemplo.html"