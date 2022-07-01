<?php

include_once '../datos/Empresa.php';
include_once '../datos/Responsable.php';
include_once '../datos/Viaje.php';
include_once '../datos/Pasajero.php';

$empresa = new Empresa();
$empresa->cargar(0,"Transporte","Neuquen");
//insertarBd($empresa);

$empresas = $empresa->listar();
$idEmpresa = $empresas[count($empresas)-1]->getIdempresa();
$empresa->setIdempresa($idEmpresa);

revisarbdViajes($empresa);

opciones($empresa);


/**
 * función que da un menú para realizar lo que quiera el usuario con respecto a la empresa
 * @param object $empresa
 */
function opciones($empres){
    $viajes = new Viaje();
    $responsable = new Responsable();
    $pasajeros = new Pasajero();
     
    do{
        echo "\n-------Menú de opciones-------\n"
            ."1) Ingresar datos de una nueva empresa.\n"
            ."2) Modificar información de una empresa.\n"
            ."3) Eliminar información de una empresa.\n"
            ."4) Ver todos los datos.\n"
            ."5) Salir.\n";
        
        echo "Ingrese su eleccion: ";
        $eleccion = trim(fgets(STDIN));
    
        //sale del programa o llama a los metodos dependiendo de la elección del usuario
        switch($eleccion){
            case 1:ingresarEmpresa($empres);break;
            case 2:modificarEmpresa($empres);break;
            case 3:eliminarEmpresa($empres);break;
            case 4:listarBd($empres);listarBd($viajes);listarBd($responsable);listarBd($pasajeros);break;
            case 5:echo "Programa finalizado";break;
            default:echo "Elección ingresada no valida, intente otra vez";break;
        }
    }while($eleccion!=5);
}

/**
 * función que le permite al usuario ingresar una empresa
 * @param object $empre
 */
function ingresarEmpresa($empre){

    echo "------Ingrese datos de la empresa------\n";
    echo "Ingrese nombre de la empresa: ";
    $nombre = trim(fgets(STDIN));
    echo "Ingrese dirección de la empresa: ";
    $direccion = trim(fgets(STDIN));

    $empre->cargar(0,$nombre,$direccion);
    insertarBd($empre);

    $empresas = $empre->listar();
    $idEmpresa = $empresas[count($empresas)-1]->getIdempresa();
    $empre->setIdempresa($idEmpresa);

    revisarbdViajes($empre);

    echo "¿Desea ingresar viajes a la empresa?\nIngrese 'Si' para ingresar Viajes, 'No' para parar no ingresar ningún Viaje: ";
    $eleccion = trim(fgets(STDIN));
    
    if(strcasecmp($eleccion,"Si")==0){
      ingresarViajes($empre);  
    }
    
}

/**
 * función que le permite al usuario ingresar un responsable del viaje
 * @param object $viaje
 */
function ingresarResponsable($viaje){

    echo "---Ingrese datos del Responsable del Viaje---\n";
    echo "Ingrese número de licencia: ";
    $licencia = trim(fgets(STDIN));
    echo "Ingrese nombre: ";
    $nombre = trim(fgets(STDIN));
    echo "Ingrese apellido: ";
    $apellido = trim(fgets(STDIN));
            
    $viaje->getRefResponsable()->cargar(0,$licencia, $nombre, $apellido);
    insertarBd($viaje->getRefResponsable());

    $responsables = $viaje->getRefResponsable()->listar();
    $idResponsable = $responsables[count($responsables)-1]->getRnumeroempleado();

    $viaje->getRefResponsable()->setRnumeroempleado($idResponsable);
}

/**
 * función que le permite al usuario ingresar los viajes
 * @param object $empresa
 */
