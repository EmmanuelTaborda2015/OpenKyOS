<?php
namespace gestionBeneficiarios\generarContratosMasivos\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

class FormProcessor {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $archivos_datos;
    public $esteRecursoDB;
    public $datos_contrato;
    public $rutaURL;
    public $rutaAbsoluta;
    public $clausulas;
    public $registro_info_contrato;
    public function __construct($sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/archivos/generacionMasiva/";
        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento") . "/archivos/generacionMasiva/";

        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $_REQUEST['tiempo'] = time();

        /**
         *  1. Consultar Proceso
         **/

        $this->consultarProceso();

        /**
         *  2. Cambiar Estado Proceso
         **/

        $this->actualizarEstadoProceso();

        /**
         *  3. Creacion Directorio
         **/

        $this->crearDirectorio();

        /**
         *  4. Creación Documentos
         **/

        $this->creacionDocumentos();

        /**
         *  5. Generar Comprimido
         **/

        $this->generarComprimido();

        /**
         *  6. Limpiar Directorio
         **/

        $this->limpiarDirectorio();

        exit;

        /**
         *  6. Validar Existencia Beneficiarios
         **/

        $this->cerrar_log();

        if (isset($this->error)) {
            Redireccionador::redireccionar("ErrorInformacionCargar", base64_encode($this->ruta_relativa_log));
        } else {
            Redireccionador::redireccionar("ExitoInformacion");
        }

    }
    public function limpiarDirectorio() {
        var_dump($this->rutaAbsoluta_archivos);
        //$this->eliminarDirectorioContenido($this->rutaAbsoluta_archivos);
        exit;
    }

    public function eliminarDirectorioContenido($rutaAnalizar) {
        foreach (glob($rutaAnalizar . "/*") as $archivos_carpeta) {
            if (is_dir($archivos_carpeta)) {

                $valorContenido = @scandir($archivos_carpeta);

                if (count($valorContenido) == 2) {

                    rmdir($archivos_carpeta);
                } else {

                    $this->eliminarDirectorioContenido($archivos_carpeta);
                }
            } else {
                unlink($archivos_carpeta);
            }
        }
        rmdir($rutaAnalizar);
    }

    public function generarComprimido() {

        $this->nombre_archivo_zip = $this->comprimir($this->rutaAbsoluta, "Proceso_" . $this->proceso['id_proceso'], "Proceso_" . $this->proceso['id_proceso']);

        $this->ruta_url_archivo = $this->rutaURL . $this->nombre_archivo_zip;

    }

    public function comprimir($rutaObjetivoContenido, $nombreComprimido, $nombreDirectorioComprimir, $rutaSalidaComprimido = '') {

        $ruta_actual = getcwd();

        chdir($rutaObjetivoContenido);

        $nombre_archivo = $nombreComprimido . "_" . time() . ".zip";

        $cadena = "zip " . $rutaSalidaComprimido . $nombre_archivo . " " . $nombreDirectorioComprimir . "/*";

        $queries = exec($cadena);

        chdir($ruta_actual);

        return $nombre_archivo;

    }

    public function creacionDocumentos() {

        switch ($this->proceso['descripcion']) {
            case 'Contratos':
                include_once "generacionContratos.php";
                break;

        }
    }

    public function crearDirectorio() {

        $this->rutaURL_archivos = $this->rutaURL . "Proceso_" . $this->proceso['id_proceso'];
        $this->rutaAbsoluta_archivos = $this->rutaAbsoluta . "Proceso_" . $this->proceso['id_proceso'];

        if (!file_exists($this->rutaAbsoluta_archivos)) {

            mkdir($this->rutaAbsoluta_archivos, 0777, true);
            chmod($this->rutaAbsoluta_archivos, 0777);
        }
    }

    public function actualizarEstadoProceso() {

        $cadenaSql = $this->miSql->getCadenaSql('actualizarProceso', $this->proceso['id_proceso']);
        //$actualizacion = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

    }

    public function consultarProceso() {

        $cadenaSql = $this->miSql->getCadenaSql('consultarProceso');
        $this->proceso = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

    }

//----- Borrar desde aca
    public function validarContratosExistentes() {

        foreach ($this->datos_beneficiario as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarExitenciaContrato', $value['identificacion_beneficiario']);

            $consulta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if ($consulta) {

                $mensaje = " El beneficiario con identificación " . $consulta['numero_identificacion'] . " ya tiene un contrato con número #" . $consulta['numero_contrato'] . " asociado con el id_benficiario " . $consulta['id_beneficiario'] . ".";
                $this->escribir_log($mensaje);

                $this->error = true;

            }

        }

    }

    public function escribir_log($mensaje) {

        fwrite($this->log, $mensaje . PHP_EOL);

    }

