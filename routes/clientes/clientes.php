<?php
include "../../config/config.php";
include "../../config/utils.php";

$dbConn = connect($db);

header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Methods');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');
header('Content-Type: application/json');

$mensaje = array(
   "ESTADO" => "",
   "MENSAJE" => "",
   "ID" => 0
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $input = file_get_contents('php://input');
   $input = json_decode($input, true);

   // SELECT CONVERT_TZ(now(),'+00:00','-06:00'); America/Guatemala

   $sql = "INSERT INTO clientes (
      nombre, 
      nit, 
      fecha_nacimiento,
      direccion,
      correo,
      telefono,
      saldo_anterior,
      debe,
      haber,
      saldo_actual,
      estado
   ) 
   VALUES(
      :NOMBRE, 
      :NIT, 
      :FECHA_NACIMIENTO,
      :DIRECCION,
      :CORREO,
      :TELEFONO,
      0,
      0,
      0,
      0,
      :ESTADO
      )";

   $stmt = $dbConn->prepare($sql);
   $stmt->bindParam(':NOMBRE', $input['NOMBRE'], PDO::PARAM_STR);
   $stmt->bindParam(':NIT', $input['NIT'], PDO::PARAM_STR);
   $stmt->bindParam(':FECHA_NACIMIENTO', $input['FECHA_NACIMIENTO'], PDO::PARAM_STR);
   $stmt->bindParam(':DIRECCION', $input['DIRECCION'], PDO::PARAM_STR);
   $stmt->bindParam(':CORREO', $input['CORREO'], PDO::PARAM_STR);
   $stmt->bindParam(':TELEFONO', $input['TELEFONO'], PDO::PARAM_STR);
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
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
   if (isset($_GET['id'])) {

      $sql = $dbConn->prepare(
         "SELECT
            cli.id_cliente AS ID_CLIENTE,
            cli.nombre AS NOMBRE,
            cli.nit AS NIT,
            DATE_FORMAT(cli.fecha_nacimiento, '%d/%m/%Y') AS FECHA_NACIMIENTO,
            cli.direccion AS DIRECCION,
            cli.correo AS CORREO,
            cli.telefono AS TELEFONO,
            cli.saldo_anterior AS SALDO_ANTERIOR,
            cli.debe AS DEBE,
            cli.haber AS HABER,
            cli.saldo_actual AS SALDO_ACTUAL,
            cli.estado AS ESTADO,
            IFNULL((SELECT COUNT(*) FROM factura_encabezado f WHERE f.id_cliente = cli.id_cliente),0) AS NO_FACTURAS
         FROM
            clientes cli
            WHERE id_cliente=:id"
      );
      $sql->bindValue(':id', $_GET['id']);
      $sql->execute();

      header("HTTP/1.1 200 OK");
      echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
      exit();
   } else {

      $sql = $dbConn->prepare(
         "SELECT 
            cli.id_cliente AS ID_CLIENTE,
            cli.nombre AS NOMBRE,
            cli.nit AS NIT,
            DATE_FORMAT(cli.fecha_nacimiento, '%d/%m/%Y') AS FECHA_NACIMIENTO,
            cli.direccion AS DIRECCION,
            cli.correo AS CORREO,
            cli.telefono AS TELEFONO,
            cli.saldo_anterior AS SALDO_ANTERIOR,
            cli.debe AS DEBE,
            cli.haber AS HABER,
            cli.saldo_actual AS SALDO_ACTUAL,
            cli.estado AS ESTADO,
            IFNULL((SELECT COUNT(*) FROM factura_encabezado f WHERE f.id_cliente = cli.id_cliente),0) AS NO_FACTURAS
         FROM
            clientes cli"
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

      $sql = "UPDATE clientes SET 
         nombre = :NOMBRE, 
         nit = :NIT, 
         fecha_nacimiento = :FECHA_NACIMIENTO,
         direccion = :DIRECCION,
         correo = :CORREO,
         telefono = :TELEFONO,
         estado = :ESTADO
      WHERE id_cliente = :ID_CLIENTE";

      $stmt = $dbConn->prepare($sql);
      $stmt->bindParam(':NOMBRE', $input['NOMBRE'], PDO::PARAM_STR);
      $stmt->bindParam(':NIT', $input['NIT'], PDO::PARAM_STR);
      $stmt->bindParam(':FECHA_NACIMIENTO', $input['FECHA_NACIMIENTO'], PDO::PARAM_STR);
      $stmt->bindParam(':DIRECCION', $input['DIRECCION'], PDO::PARAM_STR);
      $stmt->bindParam(':CORREO', $input['CORREO'], PDO::PARAM_STR);
      $stmt->bindParam(':TELEFONO', $input['TELEFONO'], PDO::PARAM_STR);
      $stmt->bindParam(':ESTADO', $input['ESTADO'], PDO::PARAM_INT);
      $stmt->bindParam(':ID_CLIENTE', $id, PDO::PARAM_INT);
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

      $sql = "DELETE FROM clientes
      WHERE id_cliente = :ID_CLIENTE";

      $stmt = $dbConn->prepare($sql);
      $stmt->bindParam(':ID_CLIENTE', $id, PDO::PARAM_INT);
      $stmt->execute();

      header("HTTP/1.1 200 OK");

      $mensaje['ESTADO'] = 1;
      $mensaje['MENSAJE'] = "Eliminado Correctamente";
      $mensaje['ID'] = $id;

      echo json_encode($mensaje);
      exit();
   }
}