function ingresarViajes($empresa){
    $i = 0;
    $seguir = "si";

    echo "\n------Ingrese viajes------\n";

    while(strcasecmp($seguir,"Si")==0){
        
        echo "------Ingrese datos del viaje------\n";
        echo "Ingrese destino del viaje: ";
        $destino = trim(fgets(STDIN));
        echo "Ingrese la cantidad máxima de pasajeros: ";
        $cantMax = trim(fgets(STDIN));
        echo "Ingrese importe del viaje: ";
        $importe = trim(fgets(STDIN));
        echo "Ingrese tipo de asiento del viaje: ";
        $tipoAsiento = trim(fgets(STDIN));
        echo "¿El viaje es de ida y vuelta?: ";
        $idaVuelta = trim(fgets(STDIN));
        
        $responsable = new Responsable();

        $viaje[$i] = new Viaje();

        if(verificarDestinoViaje($destino,$empresa)){
        $viaje[$i]->cargar(0,$destino,$cantMax, $empresa, $responsable, $importe, $tipoAsiento, $idaVuelta);

        ingresarResponsable($viaje[$i]);
        insertarBd($viaje[$i]);

        $viajes = $viaje[$i]->listar();
        $idViaje = $viajes[count($viajes)-1]->getIdviaje();

        $viaje[$i]->setIdviaje($idViaje);

        $empresa->agregarViaje($viaje[$i]); 

        revisarbdPasajeros($viaje[$i]);

        echo "¿Desea ingresar pasajeros al viaje?\nIngrese 'Si' para ingresar Pasajeros, 'No' para parar no ingresar ningún Pasajero: ";
        $eleccion = trim(fgets(STDIN));
    
            if(strcasecmp($eleccion,"Si")==0){
                ingresarPasajeros($viaje[$i]);
            }
        
        }else{
            echo "El destino del viaje ya ha sido ingresado, intente otra vez\n";
        }

        $i++;

        echo "¿Desea ingresar otro viaje?: ";
        $seguir = trim(fgets(STDIN)); 
    }
    echo "\nLos datos de los viajes actualmente son: \n".$empresa->stringColeccion($empresa->getColViajes());
}



/**
 * Función que carga los datos de los pasajeros ingresados por el usuario
 * @param object $datosViaje;
 */
function ingresarPasajeros($datosViaje){
    $cantMaxima = $datosViaje->getVcantmaxpasajeros();
    $i = 0;
    $seguir = "si";
    echo "---Ingrese pasajeros---\n";
        
    //strcasemp() para comparar el 'si' sin importar las mayúsculas o minúsculas
    while(strcasecmp($seguir,"Si")==0 && $datosViaje->hayPasajesDisponibles()){
    
        echo "Ingrese nombre del pasajero: ";
        $nombre = trim(fgets(STDIN));
        echo "Ingrese apellido del pasajero: ";
        $apellido = trim(fgets(STDIN));
        echo "Ingrese número de documento: ";
        $nroDocu = trim(fgets(STDIN));
        echo "Ingrese número de Teléfono: ";
        $tlfno = trim(fgets(STDIN));
    
        $pasajero[$i] = new Pasajero();
        
        if($datosViaje->encontrarIndice($nroDocu) != null){
            echo "Este pasajero ya ha sido ingresado, ingrese otro\n";
        }else{
            
            $pasajero[$i]->cargar($nroDocu, $nombre, $apellido, $tlfno, $datosViaje);
            $datosViaje->venderPasaje($pasajero[$i]);
            insertarBd($pasajero[$i]);
        }
        $i++;
    
        if(!$datosViaje->hayPasajesDisponibles()){
            echo "Ya llegó a la cantidad límite de pasajeros\n";
        }else{
            echo "¿Desea seguir ingresando más pasajeros?\nIngrese 'Si' para continuar, 'No' para parar: ";    
            $seguir = trim(fgets(STDIN));
        }
    }

    echo "\n\nLos datos de los pasajeros del viaje actualmente son: ".$datosViaje->stringColeccion($datosViaje->getColPasajeros());
}

/**
 * función que le permite al usuario modificar los datos de una empresa
 * @param object $empre
 */
