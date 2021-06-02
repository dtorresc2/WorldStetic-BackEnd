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
         case 'listar':
            $sql = $dbConn->prepare(
               "SELECT 
                  id_detalle AS ID_DETALLE,
                  id_factura AS ID_FACTURA,
                  id_servicio AS ID_SERVICIO,
                  cantidad AS CANTIDAD,
                  bien_servicio AS BIEN_SERVICIO,
                  descripcion AS DESCRIPCION,
                  monto_unitario AS MONTO_UNITARIO,
                  monto AS MONTO,
                  iva AS IVA,
                  monto_sin_iva AS MONTO_SIN_IVA
               FROM factura_detalle
               WHERE id_factura=:ID_FACTURA
               ORDER BY id_factura, id_detalle ASC
               "
            );
            $sql->bindValue(':ID_FACTURA', $input['ID_FACTURA']);
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_ASSOC);

            header("HTTP/1.1 200 OK");
            echo json_encode($sql->fetchAll());
            break;
      }
   }
}
