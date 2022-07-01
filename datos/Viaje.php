<?php

include_once "BaseDatos.php";

class Viaje{
    
    private $idviaje;
    private $vdestino;
    private $vcantmaxpasajeros;
    private $empresa;
    private $refResponsable;
    private $vimporte;
    private $tipoAsiento;/*primera clase o no, semicama o cama*/
    private $idayvuelta;/*si no*/
    private $colPasajeros;
    private $mensajeoperacion;

    // Metodo constructor de la clase Viaje
    public function  __construct(){   
        $this->idviaje = "";
        $this->vdestino = "";    
        $this->vcantmaxpasajeros = "";    
        $this->empresa = "";
        $this->refResponsable = "";     
        $this->vimporte = "";  
        $this->tipoAsiento = "";   
        $this->idayvuelta = ""; 

    }

    public function cargar($id,$destino,$cantmaxpasajeros,$empresa, $responsable, $importe, $asiento, $idavuelta){	
        $this->setIdviaje($id);	
        $this->setVdestino($destino);
        $this->setVcantmaxpasajeros($cantmaxpasajeros); 
        $this->setEmpresa($empresa);
        $this->setRefResponsable($responsable);
        $this->setVimporte($importe);
        $this->setTipoAsiento($asiento);
        $this->setIdayvuelta($idavuelta);
       
        $this->setColPasajeros([]);
    }

    
    public function getIdviaje(){
        return $this->idviaje;
    }
    public function setIdviaje($idviaje){
        $this->idviaje = $idviaje;
    }
    
    public function getVdestino(){
        return $this->vdestino;
    }
    public function setVdestino($vdestino){
        $this->vdestino = $vdestino;
    }
    
    public function getVcantmaxpasajeros(){
        return $this->vcantmaxpasajeros;
    }
    public function setVcantmaxpasajeros($vcantmaxpasajeros){
        $this->vcantmaxpasajeros = $vcantmaxpasajeros;
    }

    public function getVimporte(){
        return $this->vimporte;
    }
    public function setVimporte($vimporte){
        $this->vimporte = $vimporte;
    }

    public function getTipoAsiento(){
        return $this->tipoasiento;
    }
    public function setTipoAsiento($tipoasiento){
        $this->tipoasiento = $tipoasiento;
    }

    public function getIdayvuelta(){
        return $this->idayvuelta;
    }
    public function setIdayvuelta($idayvuelta){
        $this->idayvuelta = $idayvuelta;
    }

    public function getMensajeoperacion(){
        return $this->mensajeoperacion;
    }
    public function setMensajeoperacion($mensajeoperacion){
        $this->mensajeoperacion = $mensajeoperacion;
    }

    public function getRefResponsable(){
        return $this->refResponsable;
    }
    public function setRefResponsable($refResponsable){
        $this->refResponsable = $refResponsable;
    }

    public function getColPasajeros(){
        return $this->colPasajeros;
    } 
    public function setColPasajeros($colPasajeros){
        $this->colPasajeros = $colPasajeros;
    }

    public function getEmpresa(){
        return $this->empresa;
    }
    public function setEmpresa($empresa){
        $this->empresa = $empresa;
    }
    
