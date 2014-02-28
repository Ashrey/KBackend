#Bienvenido a KBackend#
El backend es el módulo de administración y configuración de una aplicación, a traves de él se gestionan las diferentes partes de la misma, desde el control de los usuarios de sistemas y sus privilegios, hasta llevar auditorias sobre las acciones que estos realizan.

##Instalación
1. Copiar el directorio **backend** a la carpeta principal de tu proyecto. Debe quedar algo así como esto:
   
   KumbiaPHPApps
   
   -core

   -bakend

   --app

   --public

   -kublog

   --app

   --public

   -kudoc

   --app

   --public

2. Incluir el archivo **backend/autoload.php** en el bootstarp de tu app
3. Disfrutar

##Para el login usar:
**Usuario:** root

**Contraseña:** admin

Este es el usuario de mayor privilegio. Todos los usuarios usan la misma clave.

##SQL
El código SQL para la base de datos se encuentra en **backend/app/config/backend.sql**

##Licencia
New BSD License, ver más detalles en **license.txt**
