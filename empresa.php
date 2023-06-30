<?php
class Empresa{
    private $id;
    private $nombre;
    private $direccion;
    private $viajes;
    private $mensajeOperacion;

    public function __construct(){
        $this->id = "";
        $this->nombre = "";
        $this->direccion = "";
        $this->viajes = array();
    }

    public function cargarEmpresa($id, $nombre, $direccion){
        $this->setId($id);
        $this->setNombre($nombre);
        $this->setDireccion($direccion);
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getId(){
        return $this->id;
    }

    public function setNombre($nombre){
        $this->nombre = $nombre;
    }

    public function getNombre(){
        return $this->nombre;
    }

    public function setDireccion($direccion){
        $this->direccion = $direccion;
    }

    public function getDireccion(){
        return $this->direccion;
    }

    public function setViajes($viajes){
        $this->viajes = $viajes;
    }

    public function getViajes(){
        return $this->viajes;
    }

    public function setMensajeOperacion($mensajeOperacion){
        $this->mensajeOperacion = $mensajeOperacion;
    }

    public function getMensajeOperacion(){
        return $this->mensajeOperacion;
    }

    public static function listar($cond = ""){
        $base=new BaseDatos();
        $consultaEmpresa="SELECT * FROM empresa";
        if ($cond!=""){
            $consultaEmpresa.=" WHERE ".$cond;
        }
        $arregloEmpresas = array();
        if($base->Iniciar()){
            if($base->Ejecutar($consultaEmpresa)){
                while($row=$base->Registro()){
                    $empresa = new Empresa();
                    $empresa->cargarEmpresa($row['idempresa'],$row['enombre'],$row['edireccion']);
                    array_push($arregloEmpresas,$empresa);
                }
            }  
        }else{
            $this->setMensajeOperacion($base->getError());
        }
        return $arregloEmpresas;
    }

    public function buscar(){
        $base=new BaseDatos();
        $consultaEmpresa="SELECT * FROM empresa WHERE idempresa=".$this->getId();
        if($this->getId()==null){
            $this->setMensajeOperacion($base->getError());
        }
        if($base->Iniciar()){
            if($base->Ejecutar($consultaEmpresa)){
                $row=$base->Registro();
                $this->setNombre($row['enombre']);
                $this->setDireccion($row['edireccion']);
            }  
        }else{
            $this->setMensajeOperacion($base->getError());
        }   
    }

    public function insertar(){
        $base = new BaseDatos();
        $consultaEmpresa = " INSERT INTO empresa (enombre,edireccion)
        VALUES ('".$this->getNombre()."','".$this->getDireccion()."')";
        $response = false;
        if($base->Iniciar()){
            if($id = $base->devuelveIDInsercion($consultaEmpresa)){
                $this->setId($id);
                $response = true;
            } else{
                $this->setMensajeOperacion($base->getError());
            }
        }else{
            $this->setMensajeOperacion($base->getError());
        }
        return $response;
    }

    public function actualizar(){
        $base = new BaseDatos();
        $consultaEmpresa = "UPDATE empresa SET enombre='".$this->getNombre()."', edireccion='".$this->getDireccion()."' WHERE idempresa=".$this->getId();
        $response = false;
        if($base->Iniciar()){
            if($base->Ejecutar($consultaEmpresa)){
                $response = true;
            } else{
                $this->setMensajeOperacion($base->getError());
            }
        }else{
            $this->setMensajeOperacion($base->getError());
        }
        return $response;
    }

    public function eliminar(){
        $base = new BaseDatos();
        $consultaEmpresa = "DELETE FROM empresa WHERE idempresa=".$this->getId();
        $response = false;
        if($base->Iniciar()){
            if($base->Ejecutar($consultaEmpresa)){
                $response = true;
            } else{
                $this->setMensajeOperacion($base->getError());
            }
        }else{
            $this->setMensajeOperacion($base->getError());
        }
        return $response;
    }

    public function mostrarViajes(){
        $arrViajes = $this->getViajes();
        $strViajes = "";
        foreach($arrViajes as $viaje){
            $strViajes .= "\nID: " . $viaje->getId() . "\nDestino: " . $viaje->getDestino() . "\nCant Pasajeros Confirmados/Maximo: " . count($viaje->getPasajeros()) . "/" . $viaje->getCantMaxPasajeros() . "\nImporte: $" . $viaje->getImporte()."\n".$viaje->getResponsableV() . "\n";;
        }
        return $strViajes;
    }

    public function __toString(){
        return 
        "\nID:".$this->getId().
        "\nNombre:".$this->getNombre().
        "\nDireccion: " . $this->getDireccion(). 
        "\nViajes:\n" . $this->mostrarViajes();
    }
}