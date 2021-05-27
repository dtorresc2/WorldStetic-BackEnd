<?php
include "../../config/config.php";
include "../../config/utils.php";

$dbConn = connect($db);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Content-Type: application/json');

$mensaje = array(
   "ESTADO" => "",
   "MENSAJE" => "",
   "ID" => 0
);

// Crear un nuevo post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $input = file_get_contents('php://input');
   $input = json_decode($input, true);

   $sql = "INSERT INTO servicios (
      descripcion, 
      monto, 
      estado
   ) 
   VALUES(
      :DESCRIPCION, 
      :MONTO, 
      :ESTADO
      )";

   $stmt = $dbConn->prepare($sql);
   $stmt->bindParam(':DESCRIPCION', $input['DESCRIPCION'], PDO::PARAM_STR);
   $stmt->bindParam(':MONTO', $input['MONTO'], PDO::PARAM_INT);
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
      // echo json_encode($respuesta);
      exit();
   }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
   if (isset($_GET['id'])) {

      $sql = $dbConn->prepare(
         "SELECT " .
            " id_servicio AS ID_SERVICIO, " .
            " descripcion AS DESCRIPCION, " .
            " monto AS MONTO, " .
            " estado AS ESTADO " .
            "FROM servicios " .
            " WHERE id_servicio=:id"
      );
      $sql->bindValue(':id', $_GET['id']);
      $sql->execute();

      header("HTTP/1.1 200 OK");
      echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
      exit();
   } else {

      $sql = $dbConn->prepare(
         "SELECT " .
            " id_servicio AS ID_SERVICIO, " .
            " descripcion AS DESCRIPCION, " .
            " monto AS MONTO, " .
            " estado AS ESTADO " .
            "FROM servicios "
      );
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);

      header("HTTP/1.1 200 OK");
      echo json_encode($sql->fetchAll());
      exit();
   }
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
   if (isset($_GET['id'])) {
      $input = $_GET;
      $id = $input['id'];

      $input = file_get_contents('php://input');
      $input = json_decode($input, true);

      $sql = "UPDATE servicios SET 
         descripcion = :DESCRIPCION, 
         monto = :MONTO, 
         estado = :ESTADO 
      WHERE id_servicio = :ID_SERVICIO";

      $stmt = $dbConn->prepare($sql);
      $stmt->bindParam(':DESCRIPCION', $input['DESCRIPCION'], PDO::PARAM_STR);
      $stmt->bindParam(':MONTO', $input['MONTO'], PDO::PARAM_INT);
      $stmt->bindParam(':ESTADO', $input['ESTADO'], PDO::PARAM_INT);
      $stmt->bindParam(':ID_SERVICIO', $id, PDO::PARAM_INT);
      $stmt->execute();

      header("HTTP/1.1 200 OK");

      $mensaje['ESTADO'] = 1;
      $mensaje['MENSAJE'] = "Actualizado Correctamente";
      $mensaje['ID'] = $id;

      echo json_encode($mensaje);
      exit();
   }
}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
   if (isset($_GET['id'])) {
      $input = $_GET;
      $id = $input['id'];

      $sql = "DELETE FROM servicios
      WHERE id_servicio = :ID_SERVICIO";

      $stmt = $dbConn->prepare($sql);
      $stmt->bindParam(':ID_SERVICIO', $id, PDO::PARAM_INT);
      $stmt->execute();

      header("HTTP/1.1 200 OK");

      $mensaje['ESTADO'] = 1;
      $mensaje['MENSAJE'] = "Eliminado Correctamente";
      $mensaje['ID'] = $id;

      echo json_encode($mensaje);
      exit();
   }
}

header("HTTP/1.1 400 Bad Request");
