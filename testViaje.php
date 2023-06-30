<?php
include_once('baseDatos.php');
include_once('pasajero.php');
include_once('responsableV.php');
include_once('empresa.php');
include_once('viaje.php');

function ABMViajes($empresa)
{
    $opcion = 0;
    while ($opcion != 5) {
        echo
                "\n1. Mostrar viajes\n" .
                "2. Nuevo viaje \n" .
                "3. Modificar viaje \n" .
                "4. Borrar un viaje \n" .
                "5. Volver \n";
        echo"Ingrese una opción: ";
        $opcion = trim(fgets(STDIN));
        switch ($opcion) {
            case 1:
                mostrarViajes($empresa);
                break;
            case 2:
                ingresarViaje($empresa);
                break;
            case 3:
                modificarViaje($empresa);
                break;
            case 4:
                borrarViaje($empresa);
                break;
            case 5:
                echo"Volviendo al menú principal \n";
                break;
            default:
                echo"Opción no válida. \n";
                break;
        }
    }
}


function mostrarViajes($empresa){
    $empresa->setViajes(Viaje::listar("idempresa = " . $empresa->getId()));
    if (count($empresa->getViajes()) == 0) {
        echo"No hay viajes cargados\n";
    } else {
        echo "Listado de viajes: \n";
        echo $empresa->mostrarViajes();
    }
}

function seleccionarViaje($empresa){
    $empresa->setViajes(Viaje::listar("idempresa = " . $empresa->getId()));
    echo"Viajes disponibles: \n";
    $arrIDViajes = array();
    foreach ($empresa->getViajes() as $viaje) {
        $viaje->setPasajeros(Pasajero::listar("idviaje = " . $viaje->getId()));
        echo"\nID: " . $viaje->getId() . "\nDestino: " . $viaje->getDestino() . "\nCant Pasajeros Confirmados/Maximo: " . count($viaje->getPasajeros()) . "/" . $viaje->getCantMaxPasajeros() . "\nImporte: $" . $viaje->getImporte() . "\n";
        array_push($arrIDViajes, $viaje->getId());
    }
    echo"\nIngrese el ID del viaje: ";
    $idViaje = trim(fgets(STDIN));
    if (!in_array($idViaje, $arrIDViajes)) {
        throw new Exception("ID de viaje no válido o inexistente. \n");
    }
    return $idViaje;
}

function ingresarViaje($empresa){
    echo"Ingrese el destino del viaje: \n";
    $destino = trim(fgets(STDIN));
    echo"Ingrese la cantidad máxima de pasajeros: \n";
    $cantMaxPasajeros = trim(fgets(STDIN));

    echo"Seleccione responsable del viaje: \n";
    $responsableId = seleccionarResponsable();
    $responsableV = new ResponsableV();
    $responsableV->setNroEmpleado($responsableId);
    $responsableV->buscar();

    echo"Ingrese el importe del viaje: ";
    $importe = trim(fgets(STDIN));
    $viaje = new Viaje();
    $viaje->setDestino($destino);
    $viaje->setCantMaxPasajeros($cantMaxPasajeros);
    $viaje->setResponsableV($responsableV);
    $viaje->setEmpresa($empresa);
    $viaje->setImporte($importe);
    if ($viaje->insertar()) {
        echo"\n***Viaje creado con éxito***\n";
        echo"Los datos del viaje son: \n" . $viaje;
    }
}

function modificarViaje($empresa){
    echo"Ingrese el ID del viaje a modificar: \n";
    $idViaje = seleccionarViaje($empresa);
    $viaje = new Viaje();
    $viaje->setId($idViaje);
    $viaje->buscar();

    echo"Ingrese el nuevo destino del viaje: \n";
    $destino = trim(fgets(STDIN));
    $viaje->setDestino($destino);

    echo"Ingrese la nueva cantidad máxima de pasajeros: \n";
    $cantMaxPasajeros = trim(fgets(STDIN));
    $viaje->setCantMaxPasajeros($cantMaxPasajeros);

    echo"Seleccione nuevo responsable del viaje: \n";
    $responsableVID = seleccionarResponsable();
    $responsableV = new ResponsableV();
    $responsableV->setNroEmpleado($responsableVID);
    $viaje->setResponsableV($responsableV);

    echo"Ingrese el nuevo importe del viaje: ";
    $importe = trim(fgets(STDIN));
    $viaje->setImporte($importe);

    if ($viaje->actualizar()) {
        echo"\n\n***Viaje modificado con éxito***\n\n";
        $viaje->buscar();
        echo"Los datos del viaje son: \n" . $viaje;
    }
}

