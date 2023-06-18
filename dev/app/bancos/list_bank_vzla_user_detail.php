<?php

//return clients destiny accounts list (with all details that calc needs)

//se indica cual consulta realizar mediante

//parametro GET['codpais']

//parametro GET['login']

header("Access-Control-Allow-Origin: *");

header("Content-Type: application/json; charset=UTF-8");

include("../bd/connection.php");


//TODO. generalizar a todos los paises, con cod_pais2, pasar en filtro de query: campo a.cod_pais
//TODO. pasar parametro login a filtro de campo u.login
//OJO: solo bancos de Vzla

$sql = "SELECT a.cod_banco, a.nroctabank, a.tipo_cta, a.doc_titular, a.nombre_titular, a.email, a.telefono \n"

    . "FROM pago_destino a \n"

    . "INNER JOIN transacciones t ON a.id_transaccion = t.id \n"

    . "INNER JOIN user u ON u.login = t.login \n"

    . "LEFT JOIN banks_pais b ON b.codigo = a.cod_banco \n"

    . "WHERE a.cod_pais = \'VEN\' \n"

    . "AND a.nroctabank LIKE \'%01340406284061048089%\' \n"

    . "AND a.nroctabank IS NOT NULL\n"

    . "AND a.doc_titular IS NOT NULL AND TRIM(a.doc_titular) != \'\' \n"

    . "AND a.nombre_titular IS NOT NULL AND TRIM(a.nombre_titular) != \'\' \n"

    . "LIMIT 1";

$result = $conn->query($sql);



$outp = "";

while($rs = $result->fetch_array(MYSQLI_ASSOC)) {

  if ($outp != "") {$outp .= ",";}

  //TODO. retornar un campo que tenga solo los ultimos 6 digitos de la cuenta

  $outp .= '{"nroctabank":"'  . $rs["nroctabank"]  . '"';

  $outp .= ',"nombre_banco":"' . $rs["nombre_banco"] . '"';

  $outp .= ',"nombre_titular":"' . $rs["nombre_titular"] . '"';

  $outp .= '}';

}

$outp ='{"records":['.$outp.']}';

//TODO. manejar el error cuando no se consigue data

$conn->close();



echo($outp);



/*todo, hacer join de tabla paises y ciudades*/

/*

  $outp .= ',"ciudad":"' . $rs["cod_ciudad"] . '"';

  $outp .= ',"pais":"'  . $rs["cod_pais"]  . '"';

*/

?>