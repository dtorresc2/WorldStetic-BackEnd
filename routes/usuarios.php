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

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
   if (isset($_GET['id'])) {
      //Mostrar un post
      $sql = $dbConn->prepare(
         "SELECT " .
            "id_usuario AS ID_USUARIO, " .
            "usuario AS USUARIO " .
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
            "usuario AS USUARIO " .
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

   $hash = password_hash('hola', PASSWORD_BCRYPT);
   $mensaje['ESTADO'] = $hash;

   header("HTTP/1.1 200 OK");

   echo $hash;
   exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
   $input = file_get_contents('php://input');
   $input = json_decode($input, true);

   if (password_verify('hola', $input['PASS'])) {
      /* Valid */
      echo 'Si';
   } else {
      /* Invalid */
      echo 'No';
   }

   header("HTTP/1.1 200 OK");
   exit();
}

if (password_verify($password, $hash)) {
   /* Valid */
} else {
   /* Invalid */
}
// https://stackoverflow.com/questions/8291712/using-clean-urls-in-restful-api
// https://codigonaranja.com/crear-restful-web-service-php