function modificarEmpresa($empre){
    echo "Ingrese numero de id de la empresa que desea modificar: ";
    $id = trim(fgets(STDIN));
    $encontrada = $empre->Buscar($id); 

    if($encontrada == true){
     
        echo "\n------MODIFICAR: \n"
            ."1) Modificar nombre.\n"
            ."2) Modificar dirección.\n"
            ."3) Modificar viaje.\n"
            ."4) Eliminar un viaje.\n"
            ."5) Ver viajes de la empresa.\n"
            ."6) Agregar un viaje.\n";

        echo "Ingrese su eleccion: ";
        $modificar = trim(fgets(STDIN));

        switch($modificar){

            case 1: echo "Ingrese nombre nuevo: ";
                    $nombre = trim(fgets(STDIN));
                    $empre->setEnombre($nombre);
                    modificarBd($empre);
                    break;

            case 2: echo "Ingrese dirección nueva: ";
                    $direccion = trim(fgets(STDIN));
                    $empre->setEdireccion($direccion);
                    modificarBd($empre);
                    break;
            case 3: modificarViaje($empre);break;
            case 4: eliminarViaje($empre);break;
            case 5: echo $empre->stringColeccion($empre->getColViajes());break;
            case 6: ingresarViajes($empre);break;
            default:echo "Elección ingresada no valida, intente otra vez";break;
        }
        
    }else{    
        echo "\n¡Empresa no encontrada!\n";
    }    
}

/**
 * función que le permite al usuario modificar los datos de un viaje
 * @param object $empresa
 */
function modificarViaje($empresa){
    echo "Ingrese numero de id del viaje que desea modificar: ";
    $id = trim(fgets(STDIN));
    $indiceViaje = $empresa->encontrarIndice($id);

    $viaje = new Viaje();
    $encontrado = $viaje->Buscar($id); 

    if($encontrado == true){

        $viaje = $empresa->getColViajes()[$indiceViaje];
       
        do{
            echo "\n------Ingrese dato que desea MODIFICAR del viaje------\n"
                ."1) Destino.\n"
                ."2) Cantidad Maxima de pasajeros.\n"
                ."3) Responsable.\n"
                ."4) Importe.\n"
                ."5) Tipo de asiento.\n"
                ."6) Ida y vuelta del viaje.\n"
                ."7) Pasajeros.\n"
                ."8) Eliminar un pasajero.\n"
                ."9) Agregar un pasajero.\n"
                ."10) Ver pasajeros del viaje.\n"
                ."11) Ver responsable del viaje.\n"
                ."12) Volver.\n";
                
            echo "Ingrese su eleccion: ";
            $eleccion = trim(fgets(STDIN));
            
            //llama al metodo escogido por el usuario 
            switch($eleccion){
                case 1:echo "Ingrese destino nuevo del viaje: ";
                $destino = trim(fgets(STDIN));
                            if(verificarDestinoViaje($destino,$empresa)){
                                $viaje->setVdestino($destino);
                            }else{
                                echo "Este destino ya ha sido ingresado, intente con otro";
                            }
                            break;
                case 2:echo "Ingrese cantidad maxima de pasajeros nueva del viaje: ";
                        $cantNueva = trim(fgets(STDIN));
                        if($cantNueva>count($viaje->getColPasajeros())){
                        $viaje->setVcantmaxpasajeros($cantNueva);break;
                        }else{
                        echo "La cantidad nueva es menor a la cantidad de pasajeros ya ingresados.\n"; 
                        }
                        break;
                case 3: modificarResponsable($viaje);break;
                case 4:echo "Ingrese nuevo importe del viaje: ";
                            $importe = trim(fgets(STDIN));
                            $viaje->setVimporte($importe);
                            modificarBd($viaje);
                            break;
                case 5:echo "Ingrese nuevo tipo de asiento del viaje: ";
                            $tipoAsiento = trim(fgets(STDIN));
                            $viaje->setTipoAsiento($tipoAsiento);
                            modificarBd($viaje);
                            break;
                case 6:echo "Ingrese si es de ida y vuelta o no: ";
                            $idavuelta = trim(fgets(STDIN));
                            $viaje->setIdayvuelta($idavuelta);
                            modificarBd($viaje);
                            break;
                case 7: modificarDatosPasajero($viaje);break;
                case 8: eliminarPasajero($viaje);break;
                case 9: ingresarPasajeros($viaje);break;
                case 10: echo $viaje->stringColeccion($viaje->getColPasajeros());break;
                case 11: echo $viaje->getRefResponsable();break;
                case 12: echo "Volviendo al menú principal...\n";break;
                default:echo "Elección inexistente, ingrese otra\n";break;
            }
        }while($eleccion!=12);
        
    }else{    
        echo "\n¡Viaje no encontrado!\n";
    }    
}

