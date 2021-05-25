<?php
include "../config/config.php";
include "../config/utils.php";

$dbConn = connect($db);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Content-Type: application/json');

// Crear un nuevo post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $input = file_get_contents('php://input');
   // echo $input;
   $input = json_decode($input, true);

   // $sql = "INSERT INTO servicios (descripcion, monto, estado) VALUES(:descripcion, :monto, :estado)";
   // $statement = $dbConn->prepare($sql);
   // bindAllValues($statement, $input);
   // $statement->execute();
   // $postId = $dbConn->lastInsertId();

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
   $stmt->bindParam(':MONTO', $input['MONTO'], PDO::PARAM_STR);
   $stmt->bindParam(':ESTADO', $input['ESTADO'], PDO::PARAM_STR);
   $stmt->execute();
   $postId = $dbConn->lastInsertId();

   if ($postId) {
      $respuesta['id'] = $postId;
      header("HTTP/1.1 200 OK");
      echo json_encode($respuesta);
      exit();
   }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
   if (isset($_GET['id'])) {
      //Mostrar un post
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
      //Mostrar lista de post
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