    /**
	 * Recupera los datos de un viaje por su id
	 * @param int $id
	 * @return true en caso de encontrar los datos, false en caso contrario 
	 */		
    public function Buscar($id){
		$base=new BaseDatos();
		$consultaViaje="select * from viaje where idviaje =".$id;
		$resp= false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaViaje)){
				if($row2=$base->Registro()){
				    $this->setIdviaje($id);
					$this->setVdestino($row2['vdestino']);
					$this->setVcantmaxpasajeros($row2['vcantmaxpasajeros']);

                    $idEmpresa = $row2['idempresa'];
                    $empresa = new Empresa();
                    $empresa->buscar($idEmpresa);
                    $this->setEmpresa($empresa);

                    $numRespon = $row2['rnumeroempleado'];
                    $respon = new Responsable();
                    $respon->buscar($numRespon);
                    $this->setRefResponsable($respon);

					$this->setVimporte($row2['vimporte']);
					$this->setTipoAsiento($row2['tipoAsiento']);
                    $this->setIdayvuelta($row2['idayvuelta']);
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
	    $arregloViaje = null;
		$base= new BaseDatos();
		$consultaViajes ="select * from viaje ";
		if ($condicion!=""){
		    $consultaViajes=$consultaViajes.' where '.$condicion;
		}
		$consultaViajes.=" order by idviaje ";

		if($base->Iniciar()){
			if($base->Ejecutar($consultaViajes)){		
                
				$arregloViaje = array();
				while($row2=$base->Registro()){
                    
				    $id = $row2['idviaje'];
                    $dest = $row2['vdestino'];
					$maxpas = $row2['vcantmaxpasajeros'];
					$empre = $row2['idempresa'];
                    $numeple = $row2['rnumeroempleado'];
                    $import = $row2['vimporte'];
					$asien = $row2['tipoAsiento'];
					$idayvuel = $row2['idayvuelta'];
				
                    $objEmpresa = new Empresa();
                    $objEmpresa->buscar($empre);

                    $objResponsable = new Responsable();
                    $objResponsable->buscar($numeple);

					$via = new Viaje();
					$via->cargar($id,$dest,$maxpas,$objEmpresa,$objResponsable,$import,$asien,$idayvuel);
					array_push($arregloViaje,$via);
	            }
				
			}	else {
		 		    $this->setmensajeoperacion($base->getError());		
			}
		}	else {
		 	    $this->setmensajeoperacion($base->getError());
		}	
		return $arregloViaje;
	}

    public function insertar(){
		$base=new BaseDatos();
		$resp= false;
		$consultaInsertar="INSERT INTO viaje(vdestino, vcantmaxpasajeros, idempresa, rnumeroempleado, vimporte, tipoAsiento, idayvuelta) 
				VALUES ('".$this->getVdestino()."','".$this->getVcantmaxpasajeros()."','".$this->getEmpresa()->getIdempresa()."','".$this->getRefResponsable()->getRnumeroempleado()."','".$this->getVimporte()."','".$this->getTipoAsiento()."','".$this->getIdayvuelta()."')";
		
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
		$consultaModifica="UPDATE viaje SET vdestino='".$this->getVdestino()."'
                                                , vcantmaxpasajeros='".$this->getVcantmaxpasajeros()."' 
                                                , idempresa='".$this->getEmpresa()->getIdempresa()."' 
                                                , rnumeroempleado='".$this->getRefResponsable()->getRnumeroempleado()."' 
                                                , vimporte='".$this->getVimporte()."' 
                                                , tipoasiento='".$this->getTipoAsiento()."' 
                                                , idayvuelta='".$this->getIdayvuelta()."' 
                                                WHERE idviaje =". $this->getIdviaje();
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
				$consultaBorra="DELETE FROM viaje WHERE idviaje=".$this->getIdviaje();
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

    
/**
 * crea una nueva colección omitiendo al pasajero según su nro de deocumento
 * modifica la colección
 * @param int $docu
 */
public function eliminarPasajero($docu){
    $pasajeros = $this->getColPasajeros();
    $pasajeroNuevo = [];
    $j = 0;
    for($i = 0; $i< count($pasajeros);$i++){
        if($pasajeros[$i]->getRdocumento() != $docu){
        $pasajeroNuevo[$j] = $pasajeros[$i];
        $j++;
        }
    }
    $this->setColPasajeros($pasajeroNuevo);
    }
    
    /**
 	* Metodo que convierte un arreglo es string
 	* @param array $coleccion;
 	* @return string $string;
 	*/
	public function stringColeccion($coleccion){
		$string = "";
		for($i = 0; $i < count($coleccion); $i++){
			$string = $string.$coleccion[$i]."\n";
		}
		return $string;
	}
    
    /**
     * agregar un arreglo aosciativo de un pasajero a una colección de pasajeros
     * modifica el arreglo de pasajeros anterior
     * @param object $pasajero
     */
    public function agregarPasajero($pasajero){
    $pasajeros = $this->getColPasajeros();
    array_push($pasajeros,$pasajero);
    $this->setColPasajeros($pasajeros);
    }
    
    /**
     * encuentra el indice en donde se encuentra el pasajero en el array según su nro de documento
     * si no encuentra el indice retorta null
     * @param int $docu
     * @return int $encontrado;
     */
    public function encontrarIndice($docu){
    $pasajeros = $this->getColPasajeros();
    
    $encontrado = null;
    $i=0;
    while($i<count($pasajeros) && $encontrado == null){
    if($pasajeros[$i]->getRdocumento() == $docu){
    $encontrado = $i;
    }
    $i++;
    }
    return $encontrado;
    }
    
    //Metodo para convertir en string
    public function __toString(){
        return "Viaje: ".$this->getIdviaje().
               "\nDestino del viaje: ".$this->getVdestino().
               "\nId de la empresa a la que pertenece: ".$this->getEmpresa()->getIdempresa().
               "\nLa cantidad maxima de pasajeros es de: ".$this->getVcantmaxpasajeros().
               "\nEl numero de empleado de responsable del viaje es: \n".$this->getRefResponsable()->getRnumeroempleado().
               "\nEl importe del viaje es: ".$this->getVimporte().
               "\nEl viaje es de ida y vuelta: ".$this->getIdayvuelta()."\n";
    }
    
    public function venderPasaje($pasajero){
    $idaVuel = $this->getIdayvuelta();
    $multiplicar = null;
    $importeBase = null;
    
    if($this->hayPasajesDisponibles()){
        $this->agregarPasajero($pasajero);
        $importeBase = $this->getVimporte();
        if($idaVuel == "si"){
            $importeBase += ($importeBase*50)/100;
        }

        $this->setVimporte($importeBase);
    
    }
    return $importeBase;
    }
    
    public function hayPasajesDisponibles(){
    $cantPasajeros = count($this->getColPasajeros());
    $maximaCant = $this->getVcantmaxpasajeros();
    $disponible = false;
    
    if($cantPasajeros < $maximaCant){
    $disponible = true;
    }
    return $disponible;
    }

	


    

    
    


    

    
    
}

?>