<?php
namespace reportes\instalacionesGenerales\entidad;

include_once "core/builder/FormularioHtml.class.php";
class FormProcessor {

    public $miConfigurador;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $urlApiProyectos;
    public $proyectos;
    public $contenidoTabla;

    public function __construct($sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->miFormulario = new \FormularioHtml();

        $_REQUEST['tiempo'] = time();

        /**
         * 1. Construcción Url Api Proyectos
         **/
        $this->crearUrlProyectos();

        /**
         * 2. Obtener Proyectos de Api
         **/

        $this->obtenerProyectos();

        /**
         * 3. Obtener detalle Proyectos
         **/

        $this->obtenerDetalleProyectos();

        /**
         * 4. Filtrar Proyectos
         **/

        $this->filtrarProyectos();

        /**
         * 5. Estructurar Tabla Proyectos a Retornar
         **/

        $this->estruturarTabla();

        /**
         * 6. Retornar Tabla
         **/
        echo $this->contenidoTabla;

        exit;

    }
    public function estruturarTabla() {
        $atributosGlobales['campoSeguro'] = 'true';
        $tab = 1;
        $i = 1;
        foreach ($this->proyectos as $key => $value) {

            // ---------------- CONTROL: Cuadro de Texto -----------
            $nombre = 'item' . $i;
            $atributos['id'] = $nombre;
            $atributos['nombre'] = $nombre;
            $atributos['marco'] = true;
            $atributos['estiloMarco'] = true;
            $atributos["etiquetaObligatorio"] = true;
            $atributos['columnas'] = 1;
            $atributos['dobleLinea'] = 1;
            $atributos['tabIndex'] = $tab;
            $atributos['etiqueta'] = '';
            $atributos['seleccionado'] = false;
            $atributos['evento'] = ' ';
            $atributos['eventoFuncion'] = ' ';
            $atributos['valor'] = $value['id'];
            $atributos['deshabilitado'] = false;
            $tab++;

            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);

            $item = $this->miFormulario->campoCuadroSeleccion($atributos);

            $clave = array_search("Proyecto/Urbanización:", array_column($value['custom_fields'], 'name'), true);
            $resultadoFinal[] = array(

                'numero' => "<center>" . $i . "</center>",
                'urbanizacion' => "<center>" . $value['name'] . "</center>",
                'opcion' => "<center>" . $item . "</center>",
            );
            $i++;
        }

        $total = count($resultadoFinal);

        $resultado = json_encode($resultadoFinal);

        $resultado = '{
                "recordsTotal":' . $total . ',
                "recordsFiltered":' . $total . ',
                "data":' . $resultado . '}';

        $this->contenidoTabla = $resultado;

    }
    public function filtrarProyectos() {

        foreach ($this->proyectos as $key => $value) {

            $clave = array_search("Proyecto/Urbanización:", array_column($value['custom_fields'], 'name'), true);

            if ($clave == false) {
                unset($this->proyectos[$key]);
            } else if (is_null($value['custom_fields'][$clave]['value']) == true || empty($value['custom_fields'][$clave]['value']) || $value['custom_fields'][$clave]['value'] = '') {
                unset($this->proyectos[$key]);
            }

        }
    }
    public function obtenerDetalleProyectos() {

        foreach ($this->proyectos as $key => $value) {

            $urlDetalle = $this->crearUrlDetalleProyectos($value['id']);

            $detalle = file_get_contents($urlDetalle);

            $detalle = json_decode($detalle, true);

            $this->proyectos[$key]['custom_fields'] = $detalle['custom_fields'];

        }

    }

    public function crearUrlDetalleProyectos($var = '') {

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";
        // Variables
        $variable = "pagina=openKyosApi";
        $variable .= "&procesarAjax=true";
        $variable .= "&action=index.php";
        $variable .= "&bloqueNombre=" . "llamarApi";
        $variable .= "&bloqueGrupo=" . "";
        $variable .= "&tiempo=" . $_REQUEST['tiempo'];
        $variable .= "&metodo=proyectosDetalle";
        $variable .= "&id_proyecto=" . $var;

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        return $urlApi;
    }
    public function obtenerProyectos() {
        $proyectos = file_get_contents($this->urlApiProyectos);

        $this->proyectos = json_decode($proyectos, true);
    }

    public function crearUrlProyectos() {

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";
        // Variables
        $variable = "pagina=openKyosApi";
        $variable .= "&procesarAjax=true";
        $variable .= "&action=index.php";
        $variable .= "&bloqueNombre=" . "llamarApi";
        $variable .= "&bloqueGrupo=" . "";
        $variable .= "&tiempo=" . $_REQUEST['tiempo'];
        $variable .= "&metodo=proyectosGeneral";

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $this->urlApiProyectos = $url . $cadena;

    }

    public function resetForm() {
        foreach ($_REQUEST as $clave => $valor) {

            if ($clave != 'pagina' && $clave != 'development' && $clave != 'jquery' && $clave != 'tiempo') {
                unset($_REQUEST[$clave]);
            }
        }
    }

}

$miProcesador = new FormProcessor($this->sql);

?>

