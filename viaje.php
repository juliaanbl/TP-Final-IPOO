<?php
class Viaje{
    private $Id;
    private $destino;
    private $cantMaxPasajeros;
    private $empresa;
    private $responsableV;
    private $importe;
    private $pasajeros;
    private $mensajeOperacion;



    public function __construct()
    {
        $this->Id = null;
        $this->destino = "";
        $this->cantMaxPasajeros = 0;
        $this->responsableV = new ResponsableV();
        $this->empresa = new Empresa();
        $this->importe = 0;
        $this->pasajeros = array();
    }


    public function cargarViaje($Id, $destino, $cantMaxPasajeros, $empresa, $responsableV, $importe, $pasajeros)
    {
        $this->setId($Id);
        $this->setDestino($destino);
        $this->setCantMaxPasajeros($cantMaxPasajeros);
        $this->setEmpresa($empresa);
        $this->setResponsableV($responsableV);
        $this->setImporte($importe);
        $this->setPasajeros($pasajeros);
    }

    public function getId()
    {
        return $this->Id;
    }

    public function getDestino()
    {
        return $this->destino;
    }

    public function getCantMaxPasajeros()
    {
        return $this->cantMaxPasajeros;
    }

    public function getEmpresa()
    {
        return $this->empresa;
    }

    public function getResponsableV()
    {
        return $this->responsableV;
    }

    public function getImporte()
    {
        return $this->importe;
    }

    public function getPasajeros()
    {
        return $this->pasajeros;
    }

    public function getMensajeOperacion()
    {
        return $this->mensajeOperacion;
    }

    public function setId($Id)
    {
        $this->Id = $Id;
    }

    public function setDestino($destino)
    {
        $this->destino = $destino;
    }

