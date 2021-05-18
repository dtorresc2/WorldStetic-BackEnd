<?php
// Configuracion de encabezados
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');

// Agregando rutas
include "./routes/users.php";
// include "./config/config.php";
// include "./config/utils.php";
// $dbConn = connect($db);
// echo 'API - WES Admin';