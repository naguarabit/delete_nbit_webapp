<?php

//graba datos de beneficiario de una transacción

//en el objeto dataParams, debe ir el campo 'id_transaccion'

//cambia el status de una transacción a Pagado

header("Access-Control-Allow-Origin: *");

header("Content-Type: application/json; charset=UTF-8");



include("../bd/connection.php");



//get objeto json con los parametros proporcionados a través de llamada tipo RestFul, desde el controlador

$dataParams =  json_decode(file_get_contents('php://input'), true);

//-echo "param.login: \n $dataParams";



/*capturar objeto datos a grabar, pasado como parametro post*/

  if (isset($dataParams)){

    $data = $dataParams;

    //*debug

    //echo "\n data user >> login: " . $data['login'] . "\n";

  }else{

    $data= null;

    //*echo "\n data user vacia \n";

  }



$outp = "";



if($data == null){//validacion pendiente

//TODO. validar datos a guardar

/*

if (!isset($user) || $$user == null || !isset($user->id) == null || $user->id == null){

*/  //devolver mensaje de error

  $err = "Error en datos de transaccion proporcionados para grabar";

  $outp .='{"resultado":"ERROR"';

  $outp .=',"mensaje":"' .$err. '"';

  $outp .=',"datos":"'   .$data. '"';

  $outp .= '}';



}else { //datos validos

  /*

//test datos aleatorios:

  $login = 'login' . rand(5, 100);

  $mail = 'user' . rand(5, 100) . '@mail.com';

  $nombre = 'Nombre ' . rand(5, 100);



EJEMPLO:

/*

$sql = "INSERT INTO transacciones

(login_user, user_observ, origen_codpais, destino_codpais, origen_monto, destino_monto, tasa_dolar_origen, tasa_dolar_destino, fechahora_origen)

VALUES ('appcode', ?, 'PAR', 'VEN', '6000', '13000', '6000', '13000', CURRENT_TIMESTAMP);";

*/



//TODO. validar cada dato antes de pasarlo al insert

//TODO. calcular hora en origen, calcular hora en destino. No necesariamente sera igual a la del servidor



//asociar beneficiario a transacción

$sql = "INSERT INTO pago_destino (id_transaccion, login, cod_pais, monto, cod_banco, nroctabank, observ_user)

        VALUES ("

        //."id_transaccion = " . $data['id_transaccion'] ."',"

        ."id_transaccion = " . 33 .",";

        ."'". $data['user']['login'] ."',"

        ."'". $data['cod_pais2'] ."',"

        .     $data['monto2'] .","

        ."'". $data['destino']['cod_banco'] ."',"

        ."'". $data['destino']['nrocta'] ."',"

        ."'". $data['destino']['tipo_cta'] ."',"

        ."'". $data['destino']['doctitular'] ."',"

        ."'". $data['destino']['nombretitular'] ."',"

        .     $data['monto2'] .","

        //...???

        //completar campos

        .")"; 

)



  $id_pago_destino = 0;

  if($conn->query($sql) == TRUE) {

    if ($conn->insert_id){//se adjuntó comprobante de pago en origen

      

      //ahora se procede a actualizar tabla transacciones con status: pagado en origen

      $id_pago_destino = $conn->insert_id;

      $sql = "UPDATE transacciones SET status = 'ORI_OK' 

      WHERE  id = $data['id_transaccion'] ";

    }



    //*DEBUG

    //*printf ("New Transaction has id: %d.\n", $conn->insert_id);

    if ($outp != "") {$outp .= ",";}

      $outp .='{"resultado":"EXITO"';

      $outp .=',"mensaje":"'    . "Comprobante(s) guardado(s)" . '"';

      $outp .=',"id_transaccion":'     . $data['id_transaccion'];

      $outp .=',"id_pago_destino":'    . $id_pago_destino; //$conn->insert_id;

      $outp .=',"sql":"'        . $sql; //*DEBUG

      $outp .='}';

  }else{

    $err =  "Error de app, intentando guardar datos. <br> Query: " . $sql . "<br>". $conn->error . "";

    $outp .='{"resultado":"ERROR"';

    $outp .=',"mensaje":"' .$err. '"';

    $outp .= '}';

  }

}


  $outp ='{"records":['.$outp.']}';

  $conn->close();

  echo($outp);

?>







