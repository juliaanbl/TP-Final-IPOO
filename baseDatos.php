<?php
class BaseDatos {
    private $HOSTNAME;
    private $BASEDATOS;
    private $USUARIO;
    private $CLAVE;
    private $CONEXION;
    private $QUERY;
    private $RESULT;
    private $ERROR;

    public function __construct(){
        $this->HOSTNAME = "127.0.0.1";
        $this->BASEDATOS = "bdviajes";
        $this->USUARIO = "root";
        $this->CLAVE="";
        $this->RESULT=0;
        $this->QUERY="";
        $this->ERROR="";
    }

    public function getError(){
        return "\n".$this->ERROR;  
    }

    public function getResult(){
        return $this->RESULT;
    }

    public  function Iniciar(){
        $resp  = false;
        $conexion = mysqli_connect($this->HOSTNAME,$this->USUARIO,$this->CLAVE,$this->BASEDATOS);
        if ($conexion){
            if (mysqli_select_db($conexion,$this->BASEDATOS)){
                $this->CONEXION = $conexion;
                unset($this->QUERY);
                unset($this->ERROR);
                $resp = true;
            }  else {
                $this->ERROR = mysqli_errno($conexion) . ": " .mysqli_error($conexion);
            }
        }else{
            $this->ERROR =  mysqli_errno($conexion) . ": " .mysqli_error($conexion);
        }
        return $resp;
    }

    public function Ejecutar($consulta){
        $resp  = false;
        unset($this->ERROR);
        $this->QUERY = $consulta;
        if(  $this->RESULT = mysqli_query( $this->CONEXION,$consulta)){
            $resp = true;
        } else {
            $this->ERROR =mysqli_errno( $this->CONEXION).": ". mysqli_error( $this->CONEXION);
            
        }
        return $resp;
    }
 
    public function Registro() {
        $resp = null;
        if ($this->RESULT){
            unset($this->ERROR);
            if($temp = mysqli_fetch_assoc($this->RESULT)){
                $resp = $temp;
            }else{
                mysqli_free_result($this->RESULT);
            }
        }else{
            $this->ERROR = mysqli_errno($this->CONEXION) . ": " . mysqli_error($this->CONEXION);
        }
        return $resp ;
    }

    public function devuelveIDInsercion($consulta){
        $resp = null;
        unset($this->ERROR);
        $this->QUERY = $consulta;
        if ($this->RESULT = mysqli_query($this->CONEXION,$consulta)){
            $id = mysqli_insert_id($this->CONEXION);
            $resp =  $id;
        } else {
            $this->ERROR =mysqli_errno( $this->CONEXION) . ": " . mysqli_error( $this->CONEXION);
           
        }
    return $resp;
    }
    
}
?>