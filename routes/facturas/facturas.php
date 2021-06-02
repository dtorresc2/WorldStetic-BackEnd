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

            $montoSinIva = ($input['MONTO'] / 1.12);
            $IVA = ($input['MONTO'] * 0.12);

            $stmt = $dbConn->prepare($sql);
            $stmt->bindParam(':SERIE', $input['SERIE'], PDO::PARAM_STR);
            $stmt->bindParam(':NUMERO_FACTURA', $input['NUMERO_FACTURA'], PDO::PARAM_INT);
            $stmt->bindParam(':NOMBRE_FACTURA', $input['NOMBRE_FACTURA'], PDO::PARAM_STR);
            $stmt->bindParam(':DIRECCION_FACTURA', $input['DIRECCION_FACTURA'], PDO::PARAM_STR);
            $stmt->bindParam(':MONTO', $input['MONTO'], PDO::PARAM_INT);
            $stmt->bindParam(':IVA', $IVA, PDO::PARAM_INT);
            $stmt->bindParam(':MONTO_SIN_IVA', $montoSinIva, PDO::PARAM_INT);
            $stmt->bindParam(':FECHA_EMISION', $input['FECHA_EMISION'], PDO::PARAM_STR);
            $stmt->bindParam(':ESTADO', $input['ESTADO'], PDO::PARAM_INT);
            $stmt->bindParam(':CONTADO_CREDITO', $input['CONTADO_CREDITO'], PDO::PARAM_INT);
            $stmt->bindParam(':ID_CLIENTE', $input['ID_CLIENTE'], PDO::PARAM_INT);
            $stmt->bindParam(':ID_USUARIO', $input['ID_USUARIO'], PDO::PARAM_INT);
            $stmt->execute();
            $postId = $dbConn->lastInsertId();

            if ($postId) {
               $respuesta['id'] = $postId;

               // Registrar Factura
               $lent = sizeof($input['DETALLE']);

               for ($i = 0; $i < $lent; $i++) {
                  // echo json_encode($input['DETALLE'][$i]);
                  $sql = "INSERT INTO factura_detalle (
                     id_detalle, 
                     id_factura,
                     id_servicio,
                     cantidad,
                     bien_servicio,
                     descripcion,
                     monto_unitario,
                     monto,
                     iva,
                     monto_sin_iva
                  ) 
                  VALUES(
                     :ID_DETALLE, 
                     :ID_FACTURA,
                     :ID_SERVICIO,
                     :CANTIDAD,
                     :BIEN_SERVICIO,
                     :DESCRIPCION,
                     :MONTO_UNITARIO,
                     :MONTO,
                     :IVA,
                     :MONTO_SIN_IVA
                     )";

                  $cont = ($i + 1);
                  $montoSinIva = ($input['DETALLE'][$i]['MONTO'] / 1.12);
                  $IVA = ($input['DETALLE'][$i]['MONTO'] * 0.12);

                  $stmt = $dbConn->prepare($sql);
                  $stmt->bindParam(':ID_DETALLE', $cont, PDO::PARAM_INT);
                  $stmt->bindParam(':ID_FACTURA', $postId, PDO::PARAM_INT);
                  $stmt->bindParam(':ID_SERVICIO', $input['DETALLE'][$i]['ID_SERVICIO'], PDO::PARAM_INT);
                  $stmt->bindParam(':CANTIDAD', $input['DETALLE'][$i]['CANTIDAD'], PDO::PARAM_INT);
                  $stmt->bindParam(':BIEN_SERVICIO', $input['DETALLE'][$i]['BIEN_SERVICIO'], PDO::PARAM_STR);
                  $stmt->bindParam(':DESCRIPCION', $input['DETALLE'][$i]['DESCRIPCION'], PDO::PARAM_STR);
                  $stmt->bindParam(':MONTO_UNITARIO', $input['DETALLE'][$i]['MONTO_UNITARIO'], PDO::PARAM_INT);
                  $stmt->bindParam(':MONTO', $input['DETALLE'][$i]['MONTO'], PDO::PARAM_INT);
                  $stmt->bindParam(':IVA', $IVA, PDO::PARAM_INT);
                  $stmt->bindParam(':MONTO_SIN_IVA', $montoSinIva, PDO::PARAM_INT);
                  $stmt->execute();
                  #$postId = $dbConn->lastInsertId();
               }

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
                     fact.id_factura AS ID_FACTURA,
                     fact.serie AS SERIE,
                     fact.numero_factura AS NUMERO_FACTURA,
                     fact.nombre_factura AS NOMBRE_FACTURA,
                     fact.direccion_factura AS DIRECCION_FACTURA,
                     fact.monto AS MONTO,
                     fact.iva AS IVA,
                     fact.monto_sin_iva AS MONTO_SIN_IVA,
                     DATE_FORMAT(fact.fecha_creacion, '%d/%m/%Y') AS FECHA_CREACION,
                     DATE_FORMAT(fact.fecha_emision, '%d/%m/%Y') AS FECHA_EMISION,
                     fact.estado AS ESTADO,
                     fact.contado_credito AS CONTADO_CREDITO,
                     fact.saldo_anterior AS SALDO_ANTERIOR,
                     fact.debe AS DEBE,
                     fact.haber AS HABER,
                     fact.saldo_actual AS SALDO_ACTUAL,
                     fact.id_cliente AS ID_CLIENTE,
                     fact.id_usuario AS ID_USUARIO,
                     cli.nombre AS NOMBRE_CLIENTE,
                     cli.nit AS NIT,
                     cli.telefono AS TELEFONO,
                     usu.usuario AS USUARIO
                  FROM factura_encabezado fact
                     LEFT JOIN clientes cli ON fact.id_cliente = cli.id_cliente
                     LEFT JOIN usuarios usu ON fact.id_usuario = usu.id_usuario
                  WHERE fact.id_factura=:ID_FACTURA
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
                     fact.id_factura AS ID_FACTURA,
                     fact.serie AS SERIE,
                     fact.numero_factura AS NUMERO_FACTURA,
                     fact.nombre_factura AS NOMBRE_FACTURA,
                     fact.direccion_factura AS DIRECCION_FACTURA,
                     fact.monto AS MONTO,
                     fact.iva AS IVA,
                     fact.monto_sin_iva AS MONTO_SIN_IVA,
                     DATE_FORMAT(fact.fecha_creacion, '%d/%m/%Y') AS FECHA_CREACION,
                     DATE_FORMAT(fact.fecha_emision, '%d/%m/%Y') AS FECHA_EMISION,
                     fact.estado AS ESTADO,
                     fact.contado_credito AS CONTADO_CREDITO,
                     fact.saldo_anterior AS SALDO_ANTERIOR,
                     fact.debe AS DEBE,
                     fact.haber AS HABER,
                     fact.saldo_actual AS SALDO_ACTUAL,
                     fact.id_cliente AS ID_CLIENTE,
                     fact.id_usuario AS ID_USUARIO,
                     cli.nombre AS NOMBRE_CLIENTE,
                     cli.nit AS NIT,
                     cli.telefono AS TELEFONO,
                     usu.usuario AS USUARIO
                  FROM factura_encabezado fact
                     LEFT JOIN clientes cli ON fact.id_cliente = cli.id_cliente
                     LEFT JOIN usuarios usu ON fact.id_usuario = usu.id_usuario
                     "
               );
               $sql->execute();
               $sql->setFetchMode(PDO::FETCH_ASSOC);

               header("HTTP/1.1 200 OK");
               echo json_encode($sql->fetchAll());
               exit();
            }
            break;

         case 'actualizar':
            $sql = "UPDATE factura_encabezado SET 
               serie = :SERIE, 
               numero_factura = :NUMERO_FACTURA, 
               nombre_factura = :NOMBRE_FACTURA,
               direccion_factura = :DIRECCION,
               monto = :MONTO,
               iva = :IVA,
               monto_sin_iva = :MONTO_SIN_IVA,
               fecha_emision = :FECHA_EMISION,
               estado = :ESTADO,
               contado_credito = :CONTADO_CREDITO,
               id_cliente = :ID_CLIENTE,
               id_usuario = :ID_USUARIO
            WHERE id_factura = :ID_FACTURA";

            $stmt = $dbConn->prepare($sql);
            $stmt->bindParam(':SERIE', $input['SERIE'], PDO::PARAM_STR);
            $stmt->bindParam(':NUMERO_FACTURA', $input['NUMERO_FACTURA'], PDO::PARAM_INT);
            $stmt->bindParam(':NOMBRE_FACTURA', $input['NOMBRE_FACTURA'], PDO::PARAM_STR);
            $stmt->bindParam(':DIRECCION', $input['DIRECCION'], PDO::PARAM_STR);
            $stmt->bindParam(':MONTO', $input['MONTO'], PDO::PARAM_INT);
            $stmt->bindParam(':IVA', $input['IVA'], PDO::PARAM_INT);
            $stmt->bindParam(':MONTO_SIN_IVA', $input['MONTO_SIN_IVA'], PDO::PARAM_INT);
            $stmt->bindParam(':FECHA_EMISION', $input['FECHA_EMISION'], PDO::PARAM_STR);
            $stmt->bindParam(':ESTADO', $input['ESTADO'], PDO::PARAM_INT);
            $stmt->bindParam(':CONTADO_CREDITO', $input['CONTADO_CREDITO'], PDO::PARAM_INT);
            $stmt->bindParam(':ID_CLIENTE', $input['ID_CLIENTE'], PDO::PARAM_INT);
            $stmt->bindParam(':ID_USUARIO', $input['ID_USUARIO'], PDO::PARAM_INT);
            $stmt->bindParam(':ID_FACTURA', $input['ID_FACTURA'], PDO::PARAM_INT);
            $stmt->execute();

            header("HTTP/1.1 200 OK");

            $mensaje['ESTADO'] = 1;
            $mensaje['MENSAJE'] = "Actualizado Correctamente";
            $mensaje['ID'] = $input['ID_FACTURA'];

            echo json_encode($mensaje);
            exit();

            break;

         case 'eliminar':
            $sql = "DELETE FROM factura_encabezado
            WHERE id_factura = :ID_FACTURA";

            $stmt = $dbConn->prepare($sql);
            $stmt->bindParam(':ID_FACTURA', $input['ID_FACTURA'], PDO::PARAM_INT);
            $stmt->execute();

            header("HTTP/1.1 200 OK");

            $mensaje['ESTADO'] = 1;
            $mensaje['MENSAJE'] = "Eliminado Correctamente";
            $mensaje['ID'] = $input['ID_FACTURA'];

            echo json_encode($mensaje);
            exit();
            break;
      }
   }
}