    public function setCantMaxPasajeros($cantMaxPasajeros)
    {
        if ($cantMaxPasajeros > 0) {
            if ($cantMaxPasajeros < count($this->getPasajeros())) {
                throw new Exception("La cantidad máxima de pasajeros no puede ser menor a la cantidad de pasajeros actual.");
            } else {
                $this->cantMaxPasajeros = $cantMaxPasajeros;
            }
        } else {
            throw new Exception("La cantidad máxima de pasajeros debe ser un número mayor a 0.");
        }
    }

    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;
    }

    public function setResponsableV($responsableV)
    {
        $this->responsableV = $responsableV;
    }

    public function setImporte($importe)
    {
        $this->importe = $importe;
    }

    public function setPasajeros($pasajeros)
    {
        $this->pasajeros = $pasajeros;
    }

    public function setMensajeOperacion($mensajeOperacion)
    {
        $this->mensajeOperacion = $mensajeOperacion;
    }

    public function agregarPasajeroAlArray($pasajero)
    {
        if (count($this->getPasajeros()) < $this->cantMaxPasajeros) {
            array_push($this->pasajeros, $pasajero);
        } else {
            throw new Exception("No hay más lugar en el viaje.");
        }
    }

    public static function listar($cond = ""){
        $base = new BaseDatos();
        $consultaViaje = "SELECT * FROM viaje";
        if($cond != ""){
            $consultaViaje.=" WHERE " . $cond;
        }
        $arregloViajes = [];
        if($base->Iniciar()){
            if($base->Ejecutar($consultaViaje)){
                
                while($resp = $base->Registro()){
                    $viaje = new Viaje();

                    $empresa = new Empresa();
                    $empresa->setId($resp["idempresa"]);
                    $empresa->buscar();

                    $responsableV = new ResponsableV();
                    $responsableV->setNroEmpleado($resp["rnumeroempleado"]);
                    $responsableV->buscar();

                    $viaje->setId($resp["idviaje"]);
                    $viaje->setPasajeros(Pasajero::listar("idviaje = " . $resp["idviaje"]));

                    $viaje->cargarViaje($resp["idviaje"], $resp["vdestino"], $resp["vcantmaxpasajeros"], $empresa, $responsableV, $resp["vimporte"], $viaje->getPasajeros());
                    array_push($arregloViajes, $viaje);
                }
            }else{
                $this->setMensajeOperacion($base->getError());
            }
        }else{
            $this->setMensajeOperacion($base->getError());
        }
        return $arregloViajes;
    }

    public function buscar(){
        $resp = false;
        $base = new BaseDatos();
        $consultaViaje = "SELECT * FROM viaje WHERE idviaje = ".$this->getId();
        if ($base->Iniciar()) {
            if($base->Ejecutar($consultaViaje)){
                if($row2=$base->Registro()){
                    $empresa = new Empresa();
                    $empresa->setId($row2['idempresa']);
                    $empresa->buscar();
                    $responsable = new ResponsableV();
                    $responsable->setNroEmpleado($row2['rnumeroempleado']);
                    $responsable->buscar();
                    $resp = true;
                    $pasajeros = Pasajero::listar("idviaje = " . $this->getId());
                    $this->cargarViaje($row2['idviaje'],$row2['vdestino'],$row2['vcantmaxpasajeros'],$empresa,$responsable,$row2['vimporte'],$pasajeros);
                }
            } else {
                $this->setMensajeOperacion($base->getError());
            }
        } else {
            $this->setMensajeOperacion($base->getError());
        }
        return $resp;
    }

    public function insertar(){
        $base = new BaseDatos();
        $consultaViaje = "INSERT INTO viaje (vdestino, vcantmaxpasajeros,idempresa, rnumeroempleado, vimporte) 
        VALUES ('" . $this->getDestino() . "','" . $this->getCantMaxPasajeros() . "','" . $this->getEmpresa()->getId() . "','" . $this->getResponsableV()->getNroEmpleado() . "','" . $this->getImporte() . "')";
        if ($base->Iniciar()) {
            if ($id = $base->devuelveIDInsercion($consultaViaje)) {
                $this->setId($id);
                return true;
            }else{
                $this->setMensajeOperacion($base->getError());
            }
        } else {
            $this->setMensajeOperacion($base->getError());
        } 
    }

    public function actualizar(){
        $base = new BaseDatos();
        $consultaViaje = "UPDATE viaje SET vdestino = '" . $this->getDestino() . "', vcantmaxpasajeros = '" . $this->getCantMaxPasajeros() . "', idempresa = '" . $this->getEmpresa()->getId() . "', rnumeroempleado = '" . $this->getResponsableV()->getNroEmpleado() . "', vimporte = '" . $this->getImporte() . "' WHERE idviaje = '" . $this->getId() . "'";
        $response = false;
        if($base->Iniciar()){
            if($base->Ejecutar($consultaViaje)){
                $response = true;
            }else{
                $this->setMensajeOperacion($base->getError());
            }
        }else{
            $this->setMensajeOperacion($base->getError());
        }

        return $response;

    }

    public function eliminar(){
        $base = new BaseDatos();
        if($this->getId() == null){
            $this->setMensajeOperacion($base->getError());
        }
        $consultaViaje = "DELETE FROM viaje WHERE idviaje = '" . $this->getId() . "'";
        $resp = false;
        if($base->Iniciar()){
            if($base->Ejecutar($consultaViaje)){
                $resp = true;
            }else{
                $this->setMensajeOperacion($base->getError());
            }
        }else{
            $this->setMensajeOperacion($base->getError());
        }

        return $resp;
    }

    public function mostrarPasajeros(){
        $pasajeros = $this->getPasajeros();
        $resp = "";
        if(count($pasajeros) == 0){
            $resp= "No hay pasajeros cargados en este viaje.";
        }else{
            foreach($pasajeros as $pasajero){
                $resp.= $pasajero->__toString() . "\n";
            }
        }
        return $resp;
    }

    public function __toString()
    {
        return "ID: " . $this->getId() . "\n" .
        "Destino: " . $this->getDestino() . "\n" .
        "Cantidad Maxima de Pasajeros: " . $this->getCantMaxPasajeros() . "\n" .
        "Empresa: " . $this->getEmpresa() . "\n" .
        "Responsable: " . $this->getResponsableV() . "\n" .
        "Importe: " . $this->getImporte() . "\n" .
        "Pasajeros: " . $this->mostrarPasajeros() . "\n";
    }

}