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

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
   if (isset($_GET['id'])) {
      $input = file_get_contents('php://input');
      $input = json_decode($input, true);

      if ($_GET['id'] == 0) {
         $sql = "UPDATE usuarios SET 
         usuario = :USUARIO
         WHERE id_usuario = :ID_USUARIO";

         $stmt = $dbConn->prepare($sql);
         $stmt->bindParam(':USUARIO', $input['DESCRIPCION'], PDO::PARAM_STR);
         $stmt->bindParam(':ID_USUARIO', $id, PDO::PARAM_INT);
         $stmt->execute();
      }

      header("HTTP/1.1 200 OK");

      $mensaje['ESTADO'] = 1;
      $mensaje['MENSAJE'] = "Actualizado Correctamente";
      $mensaje['ID'] = $id;

      echo json_encode($mensaje);
      exit();
   }
}

header("HTTP/1.1 400 Bad Request");
