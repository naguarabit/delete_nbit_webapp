'use strict';



angular.module('myApp.transaccResumen', ['ngRoute'])



//enrutamiento a resumen de una transaccion, se busca por su id

.config(['$routeProvider', function($routeProvider) {

  $routeProvider.when('/transaccion/:id_transaccion', {

    templateUrl: 'transacc/resumen.html',

    controller: 'ctrlTransaccionResumen'

  });

}])





//enrutamiento a resumen pagos destino de transaccción

.config(['$routeProvider', function($routeProvider) {

  $routeProvider.when('/transaccion/destino', {

    templateUrl: 'transacc/destino.html',

    controller: 'ctrlTransaccionPagosDestino'

  });

}])



.controller('ctrlTransaccionResumen', ['$scope', '$http', '$routeParams', function($scope, $http, $routeParams) {

  var ctrl = this;

  $scope.data = [];

  //para mostrar mensajes

  $scope.showError = false;

  $scope.mostrarExito = false;

  $scope.showErrorNotFound = false;

  $scope.showForm = true;

  $scope.exitoUpdatePD = false;

  //datos de transacción

  $scope.id_transaccion = $routeParams.id_transaccion;

  console.log('id_transaccion: ' + $scope.id_transaccion);





  //captura parametro - id de transacción

  $scope.capturarParametro = function(){

    console.log('controlador-transaccResumen- capturarParametro. inicio');

    $scope.id_transaccion = $routeParams.id_transaccion;

    console.log('id_transaccion: ' + $scope.id_transaccion);

    //TODO. validar parametro, que sea numerico entero

    console.log('controlador-transaccResumen- capturarParametro. fin');

  }





  //funcion para OBTENER DATOS DE transaccion, y los guarda en variable json

  $scope.getDataResumen = function() {

    console.log('controlador-getDataResumen. inicio');

    //TODO. agregar uso de parametro

    $http.get("./transacc/resumen_get.php?id=" + $scope.id_transaccion)

    .then(function (response) {

      console.log('controlador-getDataResumen. then');

      var data = response.data.records;

      if (data[0] != null){

        $scope.data = data[0];

        $scope.showForm = true;

        console.log('controlador-getDataResumen. SI trajo datos');
        console.log('$scope.data trasaccion resumen');
        console.log($scope.data);
        
        console.log('login:');
        console.log($scope.data.login);

      } else{

        console.log('controlador-getDataResumen. NO trajo datos');

        $scope.showForm = false;

        $scope.msg = "Transaccion con id: " + $scope.id_transaccion + " no encontrada";

        $scope.showErrorNotFound = true;

      }

      console.log('controlador-getDataResumen- getDataResumen. then. end');

  },

  function(data, status) {

    console.error('Error en llamada a busqueda dato: ', status, data);

    $scope.msg = "Error consultando datos";

    $scope.showError = true;

    $scope.showForm = false;

    });   //TODO: gestionar error, cuando no se traigan los datos del usuario, mostrar mensaje en vista

  };





//formatear valores de campo date_created

$scope.formatDateCreated = function (data){

  // Obteniendo todas las claves del JSON

  var json = angular.copy(data);

  for (var clave in json){

    // Controlando que json realmente tenga esa propiedad

    if (json.hasOwnProperty(clave)) {

      // Mostrando en pantalla la clave junto a su valor

      console.log('fecha: ' + json[clave].date_created);

      console.log('fecha formateada: ' + formatDate(json[clave].date_created));

      json[clave].date_created = formatDate(json[clave].date_created);

    }

  }

  return json;

};





 //funcion para OBTENER DATOS DE transaccion, y los guarda en variable json

 $scope.getDataPagosDestino = function() {

   console.log('controlador -resumen- getDataPagosDestino. start');



   //TODO. agregar uso de parametro

   $http.get("./../backend/transacc/destino_transacc.php?id=" + $scope.id_transaccion)

   .then(function (response) {

     if (response.data.records){

       $scope.resultados_destino = response.data.records;

       console.log('resultados_destino: ' + JSON.stringify($scope.resultados_destino));

       $scope.resultados_destino = $scope.formatDateCreated($scope.resultados_destino);

       console.log('controlador -resumen- getDataPagosDestino. data_destino = ');

       console.log($scope.resultados_destino);



      //sumar los pagos realizados

      $scope.pago_pendiente = 0.00;

      var total_pagado = 0.00;

      var total_destino = 0.00;

      for(var i = 0; i < $scope.resultados_destino.length; i++) {

          var fila = $scope.resultados_destino[i];

          total_destino += parseFloat(fila.monto);

          //verifica si este pago fue realizado

          if (fila.check_realizado == 1)

            total_pagado += parseFloat(fila.monto);

          console.log('monto en posicion ' + i + '= ' + fila.monto);

      }

      console.log('total pagado destino:' + total_pagado);

      $scope.total_pagado = total_pagado;

      $scope.pago_pendiente = total_destino - $scope.total_pagado;



      $scope.destino_showForm = true;

   }

   else{

     $scope.destino_showForm = false;

     $scope.msg = "Info no encontrada: Pagos a destino con id transaccion: " + $scope.id_transaccion;

     $scope.destino_showErrorNotFound = true;

   }

 },

 function(data, status) {

   console.error('Error en llamada a busqueda dato: ', status, data);

   $scope.msg = "Error consultando datos";

   $scope.destino_showError = true;

   $scope.destino_showForm = false;

   });   //TODO: gestionar error, cuando no se traigan los datos del usuario, mostrar mensaje en vista

  console.log('controlador -resumen- getDataPagosDestino. fin');

 };













  //NOW
  //TODO. adaptar: add que el cliente hizo la confirmacion...
  /*funcion para actualizar transacción, pagos en destino*/

  $scope.updatePagoDestino = function($tipo) {

    console.log('transacc- funcion:updatePagoDestino. inicio');

    /*-
    var d = {};
    //$d.id_transaccion  = $scope.data.id_transaccion;
    d.id_pago_destino = $id_pago_destino;
    //TODO. add field usuario_login_operador_pagador
    $scope.dataSave  = d;
    */

    console.log('$scope.dataSave');
    console.log($scope.dataSave);

    $scope.exitoUpdatePD = false;

    //indica url de peticion (luego puede cambiarse para llamar a API tipo RestFul)
    var update = '';

    //debugger;
    if ($tipo == 8){ //marcar como pago CONFIRMADO
      update = './../backend/transacc/update_transaccion8.php';
    }
    //todo. AGREGAR UN ESTADO DEL PAGO, DIGAMOS 90, LLAMA a un update que borra/revierte el pago.
    /*
    else if ($tipo == 90){ //marcar como pago CONFIRMADO
      update = './transacc/update_transaccion7.php';
    }
    */

    $http.post(update, JSON.stringify($scope.dataSave))

    .then(function (response) {

      //debugger;

      //+$scope.showErrorNotFound = false;

      if (response.data){

        debugger;
        //punto1

        var dataResp = response.data.records[0];

        if(dataResp.resultado != null)

          if(dataResp.resultado == 'EXITO'){

            //debugger;
            //punto2 EXITO

            $scope.msg = "Pago destino actualizado con Exito!";

            $scope.exitoUpdatePD = true;

            //$scope.data.id_transaccion = dataResp.resultado.id_trans; 

            //console.info('transaccion.id: ' + $scope.transaccion.id);

            //+$scope.mostrarExito = true;

            //+$scope.showError = false;

            //return true;

          }else if(dataResp.resultado == 'ERROR'){

            //debugger;
            //punto3 ERROR

            $scope.msg = '\n' + dataResp.mensaje;

            $scope.exitoUpdatePD = false;

            //+$scope.showError = true;

            //+$scope.mostrarExito = false;

            //return false;

          }

         //debugger;
          //punto4 ANTES DE UPDATE TRANSACCION

          if ($scope.exitoUpdatePD){

            //si se confirmo el pago destino, se actualiza la transaccion
            if ($tipo == 8){
              //TODO. :
              //if ya se pago todo el saldo del envio en moneda destino
              //entonces: actualizar transaccion, marcando estatus='FIN', estatus_PD='OK'
              //TODO. tambien se deberia comprobar que se recibio todo el pago en origen, aunque hay que considerar cuando se hacen remesas a credito 

              debugger;
              //punto2

              //VERSION 1:
              //COMO CADA TRANSACCION AHORA TIENE UN UNICO PAGO A DESTINO, EN ESTE IF VAMOS A FINALIZAR LA TRANSACCION
              $scope.updateTransaccion_PD(7);
            }

            //debugger;
            //punto3

            //reload page to show changes
            window.location.reload();
          }
          else{
              //TODO. cambiar este alert, por mensaje en la interfaz, mas amigable y menos intrusivo
              alert('No se pudo actualizar el pago. Por favor vuelve a intentar.')
          }

        }//if response.data

      }, function (response) {
        $scope.msg = "Ups. Hubo un error al intentar registrar la Informacion: Service not Exists (" + update + ")";
        $scope.statusval = response.status;
        $scope.statustext = response.statusText;
          //$scope.headers = response.headers();
          $scope.mostrarError = true;
          $scope.mostrarExito = false;
      });

    console.log('-transacc/resumen- funcion:updatePagoDestino. fin');

  };//updatePagoDestino




 /*funcion para actualizar transacción
 $tipo 7: por pagos en destino*/

  $scope.updateTransaccion_PD = function($tipo) {

    console.log('transacc- funcion:updateTransaccion_PD. inicio');

    var d = {};
    d.id_transaccion = $scope.id_transaccion;
    //TODO. add field usuario_login_check, que seria el user login en data a grabar
    $scope.dataSave  = d;

    console.log('$scope.dataSave');
    console.log($scope.dataSave);

    debugger;

    //indica url de peticion (luego puede cambiarse para llamar a API tipo RestFul)
    var update = '';
    
    if ($tipo == 7){ //actualizar transaccion con PAGO DESTINO 'PD_OK', Y estatus 'FIN'
      update = './../backend/transacc/update_transaccion7.php';
    }

    $http.post(update, JSON.stringify($scope.dataSave))

    .then(function (response) {

      //+$scope.showErrorNotFound = false;

      if (response.data){

        debugger;

        var dataResp = response.data.records[0];

        if(dataResp.resultado != null)

          if(dataResp.resultado == 'EXITO'){

            debugger;

            $scope.msg = "Transacción actualizada con Exito!";

            //$scope.data.id_transaccion = dataResp.resultado.id_trans;

            //console.info('transaccion.id: ' + $scope.transaccion.id);

            //+$scope.mostrarExito = true;

            //+$scope.showError = false;

          }else if(dataResp.resultado == 'ERROR'){

            debugger;

            $scope.msg = '\n' + dataResp.mensaje;

            //+$scope.showError = true;

            //+$scope.mostrarExito = false;

          }

        }

      }, function (response) {
        $scope.msg = "Ups. Hubo un error al intentar registrar la Informacion: Service not Exists (" + update + ")";
        $scope.statusval = response.status;
        $scope.statustext = response.statusText;
          //$scope.headers = response.headers();
          $scope.mostrarError = true;
          $scope.mostrarExito = false;
      });

    console.log('-transacc/resumen- funcion:updateTransaccion_PD. fin');

  };//updateTransaccion_PD






  $scope.confirmarPagoDestino = function($id_pago_destino) {
    console.info('Se va a actualizar el pago en destino con id:' + $id_pago_destino);
    var d = {};
        
    //$d.id_transaccion  = $scope.data.id_transaccion;
    d.id_pago_destino = $id_pago_destino;
    d.login = $scope.data.login;
    //TODO. add field usuario que hace la confirmacion (deberia ser el user de la transaccion)
    $scope.dataSave = d;
    $scope.updatePagoDestino(8);
  }




//funcion llamada al inicio, cuando se carga la pantalla
$scope.init_function = function(){

  console.log('controlador -transacc/resumen- init_function. start');

  //-$scope.saludo = "Saludo desde el ctrl -transacc/resumen-. ";

  //$scope.cargarPaises();

  $scope.capturarParametro();

  if($scope.id_transaccion == 'new'){

    console.log('Opcion: NEW');

    $scope.reset();

  }else{

    $scope.getDataResumen();

    $scope.getDataPagosDestino();

    //+$scope.getDataPagosOrigen();

  }

  console.log('controlador -transacc/resumen- init_function. end');

}



  ////*debugger;

  console.log('controlador-transacc/resumen. inicio');

  $scope.init_function();

  console.log('controlador -transacc/resumen- fin');

}]
)

//directiva para mostrar mensaje de confirmacion, en un boton o enlace
.directive('ngConfirmClick', [
        function(){
            return {
                link: function (scope, element, attr) {
                    var msg = attr.ngConfirmClick || "Estás seguro de esta acción?";
                    var clickAction = attr.confirmedClick;
                    element.bind('click',function (event) {
                        if ( window.confirm(msg) ) {
                            scope.$eval(clickAction)
                        }
                    });
                }
            };
}]);