/**
 * función que modifica los datos del responsable de un viaje
 * @param object $viaje
 */
function modificarResponsable($viaje){

    $responsable = $viaje->getRefResponsable();

    $encontrado = $responsable->Buscar($responsable->getRnumeroempleado()); 

    if($encontrado == true){

    do{
        echo "------Ingrese que dato desea modificar del Responsable del viaje------\n"
            ."1) Número de Licencia.\n"
            ."2) Nombre.\n"
            ."3) Apellido.\n"
            ."4) Volver\n";
                
            echo "Ingrese su eleccion: ";
            $eleccion = trim(fgets(STDIN));
            
            switch($eleccion){
                case 1:echo "Ingrese número nuevo de Licencia: ";  
                                $numLicencia = trim(fgets(STDIN));
                                $responsable->setRnumerolicencia($numLicencia);
                                break;
                case 2:echo "Ingrese nombre nuevo: ";
                                $nombreNuevo = trim(fgets(STDIN));
                                $responsable->setRnombre($nombreNuevo);
                                break;
                case 3:echo "Ingrese apellido nuevo: ";
                                 $apellidoNuevo = trim(fgets(STDIN));
                                 $responsable->setRapellido($apellidoNuevo);
                                 break;
                case 4: "Volviendo al menú de modificar datos del viaje...\n";break;
                default:"Elección inexistente, ingrese otra";break;
                }
            }while($eleccion!=4);
            
        modificarBd($responsable);
        
        }else{    
            echo "\n¡Responsable no encontrado!\n";
        }    
    }


/**
 * función que modifica los datos de una pasajero en específico
 * @param object $datosViaje;
 */
function modificarDatosPasajero($datosViaje){
    echo "Ingrese documento del pasajero que desea modificar: ";
    $docu = trim(fgets(STDIN));
    $indicePasaj = $datosViaje->encontrarIndice($docu);

    $datosPasajero = new Pasajero();
    $encontrado = $datosPasajero->Buscar($docu); 

    if($encontrado == true){
        
        $datosPasajero = $datosViaje->getColPasajeros()[$indicePasaj];
        
        do{
            echo "------Ingrese que datos desea modificar del pasajero------\n"
                ."1) Nombre.\n"
                ."2) Apellido.\n"
                ."3) Documento.\n"
                ."4) Volver.\n";
            
            echo "Ingrese su eleccion: ";
            $eleccion = trim(fgets(STDIN));
        
            switch($eleccion){
                case 1:echo "Ingrese nombre nuevo del pasajero: ";
                            $nombreNuevo = trim(fgets(STDIN));
                            $datosPasajero->setPnombre($nombreNuevo);
                            
                            break;
                case 2:echo "Ingrese apellido nuevo del pasajero: ";  
                            $apellidoNuevo = trim(fgets(STDIN));
                            $datosPasajero->setPapellido($apellidoNuevo);
                            break;
                case 3:echo "Ingrese numero de documento nuevo del pasajero: ";
                            $docuNuevo = trim(fgets(STDIN));
                            $datosPasajero->setRdocumento($docuNuevo);
                            break;
                case 4: "Volviendo al menú de modificar la colección de pasajeros...\n";break;
                default:"Elección inexistente, ingrese otra";break;
            }
        }while($eleccion!=4);

        modificarBd($datosPasajero);
    }else{
        echo "\n!Pasajero no encontrado!\n";
    }
}

/**
 * función que le permite al usuario eliminar los datos de una empresa
 * @param object $empre
 */
function eliminarEmpresa($empre){
    echo "Ingrese numero de id de la empresa que desea eliminar: ";
    $id = trim(fgets(STDIN));
    $encontrada = $empre->Buscar($id); 

    if($encontrada ==true){
        
        eliminarBd($empre);

    }else{
        echo "\n¡Empresa no encontrada!\n";
    }
}

/**
 * función que le permite al usuario eliminar los datos de un Responsable de la Base de Datos
 * @param object $empre
 */
function eliminarResponsable(){
    $responsable = new Responsable();
    echo "Ingrese numero de empleado que desea eliminar: ";
    $num = trim(fgets(STDIN));
    $encontrado = $responsable->Buscar($num); 

    if($encontrado == true){
        
        eliminarBd($responsable);

    }else{
        echo "\n¡Responsable no encontrado!\n";
    }
}

