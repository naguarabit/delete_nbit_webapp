<?php

//return lista client's destiny accounts (to populate user's account list)

//se indica cual consulta realizar mediante

//parametro GET['codpais']


//TODO. urgent: get parameter login from data and pass to query

header("Access-Control-Allow-Origin: *");

header("Content-Type: application/json; charset=UTF-8");

include("../bd/connection.php");


if (isset($_GET['codigo_pais'])){
  $codigo_pais = $_GET['codigo_pais'];
}else{
  $codigo_pais="";
}

if (isset($_GET['login'])){
  $login = $_GET['login'];
}else{
  $login="";
}


$outp = "";

if (!isset($codigo_pais)  || $codigo_pais == ""){
  //TODO. devolver mensaje de error
  $err = "No se proporciono parámetro: codigo_pais";
  $outp .='{"resultado":"ERROR"';
  $outp .=',"mensaje":"' .$err. '"';
  $outp .= '}';  $outp ='{"records":['.$outp.']}';
  $conn->close();
}

if (!isset($login)  || $login == ""){
  //TODO. devolver mensaje de error
  $err = "No se proporciono parámetro: login";
  $outp .='{"resultado":"ERROR"';
  $outp .=',"mensaje":"' .$err. '"';
  $outp .= '}';  $outp ='{"records":['.$outp.']}';
  $conn->close();
}

else{
//TODO. generalizar a todos los paises, con cod_pais2, pasar en filtro de query: campo a.cod_pais
//TODO. pasar parametro login a filtro de campo u.login
//OJO: solo bancos de Vzla

//garbage: a.id as id_cuenta_destino,

//TODO. optimizar query, que traiga solo 1 fila por cada nro de cuenta, sin importar si hay repetido el nombre del titular

$sql ="WITH accounts AS (
	SELECT DISTINCT a.cod_banco, a.nroctabank, a.tipo_cta, a.doc_titular,
	b.nombre as nombre_banco, b.nombre_largo as banco_nombre_largo
	FROM  pago_destino a
	INNER JOIN transacciones t ON a.id_transaccion = t.id
	INNER JOIN user u ON u.login = t.login
	LEFT  JOIN banks_pais b ON b.codigo = a.cod_banco
	WHERE a.cod_pais = '". $codigo_pais ."' ".
	" AND   u.login = '". $login ."' ".
	"AND   t.activo = 1
	AND   a.nroctabank IS NOT NULL AND TRIM(a.nroctabank) != ''
	AND   a.tipo_cta IS NOT NULL AND TRIM(a.tipo_cta) != ''
	AND   a.doc_titular IS NOT NULL AND TRIM(a.doc_titular) != ''
	AND   a.nombre_titular IS NOT NULL AND TRIM(a.nombre_titular) != ''
	ORDER BY b.nombre, a.nombre_titular
),
titulares AS (
	SELECT DISTINCT d.nroctabank, UPPER(d.nombre_titular) AS nombre_titular
	FROM   pago_destino d
    LEFT OUTER JOIN accounts a USING (nroctabank)
	WHERE  d.nombre_titular IS NOT NULL AND TRIM(d.nombre_titular) != ''
	AND    a.nroctabank IS NOT NULL AND TRIM(a.nroctabank) != ''
    GROUP BY d.nroctabank
)
SELECT a.*, 
(
	SELECT t.nombre_titular
    FROM   titulares t
    LEFT JOIN pago_destino d USING (nroctabank)
    WHERE  t.nroctabank = a.nroctabank
    ORDER BY LENGTH(t.nombre_titular) DESC
    LIMIT 1
) as nombre_titular_unico_x_cuenta,
(
	SELECT max(d.telefono)
    FROM   pago_destino d
    JOIN titulares t USING (nroctabank)
    WHERE  t.nroctabank = d.nroctabank
    AND    d.telefono IS NOT NULL AND TRIM(d.telefono) != ''
    -- LIMIT 1
) as telefono_unico_x_cuenta,
(
	SELECT max(d.email)
    FROM   pago_destino d
    JOIN titulares t USING (nroctabank)
    WHERE  t.nroctabank = d.nroctabank
    AND    d.email IS NOT NULL AND TRIM(d.email) != ''
) as email_titular_unico_x_cuenta
from accounts a";

//TODO. optimizar query
//email: traer el email mas usado para cada cuenta
//telefono: traer el email mas usado para cada cuenta
//titular: traer el nombre mas largo o el mas usado para cada cuenta

$result = $conn->query($sql);	

$outp = "";

while($rs = $result->fetch_array(MYSQLI_ASSOC)) {

  if ($outp != "") {$outp .= ",";}


  //$outp .= '{"id_cuenta_destino":"'  . $rs["id_cuenta_destino"]  . '"';

  //TODO. puede hacer falta retornar un campo que tenga solo los ultimos 6 digitos de la cuenta, para solo mostrar esos digitos, y no la cuenta completa
  $outp .= '{"nroctabank":"'      . $rs["nroctabank"]  . '"';

  $outp .= ', "cod_banco":"'      . $rs["cod_banco"] . '"';

  $outp .= ', "nombre_banco":"'   . $rs["nombre_banco"] . '"';

  $outp .= ', "tipo_cta":"'       . $rs["tipo_cta"] . '"';

  $outp .= ', "doc_titular":"'    . $rs["doc_titular"] . '"';

  $outp .= ', "telefono":"'       . $rs["telefono_unico_x_cuenta"] . '"';

  $outp .= ', "email":"'          . $rs["email_titular_unico_x_cuenta"] . '"';

  $outp .= ', "nombre_titular":"' . $rs["nombre_titular_unico_x_cuenta"] . '"';

  $outp .= '}';

}

$outp ='{"records":['.$outp.']}';

//TODO. manejar el error cuando no se consigue data

$conn->close();


}
echo($outp);



/*todo, hacer join de tabla paises y ciudades*/

/*

  $outp .= ',"ciudad":"' . $rs["cod_ciudad"] . '"';

  $outp .= ',"pais":"'  . $rs["cod_pais"]  . '"';

*/

?>