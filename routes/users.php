<?php
include "../config/config.php";
include "../config/utils.php";

$dbConn =  connect($db);
/*
  listar todos los posts o solo uno
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
   $identificador = $_GET['id'];
   if (isset($_GET['id'])) {
      //Mostrar un post
      $sql = $dbConn->prepare("SELECT * FROM usuarios where id_usuario=:id");
      $sql->bindValue(':id', $_GET['id']);
      $sql->execute();

      

      header("HTTP/1.1 200 OK");
      // header('Content-Type: application/json');
      echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
      exit();
   } else {
      //Mostrar lista de post
      $sql = $dbConn->prepare("SELECT * FROM usuarios");
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);

      header("HTTP/1.1 200 OK");
      echo json_encode($sql->fetchAll());
      exit();
   }
}
