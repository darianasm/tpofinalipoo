<?php

include_once "BaseDatos.php";

class Responsable{

    private $rnumeroempleado;
    private $rnumerolicencia;
    private $rnombre;
    private $rapellido;
    private $mensajeoperacion;

    public function __construct(){
		
        $this->rnumeroempleado = "";
        $this->rnumerolicencia = "";
        $this->rnombre = "";
        $this->rapellido = "";
    
    }

    public function cargar($numeroEmpleado,$numeroLicencia,$nombre,$apellido){		
        $this->setRnumeroempleado($numeroEmpleado);
        $this->setRnumerolicencia($numeroLicencia);
        $this->setRnombre($nombre);
        $this->setRapellido($apellido);
    }


    public function getRnumeroempleado(){
        return $this->rnumeroempleado;
    }
    public function setRnumeroempleado($rnumeroempleado){
        $this->rnumeroempleado = $rnumeroempleado;
    }

    
    public function getRnumerolicencia(){
        return $this->rnumerolicencia;
    }
    public function setRnumerolicencia($rnumerolicencia){
        $this->rnumerolicencia = $rnumerolicencia;

        return $this;
    }

    public function getRnombre(){
        return $this->rnombre;
    }
    public function setRnombre($rnombre){
        $this->rnombre = $rnombre;
    }

    public function getRapellido(){
        return $this->rapellido;
    }
    public function setRapellido($rapellido){
        $this->rapellido = $rapellido;
    }

    public function getmensajeoperacion(){
        return $this->mensajeoperacion;
    }
    public function setmensajeoperacion($mensajeoperacion){
        $this->mensajeoperacion = $mensajeoperacion;
    }

    /**
	 * Recupera los datos de una responsable por su numero
	 * @param int $numeroEmpleado
	 * @return true en caso de encontrar los datos, false en caso contrario 
	 */		
    public function Buscar($numeroEmpleado){
		$base=new BaseDatos();
		$consultaResponsable="select * from responsable where rnumeroempleado =".$numeroEmpleado;
		$resp= false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaResponsable)){
				if($row2=$base->Registro()){
				    $this->setRnumeroempleado($numeroEmpleado);
					$this->setRnumerolicencia($row2['rnumerolicencia']);
					$this->setRnombre($row2['rnombre']);
                    $this->setRapellido($row2['rapellido']);
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
	    $arregloResponsable = null;
		$base=new BaseDatos();
		$consultaResponsables ="select * from responsable ";
		if ($condicion!=""){
		    $consultaResponsables=$consultaResponsables.' where '.$condicion;
		}
		$consultaResponsables.=" order by rnumeroempleado ";

		if($base->Iniciar()){
			if($base->Ejecutar($consultaResponsables)){		
                
				$arregloResponsable = array();
				while($row2=$base->Registro()){
                    
				    $numE=$row2['rnumeroempleado'];
                    $numL=$row2['rnumerolicencia'];
					$nom=$row2['rnombre'];
					$ape=$row2['rapellido'];
				
					$respon =new Responsable();
					$respon->cargar($numE,$numL,$nom,$ape);
					array_push($arregloResponsable,$respon);
	            }
				
			}	else {
		 		    $this->setmensajeoperacion($base->getError());		
			}
		}	else {
		 	    $this->setmensajeoperacion($base->getError());
		}	
		return $arregloResponsable;
	}

    public function insertar(){
		$base=new BaseDatos();
		$resp= false;
		$consultaInsertar="INSERT INTO responsable(rnumeroempleado, rnumerolicencia, rnombre, rapellido) 
				VALUES (".$this->getRnumeroempleado().",'".$this->getRnumerolicencia()."','".$this->getRnombre()."','".$this->getRapellido()."')";
		
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
		$consultaModifica="UPDATE responsable SET rnumerolicencia='".$this->getRnumerolicencia()."'
                                                , rnombre='".$this->getRnombre()."' 
                                                , rapellido='".$this->getRapellido()."' 
                                                WHERE rnumeroempleado =". $this->getRnumeroempleado();
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
				$consultaBorra="DELETE FROM responsable WHERE rnumeroempleado=".$this->getRnumeroempleado();
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
        return "\nDatos del Responsable: \nNúmero de Empleado: ".$this->getRnumeroempleado().
               "\nNúmero de Licencia: ".$this->getRnumerolicencia().
               "\nNombre: ".$this->getRnombre().
               "\nApellido: ".$this->getRapellido()."\n";
    }


}

?>