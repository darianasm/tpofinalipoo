<?php

include_once "BaseDatos.php";

class Pasajero{

    private $rdocumento;
    private $pnombre;
    private $papellido;
    private $ptelefono;
	private $viaje;
    private $mensajeoperacion;

    public function __construct(){
		
        $this->rdocumento = "";
        $this->pnombre = "";
        $this->papellido = "";
        $this->ptelefono = "";
		$this->viaje = "";

    }

    public function cargar($docu,$nombre,$apellido,$tlfn, $viaje){		
        $this->setRdocumento($docu);
        $this->setPnombre($nombre);
        $this->setPapellido($apellido);
        $this->setPtelefono($tlfn);
        $this->setViaje($viaje);
    }

    public function getRdocumento(){
        return $this->rdocumento;
    }
    public function setRdocumento($rdocumento){
        $this->rdocumento = $rdocumento;
    }

    public function getPnombre(){
        return $this->pnombre;
    }
    public function setPnombre($pnombre){
        $this->pnombre = $pnombre;
    }

    public function getPapellido(){
        return $this->papellido;
    }
    public function setPapellido($papellido){
        $this->papellido = $papellido;
    }

    public function getPtelefono(){
        return $this->ptelefono;
    }
    public function setPtelefono($ptelefono){
        $this->ptelefono = $ptelefono;
    }

    public function getmensajeoperacion(){
        return $this->mensajeoperacion;
    } 
    public function setmensajeoperacion($mensajeoperacion){
        $this->mensajeoperacion = $mensajeoperacion;
    }

	public function getViaje(){
		return $this->viaje;
	} 
	public function setViaje($viaje){
		$this->viaje = $viaje;
	}

    /**
	 * Recupera los datos de un pasajero por su docu
	 * @param int $docu
	 * @return true en caso de encontrar los datos, false en caso contrario 
	 */		
    public function Buscar($docu){
		$base=new BaseDatos();
		$consultaPasajero="select * from pasajero where rdocumento =".$docu;
		$resp = false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaPasajero)){
				if($row2=$base->Registro()){
				    $this->setRdocumento($docu);
					$this->setPnombre($row2['pnombre']);
					$this->setPapellido($row2['papellido']);
                    $this->setPtelefono($row2['ptelefono']);

                    $idViaje = $row2['idviaje'];
					$viaje = new Viaje();
					$viaje->buscar($idViaje);

					$this->setViaje($viaje);

					$resp= true;
				}				
			
		 	}	else {
		 			$this->setmensajeoperacion($base->getError());
		 		
			}
		}   else {
		 		$this->setmensajeoperacion($base->getError());
		 	
		    }		
		return $resp;
	}
    

    public function listar($condicion=""){
	    $arregloPasajero = null;
		$base=new BaseDatos();
		$consultaPasajeros ="select * from pasajero ";
		if ($condicion!=""){
		    $consultaPasajeros=$consultaPasajeros.' where '.$condicion;
		}
		$consultaPasajeros.=" order by rdocumento ";

		if($base->Iniciar()){
			if($base->Ejecutar($consultaPasajeros)){		
                
				$arregloPasajero = array();
				while($row2=$base->Registro()){
                    
				    $doc=$row2['rdocumento'];
                    $nom=$row2['pnombre'];
                    $ape=$row2['papellido'];
					$tel=$row2['ptelefono'];
					$via=$row2['idviaje'];
				
					$viaje = new Viaje();
					$viaje->buscar($via);

					$pasaj =new Pasajero();
					$pasaj->cargar($doc,$nom,$ape,$tel,$viaje);
					array_push($arregloPasajero,$pasaj);
	            }
				
			}	else {
		 		    $this->setmensajeoperacion($base->getError());		
			}
		}	else {
		 	    $this->setmensajeoperacion($base->getError());
		}	
		return $arregloPasajero;
	}

    public function insertar(){
		$base=new BaseDatos();
		$resp= false;
		$consultaInsertar="INSERT INTO Pasajero(rdocumento, pnombre, papellido, ptelefono, idviaje) 
				VALUES ('".$this->getRdocumento()."',
                        '".$this->getPnombre()."',
                        '".$this->getPapellido()."',
                        ".$this->getPtelefono().",
                        ".$this->getViaje()->getIdviaje().")";
		
		if($base->Iniciar()){

			if($base->Ejecutar($consultaInsertar)){

			    $resp=  true;

			}	else {
					$this->setmensajeoperacion($base->getError());
		
			}

		} else {
				$this->setmensajeoperacion($base->getError());
			
		}
		return $resp;
	}

    public function modificar(){
	    $resp =false; 
	    $base=new BaseDatos();
		$consultaModifica="UPDATE pasajero SET  pnombre='".$this->getPnombre()."' 
                                                , papellido='".$this->getPapellido()."'
                                                , ptelefono='".$this->getPtelefono()."' 
                                                , idviaje='".$this->getViaje()->getIdviaje()."'
                                                WHERE rdocumento =". $this->getRdocumento();
		if($base->Iniciar()){
			if($base->Ejecutar($consultaModifica)){
			    $resp=  true;
			}else{
				$this->setmensajeoperacion($base->getError());
				
			}
		}else{
				$this->setmensajeoperacion($base->getError());
			
		}
		return $resp;
	}
	
	public function eliminar(){
		$base=new BaseDatos();
		$resp=false;
		if($base->Iniciar()){
				$consultaBorra="DELETE FROM pasajero WHERE rdocumento=".$this->getRdocumento();
				if($base->Ejecutar($consultaBorra)){
				    $resp=  true;
				}else{
						$this->setmensajeoperacion($base->getError());	
				}
		}else{
				$this->setmensajeoperacion($base->getError());
			
		}
		return $resp; 
	}

    public function __toString(){
        return "\nDatos del Pasajero: \nDocumento: ".$this->getRdocumento().
               "\nNombre: ".$this->getPnombre().
               "\nApellido: ".$this->getPapellido().
               "\nTeléfono: ".$this->getPtelefono().
               "\nID del viaje al que pertenece: ".$this->getViaje()->getIdviaje()."\n";
    }

	
	
}