function borrarViaje($empresa){
    echo"Ingrese el ID del viaje a borrar: \n";
    $idViaje = seleccionarViaje($empresa);
    $viaje = new Viaje();
    $viaje->setId($idViaje);
    if ($viaje->eliminar()) {
        echo"\n***Viaje borrado con éxito***\n";
    }
}

function ABMResponsableV(){
    $opcion = 0;
    while ($opcion != 5) {
        echo
            "\n1. Mostrar responsables\n" .
            "2. Nuevo responsable \n" .
            "3. Modificar responsable \n" .
            "4. Borrar responsable \n" .
            "5. Volver \n";
        echo"Ingrese una opción: ";
        $opcion = trim(fgets(STDIN));
        switch ($opcion) {
            case 1:
                mostrarResponsable();
                break;
            case 2:
                crearResponsable();
                break;
            case 3:
                modificarResponsable();
                break;
            case 4:
                borrarResponsable();
                break;
            case 5:
                echo"Volviendo al menú principal \n";
                break;
            default:
                echo"Opción no válida. \n";
                break;
        }
    }
}

function mostrarResponsable(){
    $responsables = ResponsableV::listar();
    foreach ($responsables as $responsable) {
        $viajes = Viaje::listar("rnumeroempleado=".$responsable->getNroEmpleado());
        $responsable->setViajes($viajes);
        echo$responsable;
    }
}

function seleccionarResponsable(){
    $responsables = ResponsableV::listar();
    $responsables_id = array();
    foreach ($responsables as $responsable) {
        echo$responsable;
        array_push($responsables_id, $responsable->getNroEmpleado());
    }
    echo"\n Ingrese el numero de empleado del responsable a seleccionar: ";
    $idResponsable = trim(fgets(STDIN));
    if (!in_array($idResponsable, $responsables_id)) {
        throw new Exception("El responsable no existe");
    }
    return $idResponsable;
}

function crearResponsable(){
    echo"Ingrese el nombre del responsable: \n";
    $nombre = trim(fgets(STDIN));
    echo"Ingrese el apellido del responsable: \n";
    $apellido = trim(fgets(STDIN));
    echo"Ingrese numero de licencia del responsable: \n";
    $licencia = trim(fgets(STDIN));
    $responsable = new ResponsableV();
    $responsable->setNombre($nombre);
    $responsable->setApellido($apellido);
    $responsable->setNroLicencia($licencia);
    if ($responsable->insertar()) {
        echo"Responsable creado con éxito \n";
        echo"Los datos del responsable son: \n" . $responsable;
    }
}

function modificarResponsable(){
    echo"Ingrese el Numero de empleado del responsable a modificar: \n";
    echo"Responsables disponibles: \n";
    $idResponsable = seleccionarResponsable();
    $responsable = new ResponsableV();
    $responsable->setNroEmpleado($idResponsable);
    $responsable->buscar();
    echo"Ingrese el nuevo nombre del responsable: \n";
    $nombre = trim(fgets(STDIN));
    echo"Ingrese el nuevo apellido del responsable: \n";
    $apellido = trim(fgets(STDIN));
    echo"Ingrese el nuevo numero de licencia del responsable: \n";
    $licencia = trim(fgets(STDIN));
    $responsable->setNombre($nombre);
    $responsable->setApellido($apellido);
    $responsable->setNroLicencia($licencia);
    if ($responsable->actualizar()) {
        echo"Responsable modificado con éxito \n";
        echo"Los datos del responsable son: \n" . $responsable;
    }
}

function borrarResponsable()
{
    echo"***Borrar un responsable***\n";
    echo"Lista de responsables disponibles:";
    $idResponsable = seleccionarResponsable();
    $responsable = new ResponsableV();
    $responsable->setNroEmpleado($idResponsable);
    $viajes = Viaje::listar("rnumeroempleado=".$idResponsable);
    if (count($viajes) > 0) {
        throw new Exception("El responsable tiene viajes asignados, por lo que no se lo puede eliminar.");
    }
    if ($responsable->eliminar()) {
        echo"Responsable borrado con éxito \n";
    }
}

