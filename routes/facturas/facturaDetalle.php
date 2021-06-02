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
            echo json_encode($mensaje);
            exit();
            break;
      }


      // echo json_encode($input['DETALLE'][0]['ID']);
      // $lent = sizeof($input['DETALLE']);

      // for ($i = 0; $i < $lent; $i++) {
      //    echo json_encode($input['DETALLE'][$i]);
      // }
   }
}
