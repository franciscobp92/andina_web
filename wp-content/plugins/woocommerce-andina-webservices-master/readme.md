# Woocommerce Andina Webservices
Contributors: @mvargaslandolfi, Github - @mvargaslandolfi1993
Requires at least: 4.5
Tested up to: 5.7.2
Requires PHP: 7.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

### Description

Este plugin permite la sincronización de productos con Andina Licores, validación de stock y la generación de ordenes y facturas.

### Requisitos para instalación

* Plugin [Woocommerce](https://es.wordpress.org/plugins/woocommerce/)
* Plugin [Advanced Cron Manager](https://es.wordpress.org/plugins/advanced-cron-manager/)

### Instalación manual

* El método de instalación manual conlleva descargar nuestro plugin y subirlo a tu servidor mediante tu aplicación favorita de FTP. El
codex de WordPress tiene  [instrucciones de cómo hacer esto](https://wordpress.org/support/article/managing-plugins/).

* También puedes descargar el archivo zip y subirlo directamente en la sección de Plugins -> Añadir plugin.

* En caso de realizar una actualización del plugin, deben descargar directamente el archivo .zip y volver a instalar el plugin manualmente.

### ¿Como funciona el plugin?

#### Paso 1

Se debe instalar y activar el plugin woocommerce, en caso de no hacerlo le generara un error indicando que debe tener previamente instalado este plugin.

#### Paso 2

Se debe instalar y activar el plugin Advanced Cron Manager, en caso de no hacerlo le generara un error indicando que debe tener previamente instalado este plugin.

#### Paso 3

Una vez instalado y activado el plugin Woocommerce Andina Webservices debe dirigirse a la pestaña:

WooCommerce -> Ajustes -> Webservices Andina.

**Opciones Generales:**

#### Configuración General

* **Habilitar/Deshabilitar** : En caso de habilitar se activara el plugin.

* **Notificación de errores:** Debe ingresar los correos electronicos a los cuales quiere que se le notifique los errores generados en la tienda

#### Configuración Endpoint 

##### URL

* **URL Webservice:** URL proporcionada por Andina Licores para hacer conexión a las webservices.

### Configuracion Cron Manager

Una vez realizada las previas configuraciones de las URL, debe dirigirse a: 
WooCommerce -> Herramientas -> Cron Manager.

Debe dar a click a boton Add New Event.

En el campo de texto **Hook** debe colocar lo siguiente:

* **al_webservices_synchronization_users**

En el campo **First execution**, la fecha donde desea que se realice la primera ejecución, si no selecciona ninguna fecha, entra en la cola de ejecuciones.

En el campo **Schedule**, debe colocar la frecuencia en la que desea que se ejecute el CRON, por ejemplo, cada hora, cada 2 horas, dos veces el día, etc.

Por ultimo debe dar click al boton **Add Event**.

En caso de que desee ejecutar el cron manualmente, en el listado debe buscar **al_webservices_synchronization_users** y hacer click en **Execute Now**.

#### Changelog

**1.0.0 - 2021-08-20**

* First Stable release