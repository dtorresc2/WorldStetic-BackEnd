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
   if (isset($_GET['proceso'])) {
      $input = file_get_contents('php://input');
      $input = json_decode($input, true);

      switch ($_GET['proceso']) {
         case 'registrar':
            $sql = "INSERT INTO factura_encabezado (
               serie, 
               numero_factura,
               nombre_factura,
               direccion_factura,
               monto,
               iva,
               monto_sin_iva,
               fecha_creacion,
               fecha_emision,
               estado,
               contado_credito,
               id_cliente,
               id_usuario,
               saldo_anterior,
               debe,
               haber,
               saldo_actual
            ) 
            VALUES(
               :SERIE, 
               :NUMERO_FACTURA,
               :NOMBRE_FACTURA,
               :DIRECCION_FACTURA,
               :MONTO,
               :IVA,
               :MONTO_SIN_IVA,
               (SELECT CONVERT_TZ(now(),'+00:00','-06:00')),
               :FECHA_EMISION,
               :ESTADO,
               :CONTADO_CREDITO,
               :ID_CLIENTE,
               :ID_USUARIO,
               0,
               0,
               0,
               0
               )";

            $stmt = $dbConn->prepare($sql);
            $stmt->bindParam(':SERIE', $input['SERIE'], PDO::PARAM_STR);
            $stmt->bindParam(':NUMERO_FACTURA', $input['NUMERO_FACTURA'], PDO::PARAM_INT);
            $stmt->bindParam(':NOMBRE_FACTURA', $input['NOMBRE_FACTURA'], PDO::PARAM_STR);
            $stmt->bindParam(':DIRECCION_FACTURA', $input['DIRECCION_FACTURA'], PDO::PARAM_STR);
            $stmt->bindParam(':MONTO', $input['MONTO'], PDO::PARAM_INT);
            $stmt->bindParam(':IVA', $input['IVA'], PDO::PARAM_INT);
            $stmt->bindParam(':MONTO_SIN_IVA', $input['MONTO_SIN_IVA'], PDO::PARAM_INT);

            $stmt->bindParam(':FECHA_EMISION', $input['FECHA_EMISION'], PDO::PARAM_STR);
            $stmt->bindParam(':ESTADO', $input['ESTADO'], PDO::PARAM_INT);
            $stmt->bindParam(':CONTADO_CREDITO', $input['CONTADO_CREDITO'], PDO::PARAM_INT);
            $stmt->bindParam(':ID_CLIENTE', $input['ID_CLIENTE'], PDO::PARAM_INT);
            $stmt->bindParam(':ID_USUARIO', $input['ID_USUARIO'], PDO::PARAM_INT);
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

            break;
         case 'listar':
            if ($input['ID_FACTURA'] > 0) {
               $sql = $dbConn->prepare(
                  "SELECT 
                     id_factura AS ID_FACTURA, 
                     serie AS SERIE, 
                     numero_factura AS NUMERO_FACTURA,
                     nombre_factura AS NOMBRE_FACTURA,
                     direccion_factura AS DIRECCION_FACTURA,
                     monto AS MONTO,
                     iva AS IVA,
                     monto_sin_iva AS MONTO_SIN_IVA,
                     DATE_FORMAT(fecha_creacion,'%d/%m/%Y') AS FECHA_CREACION,
                     DATE_FORMAT(fecha_emision,'%d/%m/%Y') AS FECHA_EMISION,
                     estado AS ESTADO,
                     contado_credito AS CONTADO_CREDITO,
                     saldo_anterior AS SALDO_ANTERIOR,
                     debe AS DEBE,
                     haber AS HABER,
                     saldo_actual AS SALDO_ACTUAL,
                     id_cliente AS ID_CLIENTE,
                     id_usuario AS ID_USUARIO
                     FROM factura_encabezado 
                     WHERE id_factura=:ID_FACTURA
                     "
               );
               $sql->bindValue(':ID_FACTURA', $input['ID_FACTURA']);
               $sql->execute();

               header("HTTP/1.1 200 OK");
               echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
               exit();
            } else {
               $sql = $dbConn->prepare(
                  "SELECT 
                     id_factura AS ID_FACTURA, 
                     serie AS SERIE, 
                     numero_factura AS NUMERO_FACTURA,
                     nombre_factura AS NOMBRE_FACTURA,
                     direccion_factura AS DIRECCION_FACTURA,
                     monto AS MONTO,
                     iva AS IVA,
                     monto_sin_iva AS MONTO_SIN_IVA,
                     DATE_FORMAT(fecha_creacion,'%d/%m/%Y') AS FECHA_CREACION,
                     DATE_FORMAT(fecha_emision,'%d/%m/%Y') AS FECHA_EMISION,
                     estado AS ESTADO,
                     contado_credito AS CONTADO_CREDITO,
                     saldo_anterior AS SALDO_ANTERIOR,
                     debe AS DEBE,
                     haber AS HABER,
                     saldo_actual AS SALDO_ACTUAL,
                     id_cliente AS ID_CLIENTE,
                     id_usuario AS ID_USUARIO
                     FROM factura_encabezado "
               );
               $sql->execute();
               $sql->setFetchMode(PDO::FETCH_ASSOC);

               header("HTTP/1.1 200 OK");
               echo json_encode($sql->fetchAll());
               exit();
            }
            break;
      }
   }
}
