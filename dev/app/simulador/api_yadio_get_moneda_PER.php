<?php

/*hacer consulta a api de Yadio, debuelve valores de referencia actual respecto a la moneda sol peruano

//TODO. generalizarlo para cualquier moneda

*/



//headers

header("Access-Control-Allow-Origin: *");

header("Content-Type: application/json; charset=UTF-8");

//TODO. aplicar codigo para que se pueda usar la misma peticion variando la moneda.
//hacer luego que funcione para PYG y PER por separado
if (isset($_GET['codigo'])){

  $codigo = $_GET['codigo'];

}else{

  $codigo="";

}

$outp = "";

if (!isset($codigo)  || $codigo == ""){
  //TODO. devolver mensaje de error
  
  $err = "Check your params, needed: codigo. Example: http://localhost/naguarabit/app/simulador/api_yadio_get_moneda.php?codigo=PEN";
  
  $outp .='{"resultado":"ERROR"';
  
  $outp .=',"mensaje":"' .$err. '"';
  
  $outp .= '}';  $outp ='{"records":['.$outp.']}';
  
  $conn->close();
}


//sol peruano

//$URL = "https://api.yadio.io/rate/PEN";



//guarani

$URL = "https://api.yadio.io/rate/" . $codigo;

//*echo $URL;

$outp = "";



//opcion 1

$json = file_get_contents($URL);

$outp = json_decode($json);



if ($outp != "")

  $outp ='{"records":['.$json.']}';

else {

  //TODO. manejar el error cuando no se consigue data, para mostrar en la vista

  $outp ='{"records":[{"error": "error en obtener data"]}';

}

echo($outp);

?>