    public function cerrar_log() {

        fclose($this->log);

    }

    public function creacion_log() {

        $prefijo = substr(md5(uniqid(time())), 0, 6);

        $this->ruta_absoluta_log = $this->rutaAbsoluta . "/entidad/logs/Log_documento_validacion_" . $prefijo . ".log";

        $this->ruta_relativa_log = $this->rutaURL . "/entidad/logs/Log_documento_validacion_" . $prefijo . ".log";

        $this->log = fopen($this->ruta_absoluta_log, "w");
    }

    public function cargarInformacionHojaCalculo() {

        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 300);

        if (file_exists($this->archivo['ruta_archivo'])) {

            //$documento = \PHPExcel_IOFactory::load($this->archivo['ruta_archivo']);

            //$this->informacion = $documento->getActiveSheet()->toArray(null, true, true, true);

            //unset($this->informacion[1]);

            $hojaCalculo = \PHPExcel_IOFactory::createReader($this->tipo_archivo);
            $informacion = $hojaCalculo->load($this->archivo['ruta_archivo']);
            //var_dump($informacion);die;

            //$hoja_1 = $informacion->getActiveSheet();
            //var_dump($hoja_1);

            $informacion_general = $hojaCalculo->listWorksheetInfo($this->archivo['ruta_archivo']);

            {

                $total_filas = $informacion_general[0]['totalRows'];

            }

            for ($i = 2; $i <= $total_filas; $i++) {

                $datos_beneficiario[$i]['identificacion_beneficiario'] = $informacion->setActiveSheetIndex()->getCell('A' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['telefono'] = $informacion->setActiveSheetIndex()->getCell('B' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['celular'] = $informacion->setActiveSheetIndex()->getCell('C' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['correo'] = $informacion->setActiveSheetIndex()->getCell('D' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['direccion'] = $informacion->setActiveSheetIndex()->getCell('E' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['manzana'] = $informacion->setActiveSheetIndex()->getCell('F' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['bloque'] = $informacion->setActiveSheetIndex()->getCell('G' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['torre'] = $informacion->setActiveSheetIndex()->getCell('H' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['casa_apartamento'] = $informacion->setActiveSheetIndex()->getCell('I' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['interior'] = $informacion->setActiveSheetIndex()->getCell('J' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['lote'] = $informacion->setActiveSheetIndex()->getCell('K' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['piso'] = $informacion->setActiveSheetIndex()->getCell('L' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['nombre_comisionador'] = $informacion->setActiveSheetIndex()->getCell('M' . $i)->getCalculatedValue();

                $datos_beneficiario[$i]['fecha_contrato'] = $informacion->setActiveSheetIndex()->getCell('N' . $i)->getCalculatedValue();

            }
            unlink($this->archivo['ruta_archivo']);

            $this->datos_beneficiario = $datos_beneficiario;

        } else {
            Redireccionador::redireccionar("ErrorNoCargaInformacionHojaCalculo");

        }

    }

    public function cargarArchivos() {

        $archivo_datos = '';
        $archivo = $_FILES['archivo_validacion'];

        if ($archivo['error'] == 0) {

            switch ($archivo['type']) {
                case 'application/vnd.oasis.opendocument.spreadsheet':
                    $this->tipo_archivo = 'OOCalc';
                    break;

                case 'application/vnd.ms-excel':
                    $this->tipo_archivo = 'Excel5';
                    break;

                case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                    $this->tipo_archivo = 'Excel2007';
                    break;

                default:
                    Redireccionador::redireccionar("ErrorFormatoArchivo");
                    break;
            }

            $this->prefijo = substr(md5(uniqid(time())), 0, 6);
            /*
             * obtenemos los datos del Fichero
             */
            $tamano = $archivo['size'];
            $tipo = $archivo['type'];
            $nombre_archivo = str_replace(" ", "_", $archivo['name']);
            /*
             * guardamos el fichero en el Directorio
             */
            $ruta_absoluta = $this->rutaAbsoluta . "/entidad/archivos_validar/" . $this->prefijo . "_" . $nombre_archivo;

            $ruta_relativa = $this->rutaURL . " /entidad/archivos_validar/" . $this->prefijo . "_" . $nombre_archivo;

            $archivo['rutaDirectorio'] = $ruta_absoluta;

            if (!copy($archivo['tmp_name'], $ruta_absoluta)) {

                Redireccionador::redireccionar("ErrorCargarArchivo");
            }

            $this->archivo = array(
                'ruta_archivo' => str_replace("//", "/", $ruta_absoluta),
                'nombre_archivo' => $archivo['name'],

            );

        } else {
            Redireccionador::redireccionar("ErrorArchivoNoValido");
        }

    }

}

$miProcesador = new FormProcessor($this->sql);
?>

