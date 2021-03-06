<?php
include "../../config/config.php";
include "../../config/utils.php";

// include '../../lib/Password_Compat/password.php';

$dbConn = connect($db);

$mensaje = array(
   "ESTADO" => "",
   "MENSAJE" => "",
   "ID" => 0
);

header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Methods');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT');
header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
   if (isset($_GET['id'])) {
      $input = file_get_contents('php://input');
      $input = json_decode($input, true);

      $sql = "UPDATE usuarios SET 
         usuario = :USUARIO
         WHERE id_usuario = :ID_USUARIO";

      $stmt = $dbConn->prepare($sql);
      $stmt->bindParam(':USUARIO', $input['USUARIO'], PDO::PARAM_STR);
      $stmt->bindParam(':ID_USUARIO', $_GET['id'], PDO::PARAM_INT);
      $stmt->execute();

      $ID = $_GET['id'];

      header("HTTP/1.1 200 OK");

      $mensaje['ESTADO'] = 1;
      $mensaje['MENSAJE'] = "Actualizado Correctamente";
      $mensaje['ID'] = $ID;

      echo json_encode($mensaje);
      exit();
   }
}
// else {
//    header("HTTP/1.1 400 Bad Request");
//    exit();
// }
header("HTTP/1.1 400 Bad Request");
exit();