<?php
include "../../config/config.php";
include "../../config/utils.php";

include '../../lib/password.php';

$dbConn = connect($db);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Content-Type: application/json');

$mensaje = array(
   "ESTADO" => "",
   "MENSAJE" => "",
   "ID" => 0
);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
   if (isset($_GET['id'])) {
      //Mostrar un post
      $sql = $dbConn->prepare(
         "SELECT " .
            "id_usuario AS ID_USUARIO, " .
            "usuario AS USUARIO, " .
            "password AS PASSWORD, " .
            "estado AS ESTADO, " .
            "admin AS ADMIN " .
            "FROM usuarios " .
            "where id_usuario=:id"
      );
      $sql->bindValue(':id', $_GET['id']);
      $sql->execute();

      header("HTTP/1.1 200 OK");
      echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
      exit();
   } else {
      //Mostrar lista de post
      $sql = $dbConn->prepare(
         "SELECT " .
            "id_usuario AS ID_USUARIO, " .
            "usuario AS USUARIO, " .
            "password AS PASSWORD, " .
            "estado AS ESTADO, " .
            "admin AS ADMIN " .
            "FROM usuarios "
      );
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);

      header("HTTP/1.1 200 OK");
      echo json_encode($sql->fetchAll());
      exit();
   }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $input = file_get_contents('php://input');
   $input = json_decode($input, true);

   $sql = "INSERT INTO usuarios (
      usuario, 
      password, 
      estado,
      admin
   ) 
   VALUES(
      :USUARIO, 
      :PASSWORD, 
      :ESTADO,
      1
      )";

   $hash = password_hash($input['PASSWORD'], PASSWORD_BCRYPT);

   $stmt = $dbConn->prepare($sql);
   $stmt->bindParam(':USUARIO', $input['USUARIO'], PDO::PARAM_STR);
   $stmt->bindParam(':PASSWORD', $hash, PDO::PARAM_STR);
   $stmt->bindParam(':ESTADO', $input['ESTADO'], PDO::PARAM_INT);
   $stmt->execute();
   $postId = $dbConn->lastInsertId();

   if ($postId) {
      $respuesta['id'] = $postId;
      header("HTTP/1.1 200 OK");

      $mensaje['ESTADO'] = 1;
      $mensaje['MENSAJE'] = "Creado Correctamente";
      $mensaje['ID'] = $respuesta['id'];

      echo json_encode($mensaje);
      exit();
   }
   exit();
}
// if (password_verify($password, $hash)) {
//    /* Valid */
// } else {
//    /* Invalid */
// }

// https://stackoverflow.com/questions/8291712/using-clean-urls-in-restful-api
// https://codigonaranja.com/crear-restful-web-service-php