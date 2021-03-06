<?php

namespace reportes\actaEntregaPortatil\entidad;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

$ruta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/plugin/html2pfd/";

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
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->miSql = $sql;
		$this->rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
		
		// Conexion a Base de Datos
		$conexion = "interoperacion";
		$this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$conexion = "openproject";
		$this->esteRecursoOP = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$this->rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
		$this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		
		if (! isset ( $_REQUEST ["bloqueGrupo"] ) || $_REQUEST ["bloqueGrupo"] == "") {
			$this->rutaURL .= "/blocks/" . $_REQUEST ["bloque"] . "/";
			$this->rutaAbsoluta .= "/blocks/" . $_REQUEST ["bloque"] . "/";
		} else {
			$this->rutaURL .= "/blocks/" . $_REQUEST ["bloqueGrupo"] . "/" . $_REQUEST ["bloque"] . "/";
			$this->rutaAbsoluta .= "/blocks/" . $_REQUEST ["bloqueGrupo"] . "/" . $_REQUEST ["bloque"] . "/";
		}
		
		/**
		 * 1.
		 * Estruturar Documento
		 */
		
		$this->estruturaDocumento ();
		
		/**
		 * 2.
		 * Crear PDF
		 */
		
		$this->crearPDF ();
	}
	public function crearPDF() {
		ob_start ();
		$html2pdf = new \HTML2PDF ( 'P', 'LETTER', 'es', true, 'UTF-8', array (
				2,
				2,
				2,
				10 
		) );
		$html2pdf->pdf->SetDisplayMode ( 'fullpage' );
		$html2pdf->WriteHTML ( $this->contenidoPagina );
		$html2pdf->Output ( 'Acta_Entrega_servicio_CC_' . $_REQUEST ['identificacion'] . '_' . date ( 'Y-m-d' ) . '.pdf', 'D' );
	}
	public function estruturaDocumento() {
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultaInformacionCertificado' );
		$infoCertificado = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
		
		$anexo_dir = '';
		
		if ($infoCertificado ['manzana'] != 0) {
			$anexo_dir .= " Manzana  #" . $infoCertificado ['manzana'] . " - ";
		}
		
		if ($infoCertificado ['bloque'] != 0) {
			$anexo_dir .= " Bloque #" . $infoCertificado ['bloque'] . " - ";
		}
		
		if ($infoCertificado ['torre'] != 0) {
			$anexo_dir .= " Torre #" . $infoCertificado ['torre'] . " - ";
		}
		
		if ($infoCertificado ['casa_apartamento'] != 0) {
			$anexo_dir .= " Casa/Apartamento #" . $infoCertificado ['casa_apartamento'];
		}
		
		if ($infoCertificado ['interior'] != 0) {
			$anexo_dir .= " Interior #" . $infoCertificado ['interior'];
		}
		
		if ($infoCertificado ['lote'] != 0) {
			$anexo_dir .= " Lote #" . $infoCertificado ['lote'];
		}
		
		$infoCertificado ['direccion'] = $infoCertificado ['direccion'] . " " . $anexo_dir;
		
		$_REQUEST = array_merge ( $_REQUEST, $infoCertificado );
		
		$fecha = explode ( "-", $_REQUEST ['fecha_instalacion'] );
		
		$dia = $fecha [0];
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
				"Diciembre" 
		];
		$mes = $mes [$fecha [1]];
		$anno = $fecha [2];
		{
			
			$tipo_vip = ($_REQUEST ['tipo_beneficiario'] == "1") ? "<b>X</b>" : "";
			$tipo_residencial_1 = ($_REQUEST ['tipo_beneficiario'] == "2") ? (($_REQUEST ['estrato'] == "1") ? "<b>X</b>" : "") : "";
			$tipo_residencial_2 = ($_REQUEST ['tipo_beneficiario'] == "2") ? (($_REQUEST ['estrato'] == "2") ? "<b>X</b>" : "") : "";
		}
		
		$localizacion = explode ( ",", $_REQUEST ['geolocalizacion'] );
		
		if (count ( $localizacion ) == 2) {
			$localizacion [0] = trim ( $localizacion [0] );
			$localizacion [1] = trim ( $localizacion [1] );
		} else {
			$localizacion [0] = '';
			$localizacion [1] = '';
		}
		
		/**
		 * Calculo Latitud GMS
		 */
		$latitud = $localizacion [0];
		
		$latitud_grados = reset ( explode ( ".", $latitud ) );
		
		$latitud_minutos_dc = (((($latitud - $latitud_grados) * 60) < 0) ? (($latitud - $latitud_grados) * 60) * - 1 : (($latitud - $latitud_grados) * 60));
		
		$latitud_minutos = reset ( explode ( ".", $latitud_minutos_dc ) );
		
		$latitud_segundos = (($latitud_minutos_dc - $latitud_minutos) * 60 < 0) ? ($latitud_minutos_dc - $latitud_minutos) * 60 * - 1 : ($latitud_minutos_dc - $latitud_minutos) * 60;
		
		/**
		 * Calculo longitud GMS
		 */
		$longitud = $localizacion [1];
		
		$longitud_grados = reset ( explode ( ".", $longitud ) );
		
		$longitud_minutos_dc = (((($longitud - $longitud_grados) * 60) < 0) ? (($longitud - $longitud_grados) * 60) * - 1 : (($longitud - $longitud_grados) * 60));
		
		$longitud_minutos = reset ( explode ( ".", $longitud_minutos_dc ) );
		
		$longitud_segundos = (($longitud_minutos_dc - $longitud_minutos) * 60 < 0) ? ($longitud_minutos_dc - $longitud_minutos) * 60 * - 1 : ($longitud_minutos_dc - $longitud_minutos) * 60;
		setlocale ( LC_ALL, "es_CO.UTF-8" );
		
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



                        <page backtop='25mm' backbottom='10mm' backleft='10mm' backright='10mm' footer='page'>
                            <page_header>
                                 <table  style='width:100%;' >
                                          <tr>
                                                <td align='center' style='width:100%;border=none;' >
                                                <img src='" . $this->rutaURL . "frontera/css/imagen/logos_contrato.png'  width='500' height='45'>
                                                </td>
                                                <tr>
                                                <td style='width:100%;border:none;text-align:center;font-size:9px;'><b>004/009 ACTA DE ENTREGA DE SERVICIO DE BANDA ANCHA AL USUARIO</b></td>
                                                </tr>
                                                <tr>
                                                <td style='width:100%;border:none;text-align:center;'><br><br><b>004/009 ACTA DE ENTREGA DE SERVICIO DE BANDA ANCHA AL USUARIO</b></td>
                                                </tr>

                                        </tr>
                                    </table>

                        </page_header>
                       ";
		
		$contenidoPagina .= "
                            <br>
                            El suscrito beneficiario del Proyecto Conexiones Digitales II, cuyos datos se presentan a continuación:
                            <br>
                            <table width:100%;>
                                <tr>
                                    <td style='width:25%;'><b>Contrato de Servicio</b></td>
                                    <td colspan='3' style='width:75%;text-align:center;'><b>" . $_REQUEST ['numero_contrato'] . "</b></td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Beneficiario</td>
                                    <td colspan='3' style='width:75%;text-align:center;'><b>" . $_REQUEST ['nombre'] . " " . $_REQUEST ['primer_apellido'] . " " . $_REQUEST ['segundo_apellido'] . "</b></td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>No de Identificación</td>
                                    <td colspan='3' style='width:75%;text-align:center;'><b>" . number_format ( $_REQUEST ['identificacion'], 0, '', '.' ) . "</b></td>
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
                                    <td colspan='3' style='width:75%;text-align:center;'>" . $_REQUEST ['direccion'] . "</td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Departamento</td>
                                    <td style='width:25%;text-align:center;'>" . $_REQUEST ['departamento'] . "</td>
                                    <td style='width:25%;'>Municipio</td>
                                    <td style='width:25%;text-align:center;'>" . $_REQUEST ['municipio'] . "</td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Urbanización</td>
                                    <td colspan='3' style='width:75%;text-align:center;'>" . $_REQUEST ['urbanizacion'] . "</td>
                                </tr>
                                <tr>
                                    <td style='width:25%;'>Latitud</td>
                                    <td style='width:25%;text-align:center;'>" . $latitud . "</td>
                                    <td style='width:25%;'>Longitud</td>
                                    <td style='width:25%;text-align:center;'>" . $longitud . "</td>
                                </tr>
                            </table>
                            <br>
                            <table  style='width:100%;' >
                                          <tr>
                                                <td align='center' style='width:100%;border=none;' ><b>CERTIFICA:</b></td>

                                        </tr>
                            </table>
                            1. Que ha recibido a satisfacción los equipos y el servicio de acceso de banda ancha con las características descritas a continuación:<br>
        		 	<table width:100%;>
                        <tr>
	                        <td align='center'style='width:15%;'><b>EQUIPO</b></td>
							<td align='center'style='width:20%;'><b>MAC</b></td>
	                        <td align='center'style='width:21%;'><b>SERIAL</b></td>
	                        <td align='center'style='width:14%;'><b>MARCA</b></td>
	                        <td align='center'style='width:14%;'><b>CANT</b></td>
					 		<td align='center'style='width:16%;'><b>IP</b></td>
                       	</tr>
                        <tr>
                        	<td align='center'style='width:15%;'>ESCLAVO</td>
                            <td align='center'style='width:20%;'>" . $_REQUEST ['mac_esc'] . "<br>" . $_REQUEST ['mac2_esc'] . " </td>
                            <td align='center'style='width:21%;'>" . $_REQUEST ['serial_esc'] . " </td>
                            <td align='center'style='width:14%;'>" . $_REQUEST ['marca_esc'] . " </td>
				 			<td align='center'style='width:14%;'>" . $_REQUEST ['cant_esc'] . " </td>
							<td align='center'style='width:16%;'>" . $_REQUEST ['ip_esc'] . " </td>
                        </tr>
                    </table>
					<br>
					<b>Estado del Servicio</b>
					<table width:100%;>
						<tr>
							<td align='rigth'style='width:20%;'><b>Tipo de Tecnología</b></td>
							<td colspan='4' align='center'style='width:80%;'>" . $_REQUEST ['tipo_tecnologia'] . "</td>
						</tr>
                        <tr>
							<td align='rigth'style='width:20%;'><b></b></td>
							<td align='center'style='width:15%;'><b>Hora de Prueba</b></td>
	                        <td align='center'style='width:20%;'><b>Resultado</b></td>
	                        <td align='center'style='width:20%;'><b>Unidad</b></td>
							<td align='center'style='width:25%;'><b>Observaciones</b></td>
                       	</tr>
                        <tr>
                        	<td align='rigth'style='width:20%;'><b>Velocidad de Subida</b></td>
                        	<td align='center'style='width:15%;'>" . $_REQUEST ['hora_prueba_vs'] . " </td>
                            <td align='center'style='width:20%;'>" . $_REQUEST ['resultado_vs'] . " </td>
                            <td align='center'style='width:20%;'>" . $_REQUEST ['unidad_vs'] . "</td>
                            <td align='center'style='width:25%;'>" . $_REQUEST ['observaciones_vs'] . " </td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Velocidad de Bajada</b></td>
                        	<td align='center'style='width:15%;'>" . $_REQUEST ['hora_prueba_vb'] . " </td>
                            <td align='center'style='width:20%;'>" . $_REQUEST ['resultado_vb'] . " </td>
                            <td align='center'style='width:20%;'>" . $_REQUEST ['unidad_vb'] . " </td>
                            <td align='center'style='width:25%;'>" . $_REQUEST ['observaciones_vb'] . " </td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Ping 1</b></td>
                        	<td align='center'style='width:15%;'>" . $_REQUEST ['hora_prueba_p1'] . " </td>
                            <td align='center'style='width:20%;'>" . $_REQUEST ['resultado_p1'] . " </td>
                            <td align='center'style='width:20%;'>" . $_REQUEST ['unidad_p1'] . " </td>
                            <td align='center'style='width:25%;'>" . $_REQUEST ['observaciones_p1'] . " </td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Ping 2</b></td>
                        	<td align='center'style='width:15%;'>" . $_REQUEST ['hora_prueba_p2'] . " </td>
                            <td align='center'style='width:20%;'>" . $_REQUEST ['resultado_p2'] . " </td>
                            <td align='center'style='width:20%;'>" . $_REQUEST ['unidad_p2'] . "</td>
                            <td align='center'style='width:25%;'>" . $_REQUEST ['observaciones_p2'] . " </td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Ping 3</b></td>
                        	<td align='center'style='width:15%;'>" . $_REQUEST ['hora_prueba_p3'] . " </td>
                            <td align='center'style='width:20%;'>" . $_REQUEST ['resultado_p3'] . " </td>
                            <td align='center'style='width:20%;'>" . $_REQUEST ['unidad_p3'] . " </td>
                            <td align='center'style='width:25%;'>" . $_REQUEST ['observaciones_p3'] . "</td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Traceroute</b></td>
                        	<td align='center'style='width:15%;'>" . $_REQUEST ['hora_prueba_tr1'] . " </td>
                            <td align='center'style='width:20%;'>" . $_REQUEST ['resultado_tr1'] . " </td>
                            <td align='center'style='width:20%;'>" . $_REQUEST ['unidad_tr1'] . "</td>
                            <td align='center'style='width:25%;'>" . $_REQUEST ['observaciones_tr1'] . "</td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Traceroute</b></td>
                        	<td align='center'style='width:15%;'>" . $_REQUEST ['hora_prueba_tr2'] . " </td>
                            <td align='center'style='width:20%;'>" . $_REQUEST ['resultado_tr2'] . " </td>
                            <td align='center'style='width:20%;'>" . $_REQUEST ['unidad_tr2'] . "</td>
                            <td align='center'style='width:25%;'>" . $_REQUEST ['observaciones_tr2'] . "</td>
                        </tr>
                    </table>
                            2. Que las obras civiles realizadas en el proceso de instalación por parte del contratista fueron culminadas satisfactoriamente, sin afectar la infraestructura y la estética del lugar, cumpliendo con las observaciones realizadas durante la instalación.<br><br>
                            3. Que acepta y reconoce que a la fecha ha consultado o ha sido informado por la Corporación Politécnica Nacional de Colombia sobre las condiciones mínimas requeridas de los equipos necesarios para hacer uso de los servicios contratados.<br><br>
                            4. Que se compromete a informar oportunamente a la Corporación Politécnica Nacional de Colombia sobre cualquier daño, pérdida o afectación de los equipos antes mencionados.<br>
                                <br>
                            Para constancia de lo anterior, firma en la ciudad de " . $_REQUEST ['municipio'] . ", municipio de " . $_REQUEST ['municipio'] . ", departamento de " . $_REQUEST ['departamento'] . ", el día " . $dia . " De " . $mes . " De " . $anno . ".
                            <br>
                            <br>
                            <table width:100%;>
                                <tr>
                                    <td rowspan='2' align='rigth' style='vertical-align:top;width:50%;'>Firma: </td>
                                    <td style='width:50%;text-align:center;'><b>" . $_REQUEST ['nombre'] . " " . $_REQUEST ['primer_apellido'] . " " . $_REQUEST ['segundo_apellido'] . "</b></td>
                                </tr>
                                <tr>
                                    <td style='width:50%;text-align:center;'><b>" . number_format ( $_REQUEST ['identificacion'], 0, '', '.' ) . "</b></td>
                                </tr>
                            </table>


                    ";
		
		$contenidoPagina .= "</page>";
		
		$this->contenidoPagina = $contenidoPagina;
	}
}
$miDocumento = new GenerarDocumento ( $this->sql );

?>
