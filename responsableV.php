<?php
class ResponsableV{
    private $nroEmpleado;
    private $nroLicencia;
    private $nombre;
    private $apellido;
    private $viajes;
    private $mensajeOperacion;

    public function __construct()
    {
        $this->nroEmpleado = "";
        $this->nroLicencia = "";
        $this->nombre = "";
        $this->apellido = ""; 
        $this->viajes = [];
    }

    public function cargarResponsable($nroEmpleado, $nroLicencia, $nombre, $apellido, $viajes = [])
    {
        $this->setNroEmpleado($nroEmpleado);
        $this->setNroLicencia($nroLicencia);
        $this->setNombre($nombre);
        $this->setApellido($apellido);
        $this->setViajes($viajes);
       
    }

    public function getNroEmpleado()
    {
        return $this->nroEmpleado;
    }

    public function getNroLicencia()
    {
        return $this->nroLicencia;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getApellido()
    {
        return $this->apellido;
    }

    public function getViajes()
    {
        return $this->viajes;
    }

    public function getMensajeOperacion()
    {
        return $this->mensajeOperacion;
    }

    public function setNroEmpleado($nroEmpleado)
    {
        $this->nroEmpleado = $nroEmpleado;
    }

    public function setNroLicencia($nroLicencia)
    {
        $this->nroLicencia = $nroLicencia;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }

    public function setViajes($viajes)
    {
        $this->viajes = $viajes;
    }

    public function setMensajeOperacion($mensajeOperacion)
    {
        $this->mensajeOperacion = $mensajeOperacion;
    }

    public static function listar($condicion=""){
        $arregloResponsable = array();
        $base=new BaseDatos();
        $consultaResponsable="SELECT * FROM responsable ";
        if($condicion!=""){
            $consultaResponsable=$consultaResponsable.' WHERE '.$condicion;
        }
        if($base->Iniciar()){
            if($base->Ejecutar($consultaResponsable)){
                $arregloResponsable = array();
                while($response=$base->Registro()){
                    $responsableV=new ResponsableV();
                    $responsableV->cargarResponsable($response['rnumeroempleado'], $response['rnumerolicencia'], $response['rnombre'], $response['rapellido']);
                    array_push($arregloResponsable, $responsableV);
                }
            }else{
                $this->setMensajeOperacion($base->getError());
            }
        }else{
            $this->setMensajeOperacion($base->getError());
        }
        return $arregloResponsable;
    }

    public function buscar(){
        $base=new BaseDatos();
        $consultaResponsable="SELECT * FROM responsable WHERE rnumeroempleado=".$this->getNroEmpleado();
        if($this->getNroEmpleado()==null){
            $this->setMensajeOperacion($base->getError());
        }
        if($base->Iniciar()){
            if($base->Ejecutar($consultaResponsable)){
              if($response=$base->Registro()){
                $this->cargarResponsable($response['rnumeroempleado'], $response['rnumerolicencia'], $response['rnombre'], $response['rapellido']);
              }
            }else{
                $this->setMensajeOperacion($base->getError());
            }
        }else{
            $this->setMensajeOperacion($base->getError());
        }
    }

    public function insertar(){
        $base=new BaseDatos();
        $consultaResponsable="INSERT INTO responsable(rnumerolicencia,rnombre,rapellido)  
        VALUES('".$this->getNroLicencia()."','".$this->getNombre()."','".$this->getApellido()."')";
        if(!$base->Iniciar()){
            $this->setMensajeOperacion($base->getError());
        }
        if($id = $base->devuelveIDInsercion($consultaResponsable)){
            $this->setNroEmpleado($id);
            return true;
        }else{
            $this->setMensajeOperacion($base->getError());
        }
    }

    public function actualizar(){
        $base=new BaseDatos();
        $consultaResponsable="UPDATE responsable SET rnumerolicencia='".$this->getNroLicencia()."',rnombre='".$this->getNombre()."',rapellido='".$this->getApellido()."' 
        WHERE rnumeroempleado=".$this->getNroEmpleado();
        if(!$base->Iniciar()){
            $this->setMensajeOperacion($base->getError());
        }
        if($base->Ejecutar($consultaResponsable)){
            return true;
        }else{
            $this->setMensajeOperacion($base->getError());
        }
    }


    public function eliminar(){
        $base=new BaseDatos();
        $consultaResponsable="DELETE FROM responsable WHERE rnumeroempleado=".$this->getNroEmpleado();
        if(!$base->Iniciar()){
            $this->setMensajeOperacion($base->getError());
        }
        if($base->Ejecutar($consultaResponsable)){
            return true;
        }else{
            $this->setMensajeOperacion($base->getError());
        }
    }

    public function mostrarViajes(){
        $arrViajes = $this->getViajes();
        $strViajes = "\nViajes del responsable:\n";
        foreach($arrViajes as $viaje){
            $strViajes .= 
            "\nID: " . $viaje->getId() . "\nDestino: " . $viaje->getDestino() . "\nCant Pasajeros Confirmados/Maximo: " . count($viaje->getPasajeros()) . "/" . $viaje->getCantMaxPasajeros() . "\nImporte: $" . $viaje->getImporte() ."\n";
        }
        return $strViajes;
    }

    public function __toString(){
        return 
        "\nResponsable: ". $this->getApellido().", ".$this->getNombre().
        "\nNro. Empleado: ". $this->getNroEmpleado(). 
        "\nNro. Licencia: ". $this->getNroLicencia()
        . (count($this->getViajes())>0 ? $this->mostrarViajes() : "")."\n";
    }
}