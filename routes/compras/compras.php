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
            $sql = "INSERT INTO compras (
               fecha, 
               descripcion, 
               monto,
               iva,
               monto_sin_iva,
               estado,
               id_usuario
            ) 
            VALUES(
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
            $stmt->bindParam(':DESCRIPCION', $input['DESCRIPCION'], PDO::PARAM_STR);
            $stmt->bindParam(':MONTO', $input['MONTO'], PDO::PARAM_INT);
            $stmt->bindParam(':IVA', $IVA, PDO::PARAM_INT);
            $stmt->bindParam(':MONTO_SIN_IVA', $montoSinIva, PDO::PARAM_INT);
            $stmt->bindParam(':ESTADO', $input['ESTADO'], PDO::PARAM_INT);
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
            if ($input['ID_COMPRA'] > 0) {
               $sql = $dbConn->prepare(
                  "SELECT 
                     id_compra AS ID_COMPRA,
                     DATE_FORMAT(fecha, '%d/%m/%Y') AS FECHA,
                     descripcion AS DESCRIPCION,
                     monto AS MONTO,
                     iva AS IVA,
                     monto_sin_iva AS MONTO_SIN_IVA,
                     estado AS ESTADO,
                     id_usuario AS ID_USUARIO
                  FROM compras
                     WHERE id_compra=:ID_COMPRA
                     "
               );
               $sql->bindValue(':ID_COMPRA', $input['ID_COMPRA']);
               $sql->execute();

               header("HTTP/1.1 200 OK");
               echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
               exit();
            } else {
               $sql = $dbConn->prepare(
                  "SELECT 
                     id_compra AS ID_COMPRA,
                     DATE_FORMAT(fecha, '%d/%m/%Y') AS FECHA,
                     descripcion AS DESCRIPCION,
                     monto AS MONTO,
                     iva AS IVA,
                     monto_sin_iva AS MONTO_SIN_IVA,
                     estado AS ESTADO,
                     id_usuario AS ID_USUARIO
                  FROM compras
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
            $sql = "UPDATE compras SET 
               descripcion = :DESCRIPCION, 
               monto = :MONTO,
               iva = :IVA,
               monto_sin_iva = :MONTO_SIN_IVA,
               estado = :ESTADO,
               id_usuario = :ID_USUARIO
            WHERE id_compra = :ID_COMPRA";

            $montoSinIva = ($input['MONTO'] / 1.12);
            $IVA = ($input['MONTO'] * 0.12);

            $stmt = $dbConn->prepare($sql);
            $stmt->bindParam(':DESCRIPCION', $input['DESCRIPCION'], PDO::PARAM_STR);
            $stmt->bindParam(':MONTO', $input['MONTO'], PDO::PARAM_INT);
            $stmt->bindParam(':IVA', $IVA, PDO::PARAM_INT);
            $stmt->bindParam(':MONTO_SIN_IVA', $montoSinIva, PDO::PARAM_INT);
            $stmt->bindParam(':ESTADO', $input['ESTADO'], PDO::PARAM_INT);
            $stmt->bindParam(':ID_USUARIO', $input['ID_USUARIO'], PDO::PARAM_INT);
            $stmt->bindParam(':ID_COMPRA', $input['ID_COMPRA'], PDO::PARAM_INT);
            $stmt->execute();

            header("HTTP/1.1 200 OK");

            $mensaje['ESTADO'] = 1;
            $mensaje['MENSAJE'] = "Actualizado Correctamente";
            $mensaje['ID'] = $input['ID_COMPRA'];

            echo json_encode($mensaje);
            exit();
            break;
      }
   }
}