/**
 * función que le permite al usuario eliminar los datos de un viaje
 * @param object $viaje
 */
function eliminarViaje($empresa){
    echo "Ingrese numero de id del viaje que desea eliminar: ";
    $id = trim(fgets(STDIN));
    $viaje = new Viaje();

    $encontrado = $viaje->Buscar($id); 

    if($encontrado ==true){
        eliminarBd($viaje);
        $empresa->eliminarViaje($id);
    }else{
        echo "\n¡Viaje no encontrado!\n";
    }
}

function verificarDestinoViaje($destino,$empresa){
    $viaje = new Viaje();
    $viajes = $viaje->listar();
    $valido = true;
    $i = 0;

    while($valido == true && $i< count($viajes)){
        if(strcasecmp($viajes[$i]->getVdestino(),$destino) == 0){
            $valido = false;
        }
        $i++;
    }

    return $valido;
}

/**
 * función que le permite al usuario eliminar los datos de un pasajero
 * @param object $viaje
 */
function eliminarPasajero($viaje){
    echo "Ingrese numero de documento del pasajero que desea eliminar: ";
    $docu = trim(fgets(STDIN));
    $pasajero = new Pasajero();
    
    $encontrado = $pasajero->Buscar($docu);

    if($encontrado == true){
        $viaje->eliminarPasajero($docu);
        eliminarBd($pasajero);
    }else{
        echo "¡Numero de documento de pasajero no encontrado!";
    }

}


/**
 * función que le permite al usuario insertar los datos de un objeto en la base de datos
 * @param object $objeto
 */
function insertarBd($objeto){    
    $respuesta = $objeto->insertar();
	// Inserto el OBj Viaje en la base de datos
	if ($respuesta==true) {
			echo "\nOP INSERCION: los datos fueron ingresados en la BD\n";
            
			listarBd($objeto);
	}else 
		echo $objeto->getmensajeoperacion();
}

/**
 * función que le permite al usuario modificar los datos de un objeto en la base de datos
 * @param object $objeto
 */
function modificarBd($objeto){
    $respuesta = $objeto->modificar();
	        
    if ($respuesta==true) {
        //Busco todas las Empresas almacenadas en la BD y veo la modificacion realizada
        echo " \nOP MODIFICACION: Los datos fueron actualizados correctamente\n";

        listarBd($objeto);

        }else
            echo $objeto->getmensajeoperacion();
}

/**
 * función que le permite al usuario eliminar los datos de un objeto en la base de datos
 * @param object $objeto
 */
function eliminarBd($objeto){
    $respuesta = $objeto->eliminar();
        if ($respuesta==true) {
        
            //Busco todas las Empresas almacenadas en la BD y veo la modificacion realizada
            echo " \nOP ELIMINACION: los datos fueron eliminados correctamente\n";
            listarBd($objeto);

        }else
            echo $objeto->getmensajeoperacion();
}

function listarBd($obj){

    $colObjetos =$obj->listar();

    foreach ($colObjetos as $unObjeto){
        echo $unObjeto;
        echo "-------------------------------------------------------\n";
    }
}

function revisarbdViajes($empresa){

    $viaje = new Viaje();
    $viajesbd = $viaje->listar();

    foreach($viajesbd as $unViaje){
        $idbd = $unViaje->getIdviaje();

        $indiceViaje = $empresa->encontrarIndice($idbd);

        if(!$indiceViaje && $unViaje->getEmpresa()->getIdempresa() == $empresa->getIdempresa()){
            $empresa->agregarViaje($unViaje);
            revisarbdPasajeros($unViaje);
        }
    }
}

function revisarbdPasajeros($viaje){

    $pasajero = new Pasajero();
    $pasajerosbd = $pasajero->listar();

    $coleccionPasajeros = $viaje->getColPasajeros();

    foreach($pasajerosbd as $unPasajero){
        $docubd = $unPasajero->getRdocumento();

        $indicePasajero = $viaje->encontrarIndice($docubd);

        if(!$indicePasajero && $unPasajero->getViaje()->getIdviaje() == $viaje->getIdviaje()){
            $viaje->venderPasaje($unPasajero);
           
        }
    }
}

?>