<?php
//Get Heroku ClearDB connection information
$cleardb_url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$cleardb_server = parse_url(getenv("SERVER"));
$cleardb_username = parse_url(getenv("USERNAME"));
$cleardb_password = parse_url(getenv("PASSWORD"));
$cleardb_db = parse_url(getenv("DB"));
// $cleardb_server = $cleardb_url["host"];
// $cleardb_username = $cleardb_url["user"];
// $cleardb_password = $cleardb_url["pass"];
// $cleardb_db = substr($cleardb_url["path"], 1);
$active_group = 'default';
$query_builder = TRUE;
echo 'Llegue aca '.$cleardb_server;
// Connect to DB
$conn = mysqli_connect($cleardb_server, $cleardb_username, $cleardb_password, $cleardb_db);

if (mysqli_connect_error()) {
   echo 'Error';
   die("Conexión a la base de datos fallo " . mysqli_connect_error() . mysqli_connect_errno());
}
else {
   echo 'Funciona';
}
