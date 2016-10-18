<?php
namespace gestionComisionamiento\generacionActas;
if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

include_once "core/manager/Configurador.class.php";
include_once "core/connection/Sql.class.php";

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
    public $miConfigurador;
    public function getCadenaSql($tipo, $variable = '') {

        /**
         * 1.
         * Revisar las variables para evitar SQL Injection
         */
        $prefijo = $this->miConfigurador->getVariableConfiguracion("prefijo");
        $idSesion = $this->miConfigurador->getVariableConfiguracion("id_sesion");

        switch ($tipo) {

            /**
             * Clausulas específicas
             */
            case 'consultarAgendamientos':

                $cadenaSql = " SELECT id_agendamiento, codigo_nodo, fecha_agendamiento , responsable, count(id_orden_trabajo) cantidad_beneficiarios";
                $cadenaSql .= " FROM interoperacion.agendamiento_comisionamiento";
                $cadenaSql .= " WHERE estado_registro = TRUE";
                $cadenaSql .= " GROUP BY id_agendamiento,codigo_nodo,fecha_agendamiento,responsable;";
                break;

            case 'consultarAgendamientosGeneral':
                $cadenaSql = " SELECT * ";
                $cadenaSql .= "FROM interoperacion.agendamiento_comisionamiento ";
                $cadenaSql .= " WHERE estado_registro = TRUE ";
                break;

            case 'consultarAgendamientosParticulares':
                $cadenaSql = " SELECT * ";
                $cadenaSql .= "FROM interoperacion.agendamiento_comisionamiento ";
                $cadenaSql .= " WHERE estado_registro = TRUE ";
                $cadenaSql .= " AND  id_agendamiento ='" . $variable['id_agendamiento'] . "' ";
                $cadenaSql .= " AND  codigo_nodo ='" . $variable['codigo_nodo'] . "' ";
                $cadenaSql .= " AND  responsable ='" . $variable['responsable'] . "'; ";
                break;

            case 'insertarBloque':
                $cadenaSql = 'INSERT INTO ';
                $cadenaSql .= $prefijo . 'bloque ';
                $cadenaSql .= '( ';
                $cadenaSql .= 'nombre,';
                $cadenaSql .= 'descripcion,';
                $cadenaSql .= 'grupo';
                $cadenaSql .= ') ';
                $cadenaSql .= 'VALUES ';
                $cadenaSql .= '( ';
                $cadenaSql .= '\'' . $_REQUEST['nombre'] . '\', ';
                $cadenaSql .= '\'' . $_REQUEST['descripcion'] . '\', ';
                $cadenaSql .= '\'' . $_REQUEST['grupo'] . '\' ';
                $cadenaSql .= '); ';
                break;
        }

        return $cadenaSql;
    }
}
?>

