<?php
include "../config/config.php";
include "../config/utils.php";

include '../lib/password.php';

$dbConn = connect($db);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Content-Type: application/json');

$mensaje = array(
   "PASS" => ""
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

   // $input = file_get_contents('php://input');
   // $input = json_decode($input, true);

   $hash = password_hash('hola', PASSWORD_BCRYPT);
   // $mensaje['ESTADO'] = $hash;

   header("HTTP/1.1 200 OK");

   echo $hash;
   exit();

   // $input = file_get_contents('php://input');
   // $input = json_decode($input, true);

   // header("HTTP/1.1 200 OK");

   // if (password_verify('hola', $input['PASS'])) {
   //    echo 'Si';
   // } else {
   //    echo 'No';
   // }

}

header("HTTP/1.1 400 Bad Request");
