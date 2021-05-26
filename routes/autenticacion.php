<?php
include "../config/config.php";
include "../config/utils.php";

include '../lib/password.php';

$dbConn = connect($db);

$mensaje = array(
   "ESTADO" => "",
   "MENSAJE" => "",
   "ID" => 0
);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $input = file_get_contents('php://input');
   $input = json_decode($input, true);

   $sql = $dbConn->prepare(
      "SELECT " .
         "id_usuario AS ID_USUARIO, " .
         "usuario AS USUARIO, " .
         "password AS PASSWORD, " .
         "estado AS ESTADO, " .
         "admin AS ADMIN " .
         "FROM usuarios " .
         "WHERE usuario=:USUARIO"
   );
   $sql->bindValue(':USUARIO', $input['USUARIO']);
   $sql->execute();

   header("HTTP/1.1 200 OK");
   $filas = $sql->fetch(PDO::FETCH_ASSOC);

   if ($sql->rowCount() > 0) {
      if (password_verify($input['PASSWORD'], $filas['PASSWORD'])) {
         $mensaje['ESTADO'] = 1;
         $mensaje['MENSAJE'] = "Usuario Autenticado";
         $mensaje['ID'] = (int)$filas['ID_USUARIO'];
      } else {
         $mensaje['ESTADO'] = 0;
         $mensaje['MENSAJE'] = "Credenciales Incorrectas";
         $mensaje['ID'] = -1;
      }
   } else {
      $mensaje['ESTADO'] = 0;
      $mensaje['MENSAJE'] = "Usuario No Existente";
      $mensaje['ID'] = -1;
   }

   echo json_encode($mensaje);
   exit();
}

header("HTTP/1.1 400 Bad Request");