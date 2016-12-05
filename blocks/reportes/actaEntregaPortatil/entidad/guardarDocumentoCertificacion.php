<?php

namespace reportes\actaEntregaPortatil\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";

include $ruta . "/plugin/html2pdf/html2pdf.class.php";
class GenerarDocumento {
    public $miConfigurador;
    public $elementos;
    public $miSql;
    public $conexion;
    public $contenidoPagina;
    public $rutaURL;
    public $esteRecursoDB;
    public $clausulas;
    public $beneficiario;
    public $esteRecursoOP;
    public $rutaAbsoluta;
    public function __construct($sql) {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");

        // Conexion a Base de Datos
        $conexion = "produccion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");

        $this->rutaURLArchivo = $this->rutaURL . '/archivos/actas_entrega_portatil/Actas_Comisionamiento/Cerete_Altos_de_las_Acacias/';

        $this->rutaAbsolutaArchivo = $this->rutaAbsoluta . '/archivos/actas_entrega_portatil/Actas_Comisionamiento/Cerete_Altos_de_las_Acacias/';

        if (!isset($_REQUEST["bloqueGrupo"]) || $_REQUEST["bloqueGrupo"] == "") {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloque"] . "/";
        } else {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
        }
        //var_dump($_REQUEST);exit;
        //echo "generar Masivo";

        $this->rutaURL_Bloque = $this->rutaURL;
        $this->rutaAbsoluta_Bloque = $this->rutaAbsoluta;

        $this->obtenerInformacionBeneficiario();
        //var_dump($this->beneficiario);exit;
        foreach ($this->beneficiario as $key => $value) {

            $this->asosicarCodigoDocumento($value);

            $this->estruturaDocumento($value);
            $this->crearPDF();

            $this->estruturaDocumentoCartel($value);
            $this->crearPDFCartel();

        }
        echo "Termine";exit;
        $arreglo = array(
            'nombre_contrato' => $this->nombreDocumento,
            'ruta_contrato' => $this->rutaURL . $this->nombreDocumento,
        );

        $cadenaSql = $this->miSql->getCadenaSql('registrarDocumentoCertificado', $arreglo);

        $this->registro_certificado = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

        $arreglo = array(
            'id_beneficiario' => $_REQUEST['id_beneficiario'],
            'tipologia' => "555",
            'nombre_documento' => $this->nombreDocumento,
            'ruta_relativa' => $this->rutaURL . $this->nombreDocumento,
        );

