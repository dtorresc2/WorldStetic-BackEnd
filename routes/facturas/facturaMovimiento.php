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
            $sql = "INSERT INTO factura_movimiento (
               id_factura,
               cargo_abono, 
               fecha,
               descripcion,
               monto,
               iva,
               monto_sin_iva,
               estado,
               id_usuario
            ) 
            VALUES(
               :ID_FACTURA, 
               :CARGO_ABONO, 
               (SELECT CONVERT_TZ(now(),'+00:00','-06:00')),
               :DESCRIPCION,
               :MONTO,
               :IVA,
               :MONTO_SIN_IVA,
               :ESTADO,
               :ID_USUARIO
               )";

            $montoSinIva = ($input['MONTO'] / 1.12);
            $IVA = ($input['MONTO'] * 0.12);

            $stmt = $dbConn->prepare($sql);
            $stmt->bindParam(':ID_FACTURA', $input['ID_FACTURA'], PDO::PARAM_INT);
            $stmt->bindParam(':CARGO_ABONO', $input['CARGO_ABONO'], PDO::PARAM_STR);
            $stmt->bindParam(':DESCRIPCION', $input['DESCRIPCION'], PDO::PARAM_STR);
            $stmt->bindParam(':MONTO', $input['MONTO'], PDO::PARAM_INT);
            $stmt->bindParam(':IVA', $IVA, PDO::PARAM_INT);
            $stmt->bindParam(':MONTO_SIN_IVA', $montoSinIva, PDO::PARAM_INT);
            $stmt->bindParam(':ESTADO', $input['ESTADO'], PDO::PARAM_INT);
            $stmt->bindParam(':ID_USUARIO', $input['ID_USUARIO'], PDO::PARAM_INT);
            $stmt->execute();
            $postId = $dbConn->lastInsertId();

            if ($postId) {
               header("HTTP/1.1 200 OK");

               $mensaje['ESTADO'] = 1;
               $mensaje['MENSAJE'] = "Creado Correctamente";
               $mensaje['ID'] = $postId;

               echo json_encode($mensaje);
               exit();
            }
            break;

         case 'listar':
            if ($input['ID_MOVIMIENTO'] > 0) {
               $sql = $dbConn->prepare(
                  "SELECT
                     id_movimiento AS ID_MOVIMIENTO,
                     id_factura AS ID_FACTURA,
                     cargo_abono AS CARGO_ABONO,
                     DATE_FORMAT(fecha, '%d/%m/%Y') AS FECHA,
                     DATE_FORMAT(fecha_anulacion, '%d/%m/%Y') AS FECHA_ANULACION,
                     descripcion AS DESCRIPCION,
                     monto AS MONTO,
                     iva AS IVA,
                     monto_sin_iva AS MONTO_SIN_IVA,
                     estado AS ESTADO,
                     id_usuario AS ID_USUARIO
                  FROM factura_movimiento
                  WHERE id_factura=:ID_FACTURA 
                  AND id_movimiento=:ID_MOVIMIENTO
                  "
               );
               $sql->bindValue(':ID_FACTURA', $input['ID_FACTURA']);
               $sql->bindValue(':ID_MOVIMIENTO', $input['ID_MOVIMIENTO']);
               $sql->execute();

               header("HTTP/1.1 200 OK");
               echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
               exit();
            } else {
               $sql = $dbConn->prepare(
                  "SELECT 
                     id_movimiento AS ID_MOVIMIENTO,
                     id_factura AS ID_FACTURA,
                     cargo_abono AS CARGO_ABONO,
                     DATE_FORMAT(fecha, '%d/%m/%Y') AS FECHA,
                     DATE_FORMAT(fecha_anulacion, '%d/%m/%Y') AS FECHA_ANULACION,
                     descripcion AS DESCRIPCION,
                     monto AS MONTO,
                     iva AS IVA,
                     monto_sin_iva AS MONTO_SIN_IVA,
                     estado AS ESTADO,
                     id_usuario AS ID_USUARIO
                  FROM factura_movimiento
                  WHERE id_factura=:ID_FACTURA
                  ORDER BY fecha DESC"
               );
               $sql->bindValue(':ID_FACTURA', $input['ID_FACTURA']);

               $sql->execute();
               $sql->setFetchMode(PDO::FETCH_ASSOC);

               header("HTTP/1.1 200 OK");
               echo json_encode($sql->fetchAll());
               exit();
            }
            break;

         case 'actualizar':
            $sql = "UPDATE factura_movimiento SET 
               cargo_abono = :CARGO_ABONO, 
               descripcion = :DESCRIPCION,
               monto = :MONTO,
               estado = :ESTADO,
               iva = :IVA,
               monto_sin_iva = :MONTO_SIN_IVA,
               id_usuario = :ID_USUARIO
            WHERE id_movimiento = :ID_MOVIMIENTO";

            $montoSinIva = ($input['MONTO'] / 1.12);
            $IVA = ($input['MONTO'] * 0.12);

            $stmt = $dbConn->prepare($sql);
            $stmt->bindParam(':CARGO_ABONO', $input['CARGO_ABONO'], PDO::PARAM_STR);
            $stmt->bindParam(':DESCRIPCION', $input['DESCRIPCION'], PDO::PARAM_STR);
            $stmt->bindParam(':MONTO', $input['MONTO'], PDO::PARAM_INT);
            $stmt->bindParam(':ESTADO', $input['ESTADO'], PDO::PARAM_INT);
            $stmt->bindParam(':IVA', $IVA, PDO::PARAM_INT);
            $stmt->bindParam(':MONTO_SIN_IVA', $montoSinIva, PDO::PARAM_INT);
            $stmt->bindParam(':ID_USUARIO', $input['ID_USUARIO'], PDO::PARAM_INT);
            $stmt->bindParam(':ID_MOVIMIENTO', $input['ID_MOVIMIENTO'], PDO::PARAM_INT);
            $stmt->execute();

            header("HTTP/1.1 200 OK");

            $mensaje['ESTADO'] = 1;
            $mensaje['MENSAJE'] = "Actualizado Correctamente";
            $mensaje['ID'] = $input['ID_MOVIMIENTO'];

            echo json_encode($mensaje);
            exit();
            break;

         case 'eliminar':
            $sql = "DELETE FROM factura_movimiento
               WHERE id_movimiento = :ID_MOVIMIENTO";

            $stmt = $dbConn->prepare($sql);
            $stmt->bindParam(':ID_MOVIMIENTO', $input['ID_MOVIMIENTO'], PDO::PARAM_INT);
            $stmt->execute();

            header("HTTP/1.1 200 OK");

            $mensaje['ESTADO'] = 1;
            $mensaje['MENSAJE'] = "Eliminado Correctamente";
            $mensaje['ID'] = $input['ID_MOVIMIENTO'];

            echo json_encode($mensaje);
            exit();
            break;

         case 'anular':
            $sql = "UPDATE factura_movimiento SET 
               estado = 0,
               fecha_anulacion = (SELECT CONVERT_TZ(now(),'+00:00','-06:00'))
            WHERE id_movimiento = :ID_MOVIMIENTO";

            $stmt = $dbConn->prepare($sql);
            $stmt->bindParam(':ID_MOVIMIENTO', $input['ID_MOVIMIENTO'], PDO::PARAM_INT);
            $stmt->execute();

            header("HTTP/1.1 200 OK");

            $mensaje['ESTADO'] = 1;
            $mensaje['MENSAJE'] = "Anulado Correctamente";
            $mensaje['ID'] = $input['ID_MOVIMIENTO'];

            echo json_encode($mensaje);
            exit();
            break;

         case 'habilitar':
            $sql = "UPDATE factura_movimiento SET 
               estado = 1
               WHERE id_movimiento = :ID_MOVIMIENTO";

            $stmt = $dbConn->prepare($sql);
            $stmt->bindParam(':ID_MOVIMIENTO', $input['ID_MOVIMIENTO'], PDO::PARAM_INT);
            $stmt->execute();

            header("HTTP/1.1 200 OK");

            $mensaje['ESTADO'] = 1;
            $mensaje['MENSAJE'] = "Habilitado Correctamente";
            $mensaje['ID'] = $input['ID_MOVIMIENTO'];

            echo json_encode($mensaje);
            exit();
            break;
      }
   }
}