function ABMPasajero($empresa){
    $opcion = 0;
    while ($opcion != 5) {
        echo"\nPasajeros de " . $empresa->getNombre() . "\n";
        echo
                "\n1. Mostrar pasajeros por viaje\n" .
                "2. Agregar nuevo pasajero\n" .
                "3. Modificación de un pasajero\n" .
                "4. Borrar un pasajero\n" .
                "5. Volver\n"
        ;
        echo"Ingrese una opción: ";
        $opcion = trim(fgets(STDIN));
        switch ($opcion) {
            case 1:
                mostrarViaje($empresa);
                break;
            case 2:
                crearPasajero($empresa);
                break;
            case 3:
                modificarPasajero($empresa);
                break;
            case 4:
                borrarPasajero();
                break;
            case 5:
                echo"Volviendo al menú principal \n";
                break;
            default:
                echo"Opción no válida. \n";
                break;
        }
    }
}

function mostrarViaje($empresa){
    $idViaje = seleccionarViaje($empresa);
    $pasajeros = Pasajero::listar("idviaje = $idViaje");
    if (count($pasajeros) == 0) {
        echo"\n\nNo hay pasajeros para este viaje \n\n";
    } else {
        echo"\n\nPasajeros del viaje: \n\n";
        foreach ($pasajeros as $pasajero) {
            echo$pasajero;
        }
    }
}

function verificarSiPoseeViajeAsignado($dni){
    $retorno = false;
    $p = Pasajero::listar("pdocumento = $dni");
    if (count($p) > 0) {
        $retorno = true;
    }
    return $retorno;
}

function crearPasajero($empresa){
    echo"Ingrese el dni del pasajero: \n";
    $dni = trim(fgets(STDIN));
    if (verificarSiPoseeViajeAsignado($dni)) {
        throw new Exception("El pasajero ya posee un viaje asignado");
    }
    $idViaje = seleccionarViaje($empresa);
    echo"Ingrese el nombre del pasajero: \n";
    $nombre = trim(fgets(STDIN));
    echo"Ingrese el apellido del pasajero: \n";
    $apellido = trim(fgets(STDIN));
    echo"Ingrese el telefono del pasajero: \n";
    $telefono = trim(fgets(STDIN));

    $viaje = new Viaje();
    $viaje->setId($idViaje);
    $viaje->buscar();

    $pasajero = new Pasajero();
    $pasajero->cargarPasajero($nombre, $apellido, $dni, $telefono, $viaje);
    $viaje->agregarPasajeroAlArray($pasajero);

    $pasajero->setViaje($viaje);
    if ($pasajero->insertar()) {
        echo"Pasajero creado con éxito \n";
        echo"Los datos del pasajero son: \n" . $pasajero;
    }
}

function modificarPasajero($empresa){
    echo"Ingrese el dni del pasajero: \n";
    $dni = trim(fgets(STDIN));
    $pasajero = new Pasajero();
    $pasajero->setDni($dni);
    $pasajero->buscar();

    echo"Ingrese el nombre del pasajero: \n";
    $nombre = trim(fgets(STDIN));
    echo"Ingrese el apellido del pasajero: \n";
    $apellido = trim(fgets(STDIN));
    echo"Ingrese el telefono del pasajero: \n";
    $telefono = trim(fgets(STDIN));
    $pasajero->setNombre($nombre);
    $pasajero->setApellido($apellido);
    $pasajero->setTelefono($telefono);

    echo"Seleccione el nuevo viaje a asignar a este pasajero: \n";
    $idViaje = seleccionarViaje($empresa);
    $viaje = new Viaje();
    $viaje->setId($idViaje);
    $viaje->buscar();
    if(count($viaje->getPasajeros()) == $viaje->getCantMaxPasajeros()){
        throw new Exception("El viaje seleccionado ya se encuentra lleno, seleccione otro viaje");
    }
    $pasajero->setViaje($viaje);
    if($pasajero->actualizar()){
        echo"Pasajero modificado con éxito \n";
        echo"Los datos del pasajero son: \n" . $pasajero;
    }
}

function borrarPasajero(){
    echo"Ingrese el dni del pasajero a eliminar: \n";
    $dni = trim(fgets(STDIN));
    $pasajero = new Pasajero();
    $pasajero->setDni($dni);
    $pasajero->buscar();
    if ($pasajero->eliminar()) {
        echo"Pasajero borrado con éxito \n";
    }
}

