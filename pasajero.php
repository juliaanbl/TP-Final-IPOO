<?php
include_once 'ResponsableV.php';
include_once 'Viaje.php';

class Pasajero {
    private $nombre;
    private $apellido;
    private $dni;
    private $telefono;
    private $viaje;
    private $mensajeOperacion;

    public function __construct(){
        $this->nombre = "";
        $this->apellido = "";
        $this->dni = "";
        $this->telefono = "";
        $this->viaje = new Viaje();
    }

    public function getNombre(){
        return $this->nombre;
    }

    public function getApellido(){
        return $this->apellido;
    }

    public function getDni(){
        return $this->dni;
    }

    public function getTelefono(){
        return $this->telefono;
    }

    public function getViaje(){
        return $this->viaje;
    }

    public function getMensajeOperacion(){
        return $this->mensajeOperacion;
    }

    public function setNombre($nombre){
        $this->nombre = $nombre;
    }

    public function setApellido($apellido){
        $this->apellido = $apellido;
    }

    public function setDni($dni){
        $this->dni = $dni;
    }

    public function setTelefono($telefono){
        $this->telefono = $telefono;
    }

    public function setViaje($viaje){
        $this->viaje = $viaje;
    }

    public function setMensajeOperacion($mensajeOperacion){
        $this->mensajeOperacion = $mensajeOperacion;
    }

    public function cargarPasajero($nombre, $apellido, $dni, $telefono, $viaje)
    {
        $this->setNombre($nombre);
        $this->setApellido($apellido);
        $this->setDni($dni);
        $this->setTelefono($telefono);
        $this->setViaje($viaje);
    }

    public static function listar($cond = ""){
        $base = new BaseDatos();
        $arregloPasajeros = array();
        $consultaPasajero = "SELECT * FROM pasajero";
        if($cond != ""){
            $consultaPasajero = $consultaPasajero . " WHERE " . $cond;
        }
        if($base->Iniciar()){
            if($base->Ejecutar($consultaPasajero)){
                $arregloPasajeros = array();
                while($row = $base->Registro()){
                    $pasajero = new Pasajero();
                    $viaje = new Viaje();
                    //Cargo el id de viaje pero no el objeto completo para no crear un bucle.
                    $viaje->setId($row['idviaje']);
                    $pasajero->cargarPasajero($row['pnombre'], $row['papellido'], $row['pdocumento'], $row['ptelefono'], $viaje);
                    array_push($arregloPasajeros, $pasajero);
                }
            } else{
                $this->setMensajeOperacion($base->getError());
            }
        }else{
            $this->setMensajeOperacion($base->getError());
        }
        return $arregloPasajeros;
    }

    public function buscar(){
        $response = false;
        $base = new BaseDatos();
        $consultaPasajero = "SELECT * FROM pasajero WHERE pdocumento = " . $this->getDni();
        if($base->Iniciar()){
            if($base->Ejecutar($consultaPasajero)){
                if($row = $base->Registro()){
                    $viaje = new Viaje();
                    $viaje->setId($row['idviaje']);
                    $this->cargarPasajero($row['pnombre'], $row['papellido'], $row['pdocumento'], $row['ptelefono'], $viaje);
                    $response = true;
                } else{
                    throw new Exception("No se encontró el pasajero con DNI: " . $this->getDni());
                }
            } else{
                $this->setMensajeOperacion($base->getError());
            }
        }else{
            $this->setMensajeOperacion($base->getError());
        }
        return $response;
    }

    public function insertar(){
        $base = new BaseDatos();
        if($this->getViaje()->getId() == null){
            throw new Exception("El viaje no está seteado.");
        }
        $consultaPasajero = " INSERT INTO pasajero (pdocumento,pnombre,papellido,ptelefono,idviaje) 
        VALUES ('".$this->getDni()."','".$this->getNombre()."','".$this->getApellido()."','".$this->getTelefono()."','".$this->getViaje()->getId()."')";
        $response = false;
        if($base->Iniciar()){
            if($base->Ejecutar($consultaPasajero)){
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
        $consultaPasajero = "UPDATE pasajero SET pnombre = '" . $this->getNombre() . "', papellido = '" . $this->getApellido() . "', ptelefono = '" . $this->getTelefono() ."', idviaje = '".$this->getViaje()->getId(). "' WHERE pdocumento = '" . $this->getDni() . "'";
        $response = false;
        if($base->Iniciar()){
            if($base->Ejecutar($consultaPasajero)){
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
        $consultaPasajero = "DELETE FROM pasajero WHERE pdocumento = '" . $this->getDni() . "'";
        $response = false;
        if($base->Iniciar()){
            if($base->Ejecutar($consultaPasajero)){
                $response = true;
            } else{
                $this->setMensajeOperacion($base->getError());
            }
        }else{
            $this->setMensajeOperacion($base->getError());
        }
        return $response;
    }

    public function __toString()
    {
        return 
        "\nPasajero: ". $this->getApellido().", ".$this->getNombre().
        "\nDNI: " . $this->getDni(). 
        "\nTelefono: " . $this->getTelefono()."\n";
    }
}