<?php
include "../config/config.php";
include "../config/utils.php";

$dbConn =  connect($db);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Content-Type: application/json');

// Crear un nuevo post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $input = $_POST;
   $sql = "INSERT INTO servicios " .
      "(descripcion, monto, estado) " .
      " VALUES " .
      "(:DESCRIPCION, :MONTO, :ESTADO)";

   $statement = $dbConn->prepare($sql);
   bindAllValues($statement, $input);
   $statement->execute();
   $postId = $dbConn->lastInsertId();

   if ($postId) {
      $input['id'] = $postId;
      header("HTTP/1.1 200 OK");
      echo json_encode($input);
      exit();
   }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
   if (isset($_GET['id'])) {
      //Mostrar un post
      $sql = $dbConn->prepare(
         "SELECT ". 
         "id_servicio AS ID_SERVICIO, ".
         "descripcion AS DESCRIPCION, ".
         "monto AS MONTO, ".
         "estado AS ESTADO ".
         "FROM usuarios ".
         "where id_servicio=:id"
      );
      $sql->bindValue(':id', $_GET['id']);
      $sql->execute();

      header("HTTP/1.1 200 OK");
      echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
      exit();
   } else {
      //Mostrar lista de post
      $sql = $dbConn->prepare(
         "SELECT ". 
         "id_usuario AS ID_USUARIO, ".
         "usuario AS USUARIO ".
         "FROM usuarios "
      );
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);

      header("HTTP/1.1 200 OK");
      echo json_encode($sql->fetchAll());
      exit();
   }
}