function ABMEmpresa($empresa){
    $opcion = 0;
    while ($opcion != 6) {
        echo"\n1. Cambiar empresa\n" .
            "2. Mostrar Empresas \n" .
            "3. Crear Empresa \n" .
            "4. Modificar Empresa \n" .
            "5. Borrar Empresa \n" .
            "6. Volver \n";
        echo"Ingrese una opción: ";
        $opcion = trim(fgets(STDIN));
        switch ($opcion) {
            case 1:
                $empresa = seleccionarEmpresa($empresa);
                break;
            case 2:
                mostrarEmpresas();
                break;
            case 3:
                crearEmpresa();
                break;
            case 4:
                $modificada = modificarEmpresa();
                if ($modificada->getId() == $empresa->getId()) {
                    $empresa = $modificada;
                }
                break;
            case 5:
                borrarEmpresa();
                break;
            case 6:
                echo"Volviendo al menú principal \n";
                break;
            default:
                echo"Opción no válida. \n";
                break;
        }
        return $empresa;
    }
}

function mostrarEmpresas(){
    echo"\n Lista de empresas disponibles:\n";
    $empresas = Empresa::listar();
    foreach ($empresas as $empresa) {
        $empresa->setViajes(Viaje::listar("idempresa = " . $empresa->getId()));
        echo$empresa;
    }
    return $empresas;
}

function seleccionarEmpresa(){
    $empresas = mostrarEmpresas();
    echo"Ingrese el id de la empresa: ";
    $idEmpresa = trim(fgets(STDIN));
    $retorno = null;
    $i = 0;
    while ($retorno == null && $i < count($empresas)) {
        $empresa = $empresas[$i];
        if ($empresa->getId() == $idEmpresa) {
            $retorno = $empresa;
        }
        $i++;
    }
    if ($retorno == null) {
        throw new Exception("No se encontró la empresa con id $idEmpresa \n");
    }
    return $retorno;
}

function crearEmpresa(){
    echo"***Crear una empresa***\n";
    echo"Ingrese el nombre de la empresa: ";
    $nombre = trim(fgets(STDIN));
    echo"Ingrese la dirección de la empresa: ";
    $direccion = trim(fgets(STDIN));
    $empresa = new Empresa();
    $empresa->setNombre($nombre);
    $empresa->setDireccion($direccion);
    if ($empresa->insertar()) {
        echo"Empresa guardada con éxito \n";
        echo"DATOS: \n" . $empresa . "\n";
    }
    return $empresa;
}

function modificarEmpresa(){
    echo"***Modificar una empresa***\n";
    $empresa = seleccionarEmpresa();
    echo"Empresa: " . $empresa->getNombre();
    echo"Ingrese el nuevo nombre de la empresa: ";
    $nombre = trim(fgets(STDIN));
    echo"Ingrese la nueva dirección de la empresa: ";
    $direccion = trim(fgets(STDIN));
    $empresa->setNombre($nombre);
    $empresa->setDireccion($direccion);
    if ($empresa->actualizar()) {
        echo"Empresa modificada con éxito \n";
        echo"Datos: \n" . $empresa . "\n";
    }
    return $empresa;
}

function borrarEmpresa(){
    echo"***Borrar una empresa***\n";
    $empresa = seleccionarEmpresa();
    echo"Empresa: " . $empresa->getNombre()."\n";
    if (true) {
        try {
            if ($empresa->eliminar()) {
                echo "Empresa borrada con éxito \n";
            }
        } catch (Exception $e) {
            echo"\n La empresa posee viajes asignados, por lo que es imposible borrarla. \n";
        }
    } else {
        echo"No se borró la empresa. \n";
    }
}

function menuPrincipal($empresa){
    echo"\n1. Empresa \n" .
        "2. Viajes \n" .
        "3. Pasajeros \n" .
        "4. Responsables \n" .
        "5. Salir \n";
}

function testViaje(){
    $empresa = new Empresa();
    $empresa->setId(1);
    $empresa->buscar();
    $opcion = 0;
    while ($opcion != 5) {
        try {
            menuPrincipal($empresa);
            echo "Ingrese una opción: ";
            $opcion = trim(fgets(STDIN));
            switch ($opcion) {
                case 1:
                    $empresa = ABMEmpresa($empresa);
                    break;
                case 2:
                    ABMViajes($empresa);
                    break;
                case 3:
                    ABMPasajero($empresa);
                    break;
                case 4:
                    ABMResponsableV();
                    break;
                case 5:
                    break;
                default:
                    echo "Opción no válida";
                    break;
            }
        } catch (Exception $e) {
            echo "\n\n" . $e->getMessage() . "\n\n";
        }
    }
}

testViaje();