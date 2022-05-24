<?php
/**
 * Un simple archivo que configura el log, oculta
 * los errores y crea un nuevo archivo cada día
 *
 */

# El directorio o carpeta en donde se van a crear los logs
define("RUTA_LOGS", __DIR__ . "/logs");

# Crear carpeta si no existe
if (!file_exists(RUTA_LOGS)) {
    mkdir(RUTA_LOGS);
}

# Configuramos el ini para que...
# No muestre errores
ini_set('display_errors', 0);
# Los ponga en un archivo
ini_set("log_errors", 1);

# Y le indicamos en dónde los va a poner, sería en algo como:
# RUTA_LOGS/2019-02-07.log

# Así cada día tenemos un archivo de log distinto
ini_set("error_log", RUTA_LOGS . "/" . date("Y-m-d") . ".log");

?>