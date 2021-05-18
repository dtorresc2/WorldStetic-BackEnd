<?php
//Get Heroku ClearDB connection information
$cleardb_url = parse_url(getenv("SERVER"));
// $cleardb_server = $cleardb_url["SERVER"];
// $cleardb_username = $cleardb_url["USERNAME"];
// $cleardb_password = $cleardb_url["PASSWORD"];
// $cleardb_db = substr($cleardb_url["DB"], 1);

$cleardb_server = 'us-cdbr-east-03.cleardb.com';
$cleardb_username = 'b9b0e55ea4f5df';
$cleardb_password = '90054949';
$cleardb_db = 'heroku_ae29aed98b86fc3';

$active_group = 'default';
$query_builder = TRUE;

echo $cleardb_url;

// Connect to DB
$conn = mysqli_connect($cleardb_server, $cleardb_username, $cleardb_password, $cleardb_db);

if (mysqli_connect_error()) {
   die("Conexión a la base de datos fallo " . mysqli_connect_error() . mysqli_connect_errno());
}
else {
   echo 'Funciona';
}
