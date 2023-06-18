<?php

/*

paises/list_short.php

2019-07-12

using customers.php from example in https://www.w3schools.com/angular/angular_sql.asp

updated: 11.dic.2021

author: naguarabit.com

*/

header("Access-Control-Allow-Origin: *");

header("Content-Type: application/json; charset=UTF-8");



include("../bd/connection.php");



$sql ="SELECT codigo, nombre FROM pais where ACTIVO = 1 ORDER BY order, nombre";

$result = $conn->query($sql);



$outp = "";

while($rs = $result->fetch_array(MYSQLI_ASSOC)) {

  if ($outp != "") {$outp .= ",";}

  $outp .= '{"codigo":"'  . $rs["codigo"]  . '"';

  $outp .= ',"nombre":"' . $rs["nombre"] . '"';

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