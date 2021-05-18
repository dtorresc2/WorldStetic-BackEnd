<?php
include "../config/config.php";
include "../config/utils.php";

$dbConn =  connect($db);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
   $identificador = $_GET['id'];
   if (isset($_GET['id'])) {
      //Mostrar un post
      $sql = $dbConn->prepare("SELECT * FROM usuarios where id_usuario=:id");
      $sql->bindValue(':id', $_GET['id']);
      $sql->execute();

      header("HTTP/1.1 200 OK");
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
// https://stackoverflow.com/questions/8291712/using-clean-urls-in-restful-api
// https://codigonaranja.com/crear-restful-web-service-php