        // $cadenaSql = $this->miSql->getCadenaSql('registrarRequisito', $arreglo);
        // $this->registroRequisito = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");
    }

    public function obtenerInformacionBeneficiario() {

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionCertificador');
        $beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $this->beneficiario = $beneficiario;

    }

    public function crearPDF() {

        ob_start();
        $html2pdf = new \HTML2PDF('P', 'LETTER', 'es', true, 'UTF-8', array(
            2,
            2,
            2,
            10,
        ));
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($this->contenidoPagina);
        $html2pdf->Output($this->rutaAbsolutaArchivo . $this->nombreDocumento, 'F');
    }

    public function crearPDFCartel() {

        ob_start();
        $html2pdf = new \HTML2PDF('L', 'LETTER', 'es', true, 'UTF-8', array(
            2,
            2,
            2,
            10,
        ));
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($this->contenidoPagina);
        $html2pdf->Output($this->rutaAbsolutaArchivo . $this->nombreCartel, 'F');
    }

    public function asosicarCodigoDocumento($beneficiario) {
        $this->prefijo = substr(md5(uniqid(time())), 0, 6);
        $cadenaSql = $this->miSql->getCadenaSql('consultarParametro', '008');
        $id_parametro = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
        $tipo_documento = $id_parametro['id_parametro'];
        $descripcion_documento = $id_parametro['id_parametro'] . '_' . $id_parametro['descripcion'];
        //$nombre_archivo = "AEP";
        //$this->nombreDocumento = $_REQUEST['id_beneficiario'] . "_" . $descripcion_documento . "_" . $this->prefijo . '.pdf';

        $this->nombreDocumento = $beneficiario['interior'] . "_" . $beneficiario['direccion_domicilio'] . "_" . $beneficiario['identificacion'] . "_Acta_Entrega_Portatil_" . $this->prefijo . '.pdf';
        $this->nombreCartel = $beneficiario['interior'] . "_" . $beneficiario['direccion_domicilio'] . "_" . $beneficiario['identificacion'] . "_Cartel_" . $this->prefijo . '.pdf';
    }
    public function estruturaDocumento($beneficiario) {
        unset($this->contenidoPagina);

        //$firma_contratista = $firmacontratista;
        /*
        $fecha = explode("-", $beneficiario['fecha_entrega']);

        $dia = $fecha[0];
        $mes = [
        "",
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre",
        ];
        $mes = $mes[$fecha[1]];
        $anno = $fecha[2];
         */
        {
            $tipo_vip = ($beneficiario['tipo_beneficiario'] == "1") ? "<b>X</b>" : "";
            $tipo_residencial_1 = ($beneficiario['tipo_beneficiario'] == "2") ? (($beneficiario['estrato_socioeconomico'] == "1") ? "<b>X</b>" : "") : "";
            $tipo_residencial_2 = ($beneficiario['tipo_beneficiario'] == "2") ? (($beneficiario['estrato_socioeconomico'] == "2") ? "<b>X</b>" : "") : "";
        }

        setlocale(LC_ALL, "es_CO.UTF-8");

        {
            $anexo_dir = '';

            if ($beneficiario['manzana'] != '0' && $beneficiario['manzana'] != '') {
                $anexo_dir .= " Manzana  #" . $beneficiario['manzana'];
            }

            if ($beneficiario['bloque'] != '0' && $beneficiario['bloque'] != '') {
                $anexo_dir .= " Bloque #" . $beneficiario['bloque'];
            }

            if ($beneficiario['torre'] != '0' && $beneficiario['torre'] != '') {
                $anexo_dir .= " Torre #" . $beneficiario['torre'];
            }

            if ($beneficiario['casa_apartamento'] != '0' && $beneficiario['casa_apartamento'] != '') {
                $anexo_dir .= " Casa/Apartamento #" . $beneficiario['casa_apartamento'];
            }

            if ($beneficiario['interior'] != '0' && $beneficiario['interior'] != '') {
                $anexo_dir .= " Interior #" . $beneficiario['interior'];
            }

            if ($beneficiario['lote'] != '0' && $beneficiario['lote'] != '') {
                $anexo_dir .= " Lote #" . $beneficiario['lote'];
            }

            if ($beneficiario['piso'] != '0' && $beneficiario['piso'] != '') {
                $anexo_dir .= " Piso #" . $beneficiario['piso'];
            }
        }

        $contenidoPagina = "
                            <style type=\"text/css\">
                                table {

                                    font-family:Helvetica, Arial, sans-serif; /* Nicer font */

                                    border-collapse:collapse; border-spacing: 3px;
                                }
                                td, th {
                                    border: 1px solid #000000;
                                    height: 13px;
                                } /* Make cells a bit taller */

                                th {

                                    font-weight: bold; /* Make sure they're bold */
                                    text-align: center;
                                    font-size:10px;
                                }
                                td {

                                    text-align: left;

                                }
                            </style>



                        <page backtop='25mm' backbottom='5mm' backleft='10mm' backright='10mm' footer='page'>
                            <page_header>
                                 <table  style='width:100%;' >
                                          <tr>
                                                <td align='center' style='width:100%;border=none;' >
                                                <img src='" . $this->rutaURL_Bloque . "frontera/css/imagen/logos_contrato.png'  width='500' height='45'>
                                                </td>
                                                <tr>
                                                <td style='width:100%;border:none;text-align:center;font-size:9px;'><b>008 - ACTA DE ENTREGA DE COMPUTADOR PORTÁTIL</b></td>
                                                </tr>
                                                <tr>
                                                <td style='width:100%;border:none;text-align:center;'><br><br><b>008 - ACTA DE ENTREGA DE COMPUTADOR PORTÁTIL</b></td>
                                                </tr>

                                        </tr>
                                    </table>

                        </page_header>
                       ";
//var_dump($beneficiario);exit;
        $contenidoPagina .= "
                            <br>
                            <br>
                            El suscrito beneficiario del Proyecto Conexiones Digitales II, cuyos datos se presentan a continuación:
                            <br>
                            <br>
                            <table width:100%;>
                                <tr>
                                    <td style='width:25%;'><b>Contrato de Servicio</b></td>
                                    <td colspan='3' style='width:75%;text-align:center;'><b>" . $beneficiario['numero_contrato'] . "</b></td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Beneficiario</td>
                                    <td colspan='3' style='width:75%;text-align:center;'><b>" . $beneficiario['nombre'] . " " . $beneficiario['primer_apellido'] . " " . $beneficiario['segundo_apellido'] . "</b></td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>No de Identificación</td>
                                    <td colspan='3' style='width:75%;text-align:center;'><b>" . number_format($beneficiario['identificacion'], 0, '', '.') . "</b></td>
                                </tr>
                                <tr>
                                    <td colspan='4'><b>Datos de Vivienda</b></td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Tipo</td>
                                    <td style='width:25%;text-align:center;'>VIP (" . $tipo_vip . ")</td>
                                    <td style='width:25%;text-align:center;'>Estrato 1 (" . $tipo_residencial_1 . ")</td>
                                    <td style='width:25%;text-align:center;'>Estrato 2 (" . $tipo_residencial_2 . ")</td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Dirección</td>
                                    <td colspan='3' style='width:75%;text-align:center;'>" . $beneficiario['direccion_domicilio'] . "  " . $anexo_dir . "</td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Departamento</td>
                                    <td style='width:25%;text-align:center;'>" . $beneficiario['departamento'] . "</td>
                                    <td style='width:25%;'>Municipio</td>
                                    <td style='width:25%;text-align:center;'>" . $beneficiario['municipio'] . "</td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Urbanización</td>
                                    <td colspan='3' style='width:75%;text-align:center;'>" . $beneficiario['urbanizacion'] . "</td>
                                </tr>
                            </table>
                            <br>
                            <table  style='width:100%;' >
                                          <tr>
                                                <td align='center' style='width:100%;border=none;' ><b>CERTIFICA BAJO GRAVEDAD DE JURAMENTO</b></td>

                                        </tr>
                            </table>
                             <br>
                            1. Que recibe un computador portátil NUEVO, sin uso, original de fábrica y en perfecto estado de funcionamiento, con las siguientes características:<br>
                            <br>
                                    <table width:100%;>
                                        <tr>
                                            <td align='rigth'  style=' width:20%;'><b>Marca</b></td>
                                            <td align='rigth' style='width:30%;'>Hewlett Packard</td>
                                            <td align='rigth' style=' width:20%;'><b>Modelo</b></td>
                                            <td align='rigth' style='width:30%;'>HP 245 G4 Notebook PC</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth' style=' width:20%;'><b>Serial</b></td>
                                            <td align='rigth' style='width:30%;'></td>
                                            <td align='rigth' style=' width:20%;'>Procesador</td>
                                            <td align='rigth' style='width:30%;'>AMD A8-7410 4 cores 2.2 GHz</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth' style=' width:20%;'><b>Memoria RAM</b></td>
                                            <td align='rigth' style='width:30%;'>DDR3 4096 MB</td>
                                            <td align='rigth' style=' width:20%;'><b>Disco Duro</b></td>
                                            <td align='rigth' style='width:30%;'>500 GB</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth' style=' width:20%;'><b>Sistema Operativo</b></td>
                                            <td align='rigth' style='width:30%;'>UBUNTU</td>
                                            <td align='rigth' style=' width:20%;'><b>Cámara</b></td>
                                            <td align='rigth' style='width:30%;'>Integrada 720 px HD</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth' style=' width:20%;'><b>Audio</b></td>
                                            <td align='rigth' style='width:30%;'>Integrado Estéreo</td>
                                            <td align='rigth' style=' width:20%;'><b>Batería</b></td>
                                            <td align='rigth' style='width:30%;'>41440 mWh</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth' style=' width:20%;'><b>Tarjeta de Red (Alámbrica)</b></td>
                                            <td align='rigth' style='width:30%;'>Integrada</td>
                                            <td align='rigth' style=' width:20%;'><b>Tarjeta de Red (Inalámbrica)</b></td>
                                            <td align='rigth' style='width:30%;'>Integrada</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth' style=' width:20%;'><b>Cargador</b></td>
                                            <td align='rigth' style='width:30%;'>Smart AC 100 v a 120 v</td>
                                            <td align='rigth' style=' width:20%;'><b>Pantalla</b></td>
                                            <td align='rigth' style='width:30%;'>HD SVA anti-brillo LED 14’’</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth'  style=' width:20%;'><b>Sitio web de soporte</b></td>
                                            <td align='rigth' colspan='3' style='width:80%;'>http://www.hp.com/latam/co/soporte/cas/</td>
                                        </tr>
                                        <tr>
                                            <td align='rigth'  style=' width:20%;'><b>Teléfono de soporte</b></td>
                                            <td align='rigth' colspan='3' style='width:80%;'>0180005147468368 - 018000961016.</td>
                                        </tr>
                                    </table>
                                    <br>
                            2. Que el computador recibido no presenta rayones, roturas, hendiduras o elementos sueltos.<br><br>
                            3. Que entiende que el computador recibido no tiene costo adicional y se encuentra incorporado al contrato de servicio suscrito con la Corporación Politécnica Nacional de Colombia.<br><br>
                            4. Que se compromete a velar por la seguridad del equipo y a cuidarlo para mantener su capacidad de uso y goce en el marco del contrato de servicio suscrito con la Corporación Politécnica Nacional de Colombia.<br>
                                <br>
                            5. Que se compromete a participar en por lo menos 20 horas de  capacitación sobre el manejo del equipo y/o aplicativos de uso productivo de esta herramienta como parte del proceso de apropiación social contemplado en el Anexo Técnico del proyecto Conexiones Digitales II<br><br><br>

                            Para constancia de lo anterior, firma en la ciudad de " . $beneficiario['municipio'] . ", municipio de " . $beneficiario['municipio'] . ", departamento de " . $beneficiario['departamento'] . ", el día __________________________________________.
                            <br>
                            <br>
                            <br>
                            <br>

                            <table width:100%;>
                                <tr>
                                    <td rowspan='2' style='width:50%;'>Firma:<br>&nbsp;
                                    <br>&nbsp;
                                    <br>&nbsp;
                                    <br>&nbsp;
                                    <br>&nbsp;
                                    </td>
                                    <td style='width:50%;text-align:center;'><b>" . $beneficiario['nombre'] . " " . $beneficiario['primer_apellido'] . " " . $beneficiario['segundo_apellido'] . "</b></td>
                                </tr>
                                <tr>
                                    <td style='width:50%;text-align:center;'><b>" . number_format($beneficiario['identificacion'], 0, '', '.') . "</b></td>
                                </tr>
                            </table>

                    ";

        $contenidoPagina .= "</page>";

        $this->contenidoPagina = $contenidoPagina;
        unset($beneficiario);

    }

    public function estruturaDocumentoCartel($beneficiario) {
        unset($this->contenidoPagina);

        {
            $anexo_dir = '';

            if ($beneficiario['manzana'] != '0' && $beneficiario['manzana'] != '') {
                $anexo_dir .= " Manzana  #" . $beneficiario['manzana'];
            }

            if ($beneficiario['bloque'] != '0' && $beneficiario['bloque'] != '') {
                $anexo_dir .= " Bloque #" . $beneficiario['bloque'];
            }

            if ($beneficiario['torre'] != '0' && $beneficiario['torre'] != '') {
                $anexo_dir .= " Torre #" . $beneficiario['torre'];
            }

            if ($beneficiario['casa_apartamento'] != '0' && $beneficiario['casa_apartamento'] != '') {
                $anexo_dir .= " Casa/Apartamento #" . $beneficiario['casa_apartamento'];
            }

            if ($beneficiario['interior'] != '0' && $beneficiario['interior'] != '') {
                $anexo_dir .= " Interior #" . $beneficiario['interior'];
            }

            if ($beneficiario['lote'] != '0' && $beneficiario['lote'] != '') {
                $anexo_dir .= " Lote #" . $beneficiario['lote'];
            }

            if ($beneficiario['piso'] != '0' && $beneficiario['piso'] != '') {
                $anexo_dir .= " Piso #" . $beneficiario['piso'];
            }
        }

        $contenidoPagina = "
                            <style type=\"text/css\">
                                table {

                                    font-family:Helvetica, Arial, sans-serif; /* Nicer font */

                                    border-collapse:collapse; border-spacing: 3px;
                                }
                                td, th {
                                    border: 1px solid #000000;
                                    height: 13px;
                                } /* Make cells a bit taller */

                                th {

                                    font-weight: bold; /* Make sure they're bold */
                                    text-align: center;
                                    font-size:30px;
                                }
                                td {

                                    text-align: left;

                                }
                            </style>



                        <page backtop='25mm' backbottom='10mm' backleft='10mm' backright='10mm'>

                       ";
//var_dump($beneficiario);exit;
        $contenidoPagina .= "
                        <table>
                               <tr>
                                    <td style='width:100%;border:none;font-size:30px;'>
                                                <br>
                                                <b>CODIGO DANE Y ESTRATO: </b>" . $beneficiario['codigo_municipio'] . " - VIP" . "<br><br>
                                                <b>MUNICIPIO:</b>  " . $beneficiario['municipio'] . "<br><br>
                                                <b>SUBPROYECTO: </b>" . $beneficiario['urbanizacion'] . "<br><br>
                                                <b>BENEFICIARIO: </b>" . $beneficiario['nombre'] . " " . $beneficiario['primer_apellido'] . " " . $beneficiario['segundo_apellido'] . "<br><br>
                                                <b>DIRECCIÓN: </b>" . $beneficiario['direccion_domicilio'] . "  " . $anexo_dir . "<br><br>
                                                <br>
                                                <br>


                                    </td>
                                </tr>
                            </table>



                            <table>
                               <tr>
                                    <td style='width:100%;text-align:center;border:none;font-size:30px;'><b>CONEXIONES DIGITALES II</b>
                                    <br>CONTRATO DE APORTE 681/2015<</td>
                                </tr>
                            </table>


                            <page_footer>
                            <table  style='width:100%;' >
                                        <tr>
                                                <td align='center' style='width:100%;border=none;' >
                                                <img src='" . $this->rutaURL_Bloque . "frontera/css/imagen/logos_contrato.png'  width='950' height='90'>
                                                </td>
                                        </tr>
                                    </table>
                            </page_footer>
                            ";

        $contenidoPagina .= "</page>";

        $this->contenidoPagina = $contenidoPagina;
        unset($beneficiario);
    }

}
$miDocumento = new GenerarDocumento($this->miSql);

?>
