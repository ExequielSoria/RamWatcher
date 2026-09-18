# Puesta en marcha #

Antes de levantar cualquier contenedor, es necesario crear una red de docker, ya que esta previamente especificada en los parametros de los contenedores con un nombre especifico.

Crear red docker "ramwatcher-network":

> docker network create ramwatcher-network

Luego se levanta uno de los 2 contenedores, el de desarrollo o el de produccion, la unica diferencia entre ambos es la imagen de phpmyadmin, que no se encuentra en el yml de produccion para no sobrecargar el servidor, ya que accedo a mysql desde consola.

Es importante recordar que el contenedor de mysql viene con volumenes ya preconfigurados para crear la base de datos y los usuarios a la hora de construir el contenedor por primera vez.

Construye los contenedores por primera vez, creando la bdd y el usuario especificado en el .ENV

> sudo docker compose -f "docker-compose-dev.yml" up --build -d


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