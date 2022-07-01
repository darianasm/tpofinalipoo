<?php

include_once "BaseDatos.php";

class Empresa{

    private $idempresa;
    private $enombre;
    private $edireccion;
	private $colViajes;
    private $mensajeoperacion;

    public function __construct(){
		
        $this->idempresa = "";
        $this->enombre = "";
        $this->edireccion = "";
		$this->colViaje = [];
    }

    public function cargar($id,$nom,$dire){		
        $this->setIdempresa($id);
        $this->setEnombre($nom);
        $this->setEdireccion($dire);
		$this->setColViajes([]);
    }


    public function getIdempresa(){
        return $this->idempresa;
    }
    public function setIdempresa($idempresa){
    $this->idempresa = $idempresa;
    }

    public function getEnombre(){
        return $this->enombre;
    }
    public function setEnombre($enombre){
        $this->enombre = $enombre;
    }

    public function getEdireccion(){
        return $this->edireccion;
    }
    public function setEdireccion($edireccion){
        $this->edireccion = $edireccion;
    }

    public function getmensajeoperacion(){
        return $this->mensajeoperacion;
    }
    public function setmensajeoperacion($mensajeoperacion){
        $this->mensajeoperacion = $mensajeoperacion;
    }

	public function getColViajes(){
		return $this->colViajes;
	}
	public function setColViajes($colViajes){
		$this->colViajes = $colViajes;
	}
    /**
	 * Recupera los datos de una empresa por id
	 * @param int $id
	 * @return true en caso de encontrar los datos, false en caso contrario 
	 */		
    public function Buscar($id){
		$base=new BaseDatos();
		$consultaEmpresa="select * from empresa where idempresa =".$id;
		$resp= false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaEmpresa)){
				if($row2=$base->Registro()){
				    $this->setIdempresa($id);
					$this->setEnombre($row2['enombre']);
					$this->setEdireccion($row2['edireccion']);

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
	    $arregloEmpresa = null;
		$base=new BaseDatos();
		$consultaEmpresas ="select * from empresa ";
		if ($condicion!=""){
		    $consultaEmpresas=$consultaEmpresas.' where '.$condicion;
		}
		$consultaEmpresas.=" order by idempresa ";

		if($base->Iniciar()){
			if($base->Ejecutar($consultaEmpresas)){		
                
				$arregloEmpresa = array();
				while($row2=$base->Registro()){
				    $id=$row2['idempresa'];
					$nom=$row2['enombre'];
					$dire=$row2['edireccion'];
				
					$empre =new Empresa();
					$empre->cargar($id,$nom,$dire);
					array_push($arregloEmpresa,$empre);
	            }
				
			}	else {
		 		    $this->setmensajeoperacion($base->getError());		
			}
		}	else {
		 	    $this->setmensajeoperacion($base->getError());
		}	
		return $arregloEmpresa;
	}

    public function insertar(){
		$base=new BaseDatos();
		$resp= false;
		$consultaInsertar="INSERT INTO empresa(enombre, edireccion) 
				VALUES ('".$this->getEnombre()."','".$this->getEdireccion()."')";
		
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
		$consultaModifica="UPDATE empresa SET enombre='".$this->getEnombre()."',edireccion='".$this->getEdireccion()."' WHERE idempresa=". $this->getIdempresa();
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
				$consultaBorra="DELETE FROM empresa WHERE idempresa=".$this->getIdempresa();
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
	    return "--------Datos de la empresa--------\nId: ".$this->getIdempresa(). "\nNombre: ".$this->getEnombre().
			"\nDirección: ".$this->getEdireccion()."\n";		
	}

	 /**
     * agregar un arreglo de un viaje a una colección de viajes
     * modifica el arreglo de viajes anterior
     * @param object $viaje
     */
    public function agregarViaje($viaje){
		$viajes = $this->getColViajes();
		array_push($viajes,$viaje);
		
		$this->setColViajes($viajes);
	}

	/**
	 * crea una nueva colección omitiendo al viaje según su id
 	* modifica la colección
 	* @param int $id
 	*/
	public function eliminarViaje($id){
    	$viajes = $this->getColViajes();
    	$viajeNuevo = [];
    	$j = 0;
    	for($i = 0; $i< count($viajes);$i++){
        	if($viajes[$i]->getIdviaje() != $id){
        		$viajeNuevo[$j] = $viajes[$i];
        		$j++;
        	}
    	}
    	$this->setColViajes($viajeNuevo);
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
     * encuentra el indice en donde se encuentra el viaje en el array según su id
     * si no encuentra el indice retorta null
     * @param int $id
     * @return boolean $encontrado;
     */
    public function encontrarIndice($id){
		$viajes = $this->getColViajes();
		$encontrado = null;
		$i=0;
		while($i<count($viajes) && !$encontrado){
			if($viajes[$i]->getIdviaje() == $id){
				$encontrado = $i;
			}
			$i++;
		}
		return $encontrado;
	}

	/**
     * encuentra el indice en donde se encuentra el responsable en el array de viajes según su num
     * si no encuentra el indice retorta null
     * @param int $num
     * @return boolean $encontrado;
     */
    public function encontrarIndiceResponsable($num){
		$viajes = $this->getColViajes();
		$encontrado = null;
		$i=0;
		while($i<count($viajes) && $encontrado == null){
			if($viajes[$i]->getRefResponsable()->getRnumeroempleado() == $num){
				$encontrado = $i;
			}
			$i++;
		}
		return $encontrado;
	}
	
}

?>