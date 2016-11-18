<?php

namespace reportes\instalacionesGenerales\entidad;

$ruta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/plugin/html2pfd/";

require_once $ruta . "/plugin/PHPExcel/Classes/PHPExcel.php";
class GenerarReporteExcelInstalaciones {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public $miSql;
	public $conexion;
	public $proyectos;
	public $objCal;
	public $informacion;
	public function __construct($sql, $proyectos) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->miSql = $sql;
		$this->proyectos = $proyectos;
		
		/**
		 * 1.
		 * Estruturamiento Información OpenProject
		 */
		$this->estruturarInformacion ();
		
		/**
		 * 2.
		 * Registrar Información Almacén de datos
		 */
		$this->registrarAlmacenDatos ();
	}
	
	public function estruturarInformacion() {
		
		$i = 4;
		
// 		var_dump($this->proyectos[0]['campos_parametrizados']);die;
		
		foreach ( $this->proyectos as $key => $value ) {
			
			if ($value ['tipo_proyecto'] === "core") {
				$llave_Ins = $key;
			}
			
			$var = strpos ( $value ['info'] ['identifier'], 'becera' );
			
			if ($value ['tipo_proyecto'] !== "core") {
				
				$this->informacion [$key] ['a_'] = 'Politécnica';
				
				// $this->objCal->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
				// $this->objCal->setActiveSheetIndex(0)
				// ->setCellValue('A' . $i, 'Politécnica')
				// ->getStyle("A" . $i)->applyFromArray($styleCentrado);
				
				{
					// Avance y Estado Instalación NOC
					
					{
						
						// Centro de Gestión
						$contenido_CentroGestion = $this->compactarAvances ( $this->proyectos [$llave_Ins], "Centro de Gestión" );
						$paquete_CentroGestion = $this->consultarPaqueteTrabajo ( $this->proyectos [$llave_Ins], "Centro de Gestión" );
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('B' . $i, (($contenido_CentroGestion != false) ? $contenido_CentroGestion : ""))
						// ->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);
						
						$this->informacion [$key] ['b_'] = ($contenido_CentroGestion != false) ? $contenido_CentroGestion : "";
						// $this->informacion[$key]['CentroGestion']['paquetes'] = ($paquete_CentroGestion != false) ? $paquete_CentroGestion : "";
						$this->informacion [$key] ['c_'] = (isset ( $paquete_CentroGestion ['cf_12'] ) && ! is_null ( $paquete_CentroGestion ['cf_12'] )) ? $paquete_CentroGestion ['cf_12'] : "";
						$this->informacion [$key] ['d_'] = (isset ( $paquete_CentroGestion ['cf_13'] ) && ! is_null ( $paquete_CentroGestion ['cf_13'] )) ? $paquete_CentroGestion ['cf_13'] : "";
						$this->informacion [$key] ['e_'] = (isset ( $paquete_CentroGestion ['start_date'] ) && $paquete_CentroGestion ['start_date'] != '') ? $paquete_CentroGestion ['start_date'] : "";
						$this->informacion [$key] ['f_'] = (isset ( $paquete_CentroGestion ['due_date'] ) && $paquete_CentroGestion ['due_date'] != '') ? $paquete_CentroGestion ['due_date'] : "";
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('C' . $i, ((isset($paquete_CentroGestion['cf_12']) && !is_null($paquete_CentroGestion['cf_12'])) ? $paquete_CentroGestion['cf_12'] : ""))
						// ->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('D' . $i, ((isset($paquete_CentroGestion['cf_13']) && !is_null($paquete_CentroGestion['cf_13'])) ? $paquete_CentroGestion['cf_13'] : ""))
						// ->getStyle('D' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('E' . $i, ((isset($paquete_CentroGestion['start_date']) && $paquete_CentroGestion['start_date'] != '') ? $paquete_CentroGestion['start_date'] : ""))
						// ->getStyle('E' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('F' . $i, ((isset($paquete_CentroGestion['due_date']) && $paquete_CentroGestion['due_date'] != '') ? $paquete_CentroGestion['due_date'] : ""))
						// ->getStyle('F' . $i)->applyFromArray($styleCentradoVertical);
					}
					
					{
						// Mesa Ayuda
						$contenido_MesaAyuda = $this->compactarAvances ( $this->proyectos [$llave_Ins], "Mesa  de Ayuda" );
						$paquete_MesaAyuda = $this->consultarPaqueteTrabajo ( $this->proyectos [$llave_Ins], "Mesa  de Ayuda" );
						
						$this->informacion [$key] ['g_'] = ($contenido_MesaAyuda != false) ? $contenido_MesaAyuda : "";
						// $this->informacion[$key]['MesaAyuda']['paquetes'] = ($paquete_MesaAyuda != false) ? $paquete_MesaAyuda : "";
						$this->informacion [$key] ['h_'] = (isset ( $paquete_MesaAyuda ['cf_12'] ) && ! is_null ( $paquete_MesaAyuda ['cf_12'] )) ? $paquete_MesaAyuda ['cf_12'] : "";
						$this->informacion [$key] ['i_'] = (isset ( $paquete_MesaAyuda ['cf_13'] ) && ! is_null ( $paquete_MesaAyuda ['cf_13'] )) ? $paquete_MesaAyuda ['cf_13'] : "";
						$this->informacion [$key] ['j_'] = (isset ( $paquete_MesaAyuda ['start_date'] ) && $paquete_MesaAyuda ['start_date'] != '') ? $paquete_MesaAyuda ['start_date'] : "";
						$this->informacion [$key] ['k_'] = (isset ( $paquete_MesaAyuda ['due_date'] ) && $paquete_MesaAyuda ['due_date'] != '') ? $paquete_MesaAyuda ['due_date'] : "";
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('G' . $i, (($contenido_MesaAyuda != false) ? $contenido_MesaAyuda : ""))
						// ->getStyle('G' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('H' . $i, ((isset($paquete_MesaAyuda['cf_12']) && !is_null($paquete_MesaAyuda['cf_12'])) ? $paquete_MesaAyuda['cf_12'] : ""))
						// ->getStyle('H' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('I' . $i, ((isset($paquete_MesaAyuda['cf_13']) && !is_null($paquete_MesaAyuda['cf_13'])) ? $paquete_MesaAyuda['cf_13'] : ""))
						// ->getStyle('I' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('J' . $i, ((isset($paquete_MesaAyuda['start_date']) && $paquete_MesaAyuda['start_date'] != '') ? $paquete_MesaAyuda['start_date'] : ""))
						// ->getStyle('J' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('K' . $i, ((isset($paquete_MesaAyuda['due_date']) && $paquete_MesaAyuda['due_date'] != '') ? $paquete_MesaAyuda['due_date'] : ""))
						// ->getStyle('K' . $i)->applyFromArray($styleCentradoVertical);
					}
					
					{
						
						// Otros Sistemas
						$contenido_OtrosSistemas = $this->compactarAvances ( $this->proyectos [$llave_Ins], "Otros Equipos o Sistemas en el NOC" );
						$paquete_OtrosSistemas = $this->consultarPaqueteTrabajo ( $this->proyectos [$llave_Ins], "Otros Equipos o Sistemas en el NOC" );
						
						$this->informacion [$key] ['l_'] = ($contenido_OtrosSistemas != false) ? $contenido_OtrosSistemas : "";
						// $this->informacion[$key]['OtrosSistemas']['paquetes'] = ($paquete_OtrosSistemas != false) ? $paquete_OtrosSistemas : "";
						$this->informacion [$key] ['m_'] = (! is_null ( $paquete_OtrosSistemas ['cf_12'] )) ? $paquete_OtrosSistemas ['cf_12'] : "";
						$this->informacion [$key] ['n_'] = (! is_null ( $paquete_OtrosSistemas ['cf_13'] )) ? $paquete_OtrosSistemas ['cf_13'] : "";
						$this->informacion [$key] ['o_'] = (isset ( $paquete_OtrosSistemas ['start_date'] ) && $paquete_OtrosSistemas ['start_date'] != '') ? $paquete_OtrosSistemas ['start_date'] : "";
						$this->informacion [$key] ['p_'] = (isset ( $paquete_OtrosSistemas ['due_date'] ) && $paquete_OtrosSistemas ['due_date'] != '') ? $paquete_OtrosSistemas ['due_date'] : "";
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('L' . $i, (($contenido_OtrosSistemas != false) ? $contenido_OtrosSistemas : ""))
						// ->getStyle('L' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('M' . $i, ((!is_null($paquete_OtrosSistemas['cf_12'])) ? $paquete_OtrosSistemas['cf_12'] : ""))
						// ->getStyle('M' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('N' . $i, ((!is_null($paquete_OtrosSistemas['cf_13'])) ? $paquete_OtrosSistemas['cf_13'] : ""))
						// ->getStyle('N' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('O' . $i, ((isset($paquete_OtrosSistemas['start_date']) && $paquete_OtrosSistemas['start_date'] != '') ? $paquete_OtrosSistemas['start_date'] : ""))
						// ->getStyle('O' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('P' . $i, ((isset($paquete_OtrosSistemas['due_date']) && $paquete_OtrosSistemas['due_date'] != '') ? $paquete_OtrosSistemas['due_date'] : ""))
						// ->getStyle('P' . $i)->applyFromArray($styleCentradoVertical);
					}

					$paquete_avance_instalacion_noc = $this->consultarPaqueteTrabajo ( $this->proyectos [$llave_Ins], "Porcentaje Avance", "Avance y  Estado Instalación NOC");
					$this->informacion [$key] ['q_'] = $paquete_avance_instalacion_noc ['done_ratio'];
					$this->informacion [$key] ['r_'] = (isset ( $paquete_OtrosSistemas ['cf_15'] ) && ! is_null ( $paquete_avance_instalacion_noc ['cf_15'] )) ? $paquete_avance_instalacion_noc ['cf_15'] : "";
					
					
					// $this->objCal->setActiveSheetIndex(0)
					// ->setCellValue('Q' . $i, "% " . $paquete_avance_instalacion_noc['done_ratio'])
					// ->getStyle('Q' . $i)->applyFromArray($styleCentrado);
					
					// $this->objCal->setActiveSheetIndex(0)
					// ->setCellValue('R' . $i, ((isset($paquete_OtrosSistemas['cf_15']) && !is_null($paquete_avance_instalacion_noc['cf_15'])) ? $paquete_avance_instalacion_noc['cf_15'] : ""))
					// ->getStyle('R' . $i)->applyFromArray($styleCentradoVertical);
				}
				
				$value ['campos_personalizados'] = $value ['info'] ['custom_fields'];
				
				$clave_departamento = array_search ( 1, array_column ( $value ['campos_personalizados'], 'id' ), true );
				$longitud = strlen ( $value ['campos_personalizados'] [$clave_departamento] ['value'] );
				$departamento = substr ( $value ['campos_personalizados'] [$clave_departamento] ['value'], 5, $longitud );
				
				// $this->informacion[$key]['departamento']['clave']= $clave_departamento;
				// $this->informacion[$key]['departamento']['longitud']= $longitud;
				$this->informacion [$key] ['s_'] = $departamento;
				
				// $this->objCal->setActiveSheetIndex(0)
				// ->setCellValue('S' . $i, $departamento)
				// ->getStyle("S" . $i)->applyFromArray($styleCentradoVertical);
				
				$clave_municipio = array_search ( 2, array_column ( $value ['campos_personalizados'], 'id' ), true );
				$longitud = strlen ( $value ['campos_personalizados'] [$clave_municipio] ['value'] );
				$municipio = substr ( $value ['campos_personalizados'] [$clave_municipio] ['value'], 8, $longitud );
				$codigo_dane = substr ( $value ['campos_personalizados'] [$clave_municipio] ['value'], 0, 4 );
				
				// $this->informacion[$key]['municipio']['clave']= $clave_municipio;
				// $this->informacion[$key]['municipio']['longitud']= $longitud;
				$this->informacion [$key] ['t_'] = $municipio;
				$this->informacion [$key] ['u_'] = $codigo_dane;
				
				$clave_urbanizacion = array_search ( 33, array_column ( $value ['campos_personalizados'], 'id' ), true );
				$urbanizacion = $value ['campos_personalizados'] [$clave_urbanizacion] ['value'];
				
				// $this->informacion[$key]['urbanizacion']['clave']= $clave_urbanizacion;
				$this->informacion [$key] ['v_'] = $urbanizacion;
				
				// $this->objCal->setActiveSheetIndex(0)
				// ->setCellValue('T' . $i, $municipio)
				// ->getStyle("T" . $i)->applyFromArray($styleCentradoVertical);
				
				// $codigo_dane = substr($value['campos_personalizados'][$clave_municipio]['value'], 0, 4);
				// $this->objCal->setActiveSheetIndex(0)
				// ->setCellValue('U' . $i, $codigo_dane)
				// ->getStyle("U" . $i)->applyFromArray($styleCentradoVertical);
				
				// $clave_urbanizacion = array_search(33, array_column($value['campos_personalizados'], 'id'), true);
				// $urbanizacion = $value['campos_personalizados'][$clave_urbanizacion]['value'];
				
				// $this->objCal->setActiveSheetIndex(0)
				// ->setCellValue('V' . $i, $urbanizacion)
				// ->getStyle("V" . $i)->applyFromArray($styleCentradoVertical);
				
				{
					// Avance y Estado Instalación Nodo Cabecera
					
					$clave_cabecera_campo = array_search ( 43, array_column ( $value ['campos_personalizados'], 'id' ), true );
					$cabecera_campo = $value ['campos_personalizados'] [$clave_cabecera_campo] ['value'];
					$clave_cabecera_proyecto = array_search ( $cabecera_campo, array_column ( $this->proyectos, 'name' ), true );
					$cabecera = $this->proyectos [$clave_cabecera_proyecto];
					
					// $this->informacion[$key]['cabecera']['clave_campo']= $municipio;
					// $this->informacion[$key]['cabecera']['campo']= $municipio;
					// $this->informacion[$key]['cabecera']['clave_proyecto']= $municipio;
					// $this->informacion[$key]['cabecera']['cabecera']= $municipio;
					
					{
						// Infraestructura Nodos
						
						$contenido_InfraestructuraNodos = $this->compactarAvances ( $cabecera, "Infraestructura Nodos" );
						$paquete_InfraestructuraNodos = $this->consultarPaqueteTrabajo ( $cabecera, "Infraestructura Nodos" );
						
						$this->informacion [$key] ['w_'] = ($contenido_InfraestructuraNodos != false) ? $contenido_InfraestructuraNodos : "";
						$this->informacion [$key] ['x_'] = (isset ( $paquete_InfraestructuraNodos ['cf_14'] ) && ! is_null ( $paquete_InfraestructuraNodos ['cf_14'] )) ? $paquete_InfraestructuraNodos ['cf_14'] : "";
						$this->informacion [$key] ['y_'] = (isset ( $paquete_InfraestructuraNodos ['due_date'] ) && $paquete_InfraestructuraNodos ['due_date'] != '') ? $paquete_InfraestructuraNodos ['due_date'] : "";

						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('W' . $i, (($contenido_InfraestructuraNodos != false) ? $contenido_InfraestructuraNodos : ""))
						// ->getStyle("W" . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('X' . $i, ((isset($paquete_InfraestructuraNodos['cf_14']) && !is_null($paquete_InfraestructuraNodos['cf_14'])) ? $paquete_InfraestructuraNodos['cf_14'] : ""))
						// ->getStyle('X' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('Y' . $i, ((isset($paquete_InfraestructuraNodos['due_date']) && $paquete_InfraestructuraNodos['due_date'] != '') ? $paquete_InfraestructuraNodos['due_date'] : ""))
						// ->getStyle('Y' . $i)->applyFromArray($styleCentradoVertical);
					}
					{
						// Instalación Red troncal o interconexión ISP
						
						$contenido_RedTroncalISP = $this->compactarAvances ( $cabecera, "Instalación Red troncal o interconexión ISP" );
						$paquete_RedTroncalISP = $this->consultarPaqueteTrabajo ( $cabecera, "Instalación Red troncal o interconexión ISP" );
						
						$this->informacion [$key] ['z_'] = ($contenido_RedTroncalISP != false) ? $contenido_RedTroncalISP : "";
						$this->informacion [$key] ['a_a'] = (isset ( $paquete_RedTroncalISP ['cf_14'] ) && ! is_null ( $paquete_RedTroncalISP ['cf_14'] )) ? $paquete_RedTroncalISP ['cf_14'] : "";
						$this->informacion [$key] ['a_b'] = (isset ( $paquete_RedTroncalISP ['cf_16'] ) && ! is_null ( $paquete_RedTroncalISP ['cf_16'] )) ? $paquete_RedTroncalISP ['cf_16'] : "";
						$this->informacion [$key] ['a_c'] = (isset ( $paquete_RedTroncalISP ['cf_17'] ) && ! is_null ( $paquete_RedTroncalISP ['cf_17'] )) ? $paquete_RedTroncalISP ['cf_17'] : "";
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('Z' . $i, (($contenido_RedTroncalISP != false) ? $contenido_RedTroncalISP : ""))
						// ->getStyle("Z" . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AA' . $i, ((isset($paquete_RedTroncalISP['cf_14']) && !is_null($paquete_RedTroncalISP['cf_14'])) ? $paquete_RedTroncalISP['cf_14'] : ""))
						// ->getStyle('AA' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AB' . $i, ((isset($paquete_RedTroncalISP['cf_16']) && !is_null($paquete_RedTroncalISP['cf_16'])) ? $paquete_RedTroncalISP['cf_16'] : ""))
						// ->getStyle('AB' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AC' . $i, ((isset($paquete_RedTroncalISP['cf_17']) && !is_null($paquete_RedTroncalISP['cf_17'])) ? $paquete_RedTroncalISP['cf_17'] : ""))
						// ->getStyle('AC' . $i)->applyFromArray($styleCentradoVertical);
					}
					
					// {
					// //Instalación Red troncal o interconexión ISP
					
					// $contenido_RedTroncalISP = $this->compactarAvances($cabecera, "Instalación red troncal o interconexión ISP");
					// $paquete_RedTroncalISP = $this->consultarPaqueteTrabajo($cabecera, "Instalación red troncal o interconexión ISP");
					
					// print_r($this->informacion[$key]);die;
					
					// $this->objCal->setActiveSheetIndex(0)
					// ->setCellValue('Z' . $i, (($contenido_RedTroncalISP != false) ? $contenido_RedTroncalISP : ""))
					// ->getStyle("Z" . $i)->applyFromArray($styleCentradoVertical);
					
					// $this->objCal->setActiveSheetIndex(0)
					// ->setCellValue('AA' . $i, ((isset($paquete_RedTroncalISP['cf_14']) && !is_null($paquete_RedTroncalISP['cf_14'])) ? $paquete_RedTroncalISP['cf_14'] : ""))
					// ->getStyle('AA' . $i)->applyFromArray($styleCentradoVertical);
					
					// $this->objCal->setActiveSheetIndex(0)
					// ->setCellValue('AB' . $i, ((isset($paquete_RedTroncalISP['cf_16']) && !is_null($paquete_RedTroncalISP['cf_16'])) ? $paquete_RedTroncalISP['cf_16'] : ""))
					// ->getStyle('AB' . $i)->applyFromArray($styleCentradoVertical);
					
					// $this->objCal->setActiveSheetIndex(0)
					// ->setCellValue('AC' . $i, ((isset($paquete_RedTroncalISP['cf_17']) && !is_null($paquete_RedTroncalISP['cf_17'])) ? $paquete_RedTroncalISP['cf_17'] : ""))
					// ->getStyle('AC' . $i)->applyFromArray($styleCentradoVertical);
					// }
					
					{
						// Instalación Red troncal o interconexión ISP
						
						$paquete_InstFuncEquiNodoCab = $this->consultarPaqueteTrabajo ( $cabecera, "Instalación y Puesta en Funcionamiento Equipos" );
						
						$this->informacion [$key] ['a_d'] = (isset ( $paquete_InstFuncEquiNodoCab ['cf_45'] ) && ! is_null ( $paquete_InstFuncEquiNodoCab ['cf_45'] )) ? $paquete_InstFuncEquiNodoCab ['cf_45'] : "";
						$this->informacion [$key] ['a_e'] = (isset ( $paquete_InstFuncEquiNodoCab ['cf_46'] ) && ! is_null ( $paquete_InstFuncEquiNodoCab ['cf_46'] )) ? $paquete_InstFuncEquiNodoCab ['cf_46'] : "";
						$this->informacion [$key] ['a_f'] = (isset ( $paquete_InstFuncEquiNodoCab ['cf_47'] ) && ! is_null ( $paquete_InstFuncEquiNodoCab ['cf_47'] )) ? $paquete_InstFuncEquiNodoCab ['cf_47'] : "";
						$this->informacion [$key] ['a_g'] = (isset ( $paquete_InstFuncEquiNodoCab ['cf_16'] ) && ! is_null ( $paquete_InstFuncEquiNodoCab ['cf_16'] )) ? $paquete_InstFuncEquiNodoCab ['cf_16'] : "";
						$this->informacion [$key] ['a_h'] = (isset ( $paquete_InstFuncEquiNodoCab ['cf_17'] ) && ! is_null ( $paquete_InstFuncEquiNodoCab ['cf_17'] )) ? $paquete_InstFuncEquiNodoCab ['cf_17'] : "";
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AD' . $i, ((isset($paquete_InstFuncEquiNodoCab['cf_45']) && !is_null($paquete_InstFuncEquiNodoCab['cf_45'])) ? $paquete_InstFuncEquiNodoCab['cf_45'] : ""))
						// ->getStyle('AD' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AE' . $i, ((isset($paquete_InstFuncEquiNodoCab['cf_46']) && !is_null($paquete_InstFuncEquiNodoCab['cf_46'])) ? $paquete_InstFuncEquiNodoCab['cf_46'] : ""))
						// ->getStyle('AE' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AF' . $i, ((isset($paquete_InstFuncEquiNodoCab['cf_47']) && !is_null($paquete_InstFuncEquiNodoCab['cf_47'])) ? $paquete_InstFuncEquiNodoCab['cf_47'] : ""))
						// ->getStyle('AF' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AG' . $i, ((isset($paquete_InstFuncEquiNodoCab['cf_16']) && !is_null($paquete_InstFuncEquiNodoCab['cf_16'])) ? $paquete_InstFuncEquiNodoCab['cf_16'] : ""))
						// ->getStyle('AG' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AH' . $i, ((isset($paquete_InstFuncEquiNodoCab['cf_17']) && !is_null($paquete_InstFuncEquiNodoCab['cf_17'])) ? $paquete_InstFuncEquiNodoCab['cf_17'] : ""))
						// ->getStyle('AH' . $i)->applyFromArray($styleCentradoVertical);
					}
					
					{
						$cabecera ['campos_personalizados'] = $cabecera ['info'] ['custom_fields'];
						
						$cabecera_key_fecha_funcionamiento = array_search ( 48, array_column ( $cabecera ['campos_personalizados'], 'id' ), true );
						$fecha_funcionamiento_cabecera = $cabecera ['campos_personalizados'] [$cabecera_key_fecha_funcionamiento] ['value'];
						$paquete_AvancInstNodoCab = $this->consultarPaqueteTrabajo ( $cabecera, "Porcentaje Avance", "Avance y Estado Instalación Nodo Cabecera" );
						
						$this->informacion [$key] ['a_i'] = (! is_null ( $fecha_funcionamiento_cabecera ) && $fecha_funcionamiento_cabecera != '' && $cabecera_key_fecha_funcionamiento != false) ? $fecha_funcionamiento_cabecera : "";
						// $this->informacion[$key]['instalacion_puesta_funciont']['fecha_func_cab'] = $fecha_funcionamiento_cabecera;
						$this->informacion [$key] ['a_j'] = $paquete_AvancInstNodoCab ['done_ratio'];
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AI' . $i, ((!is_null($fecha_funcionamiento_cabecera) && $fecha_funcionamiento_cabecera != '' && $cabecera_key_fecha_funcionamiento != false) ? $fecha_funcionamiento_cabecera : ""))
						// ->getStyle('AI' . $i)->applyFromArray($styleCentradoVertical);
						
						// $paquete_AvancInstNodoCab = $this->consultarPaqueteTrabajo($cabecera, "Avance y estado instalación nodo cabecera");
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AJ' . $i, $paquete_AvancInstNodoCab['done_ratio'] . "%")
						// ->getStyle('AJ' . $i)->applyFromArray($styleCentrado);
					}
				}
				
				{
					// Avance y Estado Instalación Red de Distribución
					
					{
						// Estado Construcción Red de Distribución
						
						$contenido_ConsRedDistrb = $this->compactarAvances ( $value, "Estado Construcción Red de Distribución" );
						$paquete_ConsRedDistrb = $this->consultarPaqueteTrabajo ( $value, "Estado Construcción Red de Distribución" );
						
						$this->informacion [$key] ['a_k'] = ($contenido_ConsRedDistrb != false) ? $contenido_ConsRedDistrb : "";
						$this->informacion [$key] ['a_l'] = (! is_null ( $paquete_ConsRedDistrb ['cf_14'] )) ? $paquete_ConsRedDistrb ['cf_14'] : "";
						$this->informacion [$key] ['a_m'] = (isset ( $paquete_ConsRedDistrb ['due_date'] ) && $paquete_ConsRedDistrb ['due_date'] != '') ? $paquete_ConsRedDistrb ['due_date'] : "";
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AK' . $i, (($contenido_ConsRedDistrb != false) ? $contenido_ConsRedDistrb : ""))
						// ->getStyle("AK" . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AL' . $i, ((!is_null($paquete_ConsRedDistrb['cf_14'])) ? $paquete_ConsRedDistrb['cf_14'] : ""))
						// ->getStyle('AL' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AM' . $i, ((isset($paquete_ConsRedDistrb['due_date']) && $paquete_ConsRedDistrb['due_date'] != '') ? $paquete_ConsRedDistrb['due_date'] : ""))
						// ->getStyle('AM' . $i)->applyFromArray($styleCentradoVertical);
					}
					
					{
						// Tendido y puesta en funcionamiento Fibra óptica
						
						$contenido_FunFibrOp = $this->compactarAvances ( $value, "Tendido y Puesta en Funcionamiento Fibra Óptica" );
						$paquete_FunFibrOp = $this->consultarPaqueteTrabajo ( $value, "Tendido y Puesta en Funcionamiento Fibra Óptica" );
						
						$this->informacion [$key] ['a_n'] = ($contenido_FunFibrOp != false) ? $contenido_FunFibrOp : "";
						$this->informacion [$key] ['a_o'] = (! is_null ( $paquete_FunFibrOp ['cf_14'] )) ? $paquete_FunFibrOp ['cf_14'] : "";
						$this->informacion [$key] ['a_p'] = (isset ( $paquete_FunFibrOp ['cf_16'] ) && $paquete_FunFibrOp ['cf_16'] != '') ? $paquete_FunFibrOp ['cf_16'] : "";
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AN' . $i, (($contenido_FunFibrOp != false) ? $contenido_FunFibrOp : ""))
						// ->getStyle("AN" . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AO' . $i, ((!is_null($paquete_FunFibrOp['cf_14'])) ? $paquete_FunFibrOp['cf_14'] : ""))
						// ->getStyle('AO' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AP' . $i, ((isset($paquete_FunFibrOp['cf_16']) && $paquete_FunFibrOp['cf_16'] != '') ? $paquete_FunFibrOp['cf_16'] : ""))
						// ->getStyle('AP' . $i)->applyFromArray($styleCentradoVertical);
					}
					
					{
						
						$paquete_AvanRedDist = $this->consultarPaqueteTrabajo ( $value, "Porcentaje Avance", "Avance y Estado Instalación Red de Distribución" );
						
						$this->informacion [$key] ['a_q'] = ($paquete_AvanRedDist != false) ? $paquete_AvanRedDist : "";
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AQ' . $i, $paquete_AvanRedDist['done_ratio'] . "%")
						// ->getStyle('AQ' . $i)->applyFromArray($styleCentrado);
					}
				}
				
				{
					
					// Avance y Estado Instalación Nodo EOC
					
					{
						// Estado Construcción Red de Distribución
						
						//Aqui
						//$contenido_ConsRedDistrb = $this->compactarAvances ( $value, "Infraestructura nodo (Avance y estado instalación nodo EOC)", "description" );
						//$paquete_ConsRedDistrb = $this->consultarPaqueteTrabajo ( $value, "Infraestructura nodo (Avance y estado instalación nodo EOC)", "description" );
						
						//$this->informacion [$key] ['a_r'] = ($contenido_ConsRedDistrb != false) ? $contenido_ConsRedDistrb : "";
						//$this->informacion [$key] ['a_s'] = (! is_null ( $paquete_ConsRedDistrb ['cf_14'] )) ? $paquete_ConsRedDistrb ['cf_14'] : "";
						//$this->informacion [$key] ['a_t'] = (isset ( $paquete_ConsRedDistrb ['due_date'] ) && $paquete_ConsRedDistrb ['due_date'] != '') ? $paquete_ConsRedDistrb ['due_date'] : "";
						
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AR' . $i, (($contenido_ConsRedDistrb != false) ? $contenido_ConsRedDistrb : ""))
						// ->getStyle("AR" . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AS' . $i, ((!is_null($paquete_ConsRedDistrb['cf_14'])) ? $paquete_ConsRedDistrb['cf_14'] : ""))
						// ->getStyle('AS' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AT' . $i, ((isset($paquete_ConsRedDistrb['due_date']) && $paquete_ConsRedDistrb['due_date'] != '') ? $paquete_ConsRedDistrb['due_date'] : ""))
						// ->getStyle('AT' . $i)->applyFromArray($styleCentradoVertical);
					}
					
					{
						// Instalación y Puesta en Funcionamiento Equipos
						
						//Aqui
						//$contenido_PFuncEqEOC = $this->compactarAvances ( $value, "Instalación y puesta en funcionamiento equipos (Avance y estado instalación nodo EOC)", "description" );
						//$paquete_PFuncEqEOC = $this->consultarPaqueteTrabajo ( $value, "Instalación y puesta en funcionamiento equipos (Avance y estado instalación nodo EOC)", "description" );
						
						//$this->informacion [$key] ['a_w'] = (isset ( $paquete_PFuncEqEOC ['cf_46'] ) && ! is_null ( $paquete_PFuncEqEOC ['cf_46'] )) ? $paquete_PFuncEqEOC ['cf_46'] : "";
						//$this->informacion [$key] ['a_x'] = (isset ( $paquete_PFuncEqEOC ['cf_16'] ) && ! is_null ( $paquete_PFuncEqEOC ['cf_16'] )) ? $paquete_PFuncEqEOC ['cf_16'] : "";
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AW' . $i, ((isset($paquete_PFuncEqEOC['cf_46']) && !is_null($paquete_PFuncEqEOC['cf_46'])) ? $paquete_PFuncEqEOC['cf_46'] : ""))
						// ->getStyle('AW' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AX' . $i, ((isset($paquete_PFuncEqEOC['cf_16']) && !is_null($paquete_PFuncEqEOC['cf_16'])) ? $paquete_PFuncEqEOC['cf_16'] : ""))
						// ->getStyle('AX' . $i)->applyFromArray($styleCentradoVertical);
					}
					
					{
						unset ( $llaveFechaFuncionamiento );
						
						$paquete_AvancInstNodoEoc = $this->consultarPaqueteTrabajo ( $value, "Porcentaje Avance", "Avance y Estado Instalación Nodo EOC" );
						
						// $this->informacion[$key]['AvancInstNodoEoc']['contenido'] = ($paquete_AvancInstNodoEoc != false) ? $paquete_AvancInstNodoEoc : "";
						
						$llaveEocInstalar = array_search ( 29, array_column ( $value ['campos_personalizados'], 'id' ), true );
						$llaveEocInstaladas = array_search ( 35, array_column ( $value ['campos_personalizados'], 'id' ), true );
						$llaveFechaFuncionamiento = array_search ( 48, array_column ( $value ['campos_personalizados'], 'id' ), true );
						
						$this->informacion [$key] ['a_u'] = (($llaveEocInstalar != false && $value ['campos_personalizados'] [$llaveEocInstalar] ['value'] != '')) ? $value ['campos_personalizados'] [$llaveEocInstalar] ['value'] : "";
						$this->informacion [$key] ['a_v'] = (($llaveEocInstaladas != false && $value ['campos_personalizados'] [$llaveEocInstaladas] ['value'] != '')) ? $value ['campos_personalizados'] [$llaveEocInstaladas] ['value'] : "";
						$this->informacion [$key] ['a_y'] = (($llaveFechaFuncionamiento != false && $value ['campos_personalizados'] [$llaveEocInstaladas] ['value'] != '' && $value ['campos_personalizados'] [$llaveFechaFuncionamiento] ['value'] != '')) ? $value ['campos_personalizados'] [$llaveFechaFuncionamiento] ['value'] : "";
						$this->informacion [$key] ['a_z'] = $paquete_AvancInstNodoEoc ['done_ratio'];
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AU' . $i, (($llaveEocInstalar != false && $value['campos_personalizados'][$llaveEocInstalar]['value'] != '')) ? $value['campos_personalizados'][$llaveEocInstalar]['value'] : "")
						// ->getStyle('AU' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AV' . $i, (($llaveEocInstaladas != false && $value['campos_personalizados'][$llaveEocInstaladas]['value'] != '')) ? $value['campos_personalizados'][$llaveEocInstaladas]['value'] : "")
						// ->getStyle('AV' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AY' . $i, (($llaveFechaFuncionamiento != false && $value['campos_personalizados'][$llaveEocInstaladas]['value'] != '' && $value['campos_personalizados'][$llaveFechaFuncionamiento]['value'] != '')) ? $value['campos_personalizados'][$llaveFechaFuncionamiento]['value'] : "")
						// ->getStyle('AY' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('AZ' . $i, $paquete_AvancInstNodoEoc['done_ratio'] . "%")
						// ->getStyle('AZ' . $i)->applyFromArray($styleCentrado);
					}
				}
				
				{
					// Avance y Estado Instalación Nodo Inalámbrico
					
					{
						// Infraestructura Nodo
						
						//Aqui
						//$contenido_InsNoInala = $this->compactarAvances ( $value, "Infraestructura nodo (Avance y estado instalación nodo inalámbrico)", "description" );
						//$paquete_InsNoInala = $this->consultarPaqueteTrabajo ( $value, "Infraestructura nodo (Avance y estado instalación nodo inalámbrico)", "description" );
						
						//$this->informacion [$key] ['b_a'] = ($contenido_InsNoInala != false) ? $contenido_InsNoInala : "";
						//$this->informacion [$key] ['b_b'] = (! is_null ( $paquete_InsNoInala ['cf_14'] )) ? $paquete_InsNoInala ['cf_14'] : "";
						//$this->informacion [$key] ['b_c'] = (isset ( $paquete_InsNoInala ['due_date'] ) && $paquete_InsNoInala ['due_date'] != '') ? $paquete_InsNoInala ['due_date'] : "";
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('BA' . $i, (($contenido_InsNoInala != false) ? $contenido_InsNoInala : ""))
						// ->getStyle("BA" . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('BB' . $i, ((!is_null($paquete_InsNoInala['cf_14'])) ? $paquete_InsNoInala['cf_14'] : ""))
						// ->getStyle('BB' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('BC' . $i, ((isset($paquete_InsNoInala['due_date']) && $paquete_InsNoInala['due_date'] != '') ? $paquete_InsNoInala['due_date'] : ""))
						// ->getStyle('BC' . $i)->applyFromArray($styleCentradoVertical);
					}
					
					{
						// Instalación y Puesta en Funcionamiento Equipos
						
						// $contenido_InsPusFunEquInala = $this->compactarAvances($value, "Instalación y puesta en funcionamiento equipos (Avance y estado instalación nodo inalámbrico)", "description");
						// var_dump($contenido_InsNoInala);
						
						//Aqui
						//$paquete_InsPusFunEquInala = $this->consultarPaqueteTrabajo ( $value, "Instalación y puesta en funcionamiento equipos (Avance y estado instalación nodo inalámbrico)", "description" );
						
						//$this->informacion [$key] ['b_f'] = (isset ( $paquete_InsPusFunEquInala ['cf_16'] ) && ! is_null ( $paquete_InsPusFunEquInala ['cf_16'] )) ? $paquete_InsPusFunEquInala ['cf_16'] : "";
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('BF' . $i, ((isset($paquete_InsPusFunEquInala['cf_16']) && !is_null($paquete_InsPusFunEquInala['cf_16'])) ? $paquete_InsPusFunEquInala['cf_16'] : ""))
						// ->getStyle('BF' . $i)->applyFromArray($styleCentradoVertical);
					}
					
					{
						
						unset ( $llaveFechaFuncionamiento );
						$paquete_AvancInstNodoInal = $this->consultarPaqueteTrabajo ( $value, "Porcentaje Avance", "Avance y Estado Instalación Nodo Inalámbrico" );
						// var_dump($paquete_AvancInstNodoInal);exit;
						$llaveCeldasInstalar = array_search ( 30, array_column ( $value ['campos_personalizados'], 'id' ), true );
						$llaveCeldasInstaladas = array_search ( 34, array_column ( $value ['campos_personalizados'], 'id' ), true );
						$llaveFechaFuncionamiento = array_search ( 48, array_column ( $value ['campos_personalizados'], 'id' ), true );
						/*
						 * var_dump($llaveCeldasInstalar);
						 * var_dump($llaveCeldasInstaladas);
						 * var_dump($llaveFechaFuncionamiento);
						 * var_dump($value);exit;
						 */
						
						// $this->informacion[$key]['b_d'] = ($paquete_AvancInstNodoInal != false) ? $paquete_AvancInstNodoInal : "";
						
						$this->informacion [$key] ['b_d'] = (($llaveCeldasInstalar != false && $value ['campos_personalizados'] [$llaveCeldasInstalar] ['value'] != '')) ? $value ['campos_personalizados'] [$llaveCeldasInstalar] ['value'] : "";
						$this->informacion [$key] ['b_e'] = (($llaveCeldasInstaladas != false && $value ['campos_personalizados'] [$llaveCeldasInstaladas] ['value'] != '')) ? $value ['campos_personalizados'] [$llaveCeldasInstaladas] ['value'] : "";
						$this->informacion [$key] ['b_g'] = (($value ['campos_personalizados'] [$llaveCeldasInstaladas] ['value'] != '' && $llaveFechaFuncionamiento != false && $value ['campos_personalizados'] [$llaveFechaFuncionamiento] ['value'] != '')) ? $value ['campos_personalizados'] [$llaveFechaFuncionamiento] ['value'] : "";
						$this->informacion [$key] ['b_h'] = $paquete_AvancInstNodoInal ['done_ratio'];
						
						// var_dump($this->informacion[$key]);die;
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('BD' . $i, (($llaveCeldasInstalar != false && $value['campos_personalizados'][$llaveCeldasInstalar]['value'] != '')) ? $value['campos_personalizados'][$llaveCeldasInstalar]['value'] : "")
						// ->getStyle('BD' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('BE' . $i, (($llaveCeldasInstaladas != false && $value['campos_personalizados'][$llaveCeldasInstaladas]['value'] != '')) ? $value['campos_personalizados'][$llaveCeldasInstaladas]['value'] : "")
						// ->getStyle('BE' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('BG' . $i, (($value['campos_personalizados'][$llaveCeldasInstaladas]['value'] != '' && $llaveFechaFuncionamiento != false && $value['campos_personalizados'][$llaveFechaFuncionamiento]['value'] != '')) ? $value['campos_personalizados'][$llaveFechaFuncionamiento]['value'] : "")
						// ->getStyle('BG' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('BH' . $i, $paquete_AvancInstNodoInal['done_ratio'] . "%")
						// ->getStyle('BH' . $i)->applyFromArray($styleCentrado);
					}
				}
				
				{
					
					$llaveFechaPrevistaInterventoria = array_search ( 49, array_column ( $value ['campos_personalizados'], 'id' ), true );
					$llaveHFCInstalar = array_search ( 31, array_column ( $value ['campos_personalizados'], 'id' ), true );
					
					$this->informacion [$key] ['b_i'] = (($llaveFechaPrevistaInterventoria != false && $value ['campos_personalizados'] [$llaveFechaPrevistaInterventoria] ['value'] != '')) ? $value ['campos_personalizados'] [$llaveFechaPrevistaInterventoria] ['value'] : "";
					$this->informacion [$key] ['b_j'] = (($llaveHFCInstalar != false && $value ['campos_personalizados'] [$llaveHFCInstalar] ['value'] != '')) ? $value ['campos_personalizados'] [$llaveHFCInstalar] ['value'] : "";
					
					// $this->objCal->setActiveSheetIndex(0)
					// ->setCellValue('BI' . $i, (($llaveFechaPrevistaInterventoria != false && $value['campos_personalizados'][$llaveFechaPrevistaInterventoria]['value'] != '')) ? $value['campos_personalizados'][$llaveFechaPrevistaInterventoria]['value'] : "")
					// ->getStyle('BI' . $i)->applyFromArray($styleCentradoVertical);
					
					// $llaveHFCInstalar = array_search(31, array_column($value['campos_personalizados'], 'id'), true);
					
					// $this->objCal->setActiveSheetIndex(0)
					// ->setCellValue('BJ' . $i, (($llaveHFCInstalar != false && $value['campos_personalizados'][$llaveHFCInstalar]['value'] != '')) ? $value['campos_personalizados'][$llaveHFCInstalar]['value'] : "")
					// ->getStyle('BJ' . $i)->applyFromArray($styleCentradoVertical);
				}
				
				{
					
					// Avance y Estado Instalación Accesos HFC
					
					{
						$paquete_EstaInsHFC = $this->consultarPaqueteTrabajo ( $value, "Fecha Inicio instalación Acc HFC", "Avance y estado instalación accesos HFC" );
						
						$this->informacion [$key] ['b_k'] = (isset ( $paquete_EstaInsHFC ['start_date'] ) && $paquete_EstaInsHFC ['start_date'] != '') ? $paquete_EstaInsHFC ['start_date'] : "";
						$this->informacion [$key] ['b_l'] = (isset ( $paquete_EstaInsHFC ['due_date'] ) && $paquete_EstaInsHFC ['due_date'] != '') ? $paquete_EstaInsHFC ['due_date'] : "";
						
						$llaveHFCInstalados = array_search ( 36, array_column ( $value ['campos_personalizados'], 'id' ), true );
						$llaveAccVIP = array_search ( 37, array_column ( $value ['campos_personalizados'], 'id' ), true );
						
						$this->informacion [$key] ['b_p'] = (($llaveHFCInstalados != false && $value ['campos_personalizados'] [$llaveHFCInstalados] ['value'] != '')) ? $value ['campos_personalizados'] [$llaveHFCInstalados] ['value'] : "";
						$this->informacion [$key] ['b_q'] = (($llaveAccVIP != false && $value ['campos_personalizados'] [$llaveAccVIP] ['value'] != '')) ? $value ['campos_personalizados'] [$llaveAccVIP] ['value'] : "";
						$this->informacion [$key] ['b_r'] = (($llaveAccVIP != false && $value ['campos_personalizados'] [$llaveAccVIP] ['value'] != '')) ? $value ['campos_personalizados'] [$llaveAccVIP] ['value'] : "";
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('BK' . $i, ((isset($paquete_EstaInsHFC['start_date']) && $paquete_EstaInsHFC['start_date'] != '') ? $paquete_EstaInsHFC['start_date'] : ""))
						// ->getStyle('BK' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('BL' . $i, ((isset($paquete_EstaInsHFC['due_date']) && $paquete_EstaInsHFC['due_date'] != '') ? $paquete_EstaInsHFC['due_date'] : ""))
						// ->getStyle('BL' . $i)->applyFromArray($styleCentradoVertical);
						
						// $llaveHFCInstalados = array_search(36, array_column($value['campos_personalizados'], 'id'), true);
						// $llaveAccVIP = array_search(37, array_column($value['campos_personalizados'], 'id'), true);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('BP' . $i, (($llaveHFCInstalados != false && $value['campos_personalizados'][$llaveHFCInstalados]['value'] != '')) ? $value['campos_personalizados'][$llaveHFCInstalados]['value'] : "")
						// ->getStyle('BP' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('BQ' . $i, (($llaveAccVIP != false && $value['campos_personalizados'][$llaveAccVIP]['value'] != '')) ? $value['campos_personalizados'][$llaveAccVIP]['value'] : "")
						// ->getStyle('BQ' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('BR' . $i, (($llaveAccVIP != false && $value['campos_personalizados'][$llaveAccVIP]['value'] != '')) ? $value['campos_personalizados'][$llaveAccVIP]['value'] : "")
						// ->getStyle('BR' . $i)->applyFromArray($styleCentradoVertical);
					}
					{
						// Tendido y Puesta en Funcionameinto Red Coaxial
						
						$contenido_TenPusRedCox = $this->compactarAvances ( $value, "Tendido y Puesta en Funcionameinto Red Coaxial" );
						
						$paquete_TenPusRedCox = $this->consultarPaqueteTrabajo ( $value, "Tendido y Puesta en Funcionameinto Red Coaxial" );
						
						$this->informacion [$key] ['b_m'] = ($contenido_TenPusRedCox != false) ? $contenido_TenPusRedCox : "";
						$this->informacion [$key] ['b_n'] = (isset ( $paquete_TenPusRedCox ['cf_14'] ) && ! is_null ( $paquete_TenPusRedCox ['cf_14'] )) ? $paquete_TenPusRedCox ['cf_14'] : "";
						$this->informacion [$key] ['b_o'] = (isset ( $paquete_TenPusRedCox ['cf_16'] ) && ! is_null ( $paquete_TenPusRedCox ['cf_16'] )) ? $paquete_TenPusRedCox ['cf_16'] : "";
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('BM' . $i, (($contenido_TenPusRedCox != false) ? $contenido_TenPusRedCox : ""))
						// ->getStyle("BM" . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('BN' . $i, ((isset($paquete_TenPusRedCox['cf_14']) && !is_null($paquete_TenPusRedCox['cf_14'])) ? $paquete_TenPusRedCox['cf_14'] : ""))
						// ->getStyle('BN' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('BO' . $i, ((isset($paquete_TenPusRedCox['cf_16']) && !is_null($paquete_TenPusRedCox['cf_16'])) ? $paquete_TenPusRedCox['cf_16'] : ""))
						// ->getStyle('BO' . $i)->applyFromArray($styleCentradoVertical);
					}
				}
				
				{
					
					{
						
						$paquete_EstaAvanAccInhabala = $this->consultarPaqueteTrabajo ( $value, "Fecha Inicio instalación Acc Inalámbricos", "Avance y Estado Instalación Accesos Inalámbricos" );
						
						$this->informacion [$key] ['b_t'] = (isset ( $paquete_EstaAvanAccInhabala ['start_date'] ) && $paquete_EstaAvanAccInhabala ['start_date'] != '') ? $paquete_EstaAvanAccInhabala ['start_date'] : "";
						$this->informacion [$key] ['b_u'] = (isset ( $paquete_EstaAvanAccInhabala ['due_date'] ) && $paquete_EstaAvanAccInhabala ['due_date'] != '') ? $paquete_EstaAvanAccInhabala ['due_date'] : "";
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('BT' . $i, ((isset($paquete_EstaAvanAccInhabala['start_date']) && $paquete_EstaAvanAccInhabala['start_date'] != '') ? $paquete_EstaAvanAccInhabala['start_date'] : ""))
						// ->getStyle('BT' . $i)->applyFromArray($styleCentradoVertical);
						
						// $this->objCal->setActiveSheetIndex(0)
						// ->setCellValue('BU' . $i, ((isset($paquete_EstaAvanAccInhabala['due_date']) && $paquete_EstaAvanAccInhabala['due_date'] != '') ? $paquete_EstaAvanAccInhabala['due_date'] : ""))
						// ->getStyle('BU' . $i)->applyFromArray($styleCentradoVertical);
					}
					
					$llaveAccInalam = array_search ( 32, array_column ( $value ['campos_personalizados'], 'id' ), true );
					$llaveSMCPE = array_search ( 40, array_column ( $value ['campos_personalizados'], 'id' ), true );
					
					$this->informacion [$key] ['b_s'] = (($llaveAccInalam != false && $value ['campos_personalizados'] [$llaveAccInalam] ['value'] != '')) ? $value ['campos_personalizados'] [$llaveAccInalam] ['value'] : "";
					$this->informacion [$key] ['b_v'] = (($llaveSMCPE != false && $value ['campos_personalizados'] [$llaveSMCPE] ['value'] != '')) ? $value ['campos_personalizados'] [$llaveSMCPE] ['value'] : "";
					
					// $this->objCal->setActiveSheetIndex(0)
					// ->setCellValue('BS' . $i, (($llaveAccInalam != false && $value['campos_personalizados'][$llaveAccInalam]['value'] != '')) ? $value['campos_personalizados'][$llaveAccInalam]['value'] : "")
					// ->getStyle('BS' . $i)->applyFromArray($styleCentradoVertical);
					
					// $llaveSMCPE = array_search(40, array_column($value['campos_personalizados'], 'id'), true);
					
					// $this->objCal->setActiveSheetIndex(0)
					// ->setCellValue('BV' . $i, (($llaveSMCPE != false && $value['campos_personalizados'][$llaveSMCPE]['value'] != '')) ? $value['campos_personalizados'][$llaveSMCPE]['value'] : "")
					// ->getStyle('BV' . $i)->applyFromArray($styleCentradoVertical);
					
					$llaveE1E2 = array_search ( 41, array_column ( $value ['campos_personalizados'], 'id' ), true );
					
					$this->informacion [$key] ['b_w'] = (($llaveE1E2 != false && $value ['campos_personalizados'] [$llaveE1E2] ['value'] != '')) ? $value ['campos_personalizados'] [$llaveE1E2] ['value'] : "";
					$this->informacion [$key] ['b_x'] = (($llaveE1E2 != false && $value ['campos_personalizados'] [$llaveE1E2] ['value'] != '')) ? $value ['campos_personalizados'] [$llaveE1E2] ['value'] : "";
					
					// $this->objCal->setActiveSheetIndex(0)
					// ->setCellValue('BW' . $i, (($llaveE1E2 != false && $value['campos_personalizados'][$llaveE1E2]['value'] != '')) ? $value['campos_personalizados'][$llaveE1E2]['value'] : "")
					// ->getStyle('BW' . $i)->applyFromArray($styleCentradoVertical);
					
					// $this->objCal->setActiveSheetIndex(0)
					// ->setCellValue('BX' . $i, (($llaveE1E2 != false && $value['campos_personalizados'][$llaveE1E2]['value'] != '')) ? $value['campos_personalizados'][$llaveE1E2]['value'] : "")
					// ->getStyle('BX' . $i)->applyFromArray($styleCentradoVertical);
					
					$llaveRInternve = array_search ( 42, array_column ( $value ['campos_personalizados'], 'id' ), true );
					
					$this->informacion [$key] ['b_y'] = (($llaveRInternve != false && $value ['campos_personalizados'] [$llaveRInternve] ['value'] != '')) ? $value ['campos_personalizados'] [$llaveRInternve] ['value'] : "";
					
					// $this->objCal->setActiveSheetIndex(0)
					// ->setCellValue('BY' . $i, (($llaveRInternve != false && $value['campos_personalizados'][$llaveRInternve]['value'] != '')) ? $value['campos_personalizados'][$llaveRInternve]['value'] : "")
					// ->getStyle('BY' . $i)->applyFromArray($styleCentradoVertical);
				}
				
				$i ++;
			}
		}
		
		var_dump($this->informacion);die;
	}
	
	public function registrarAlmacenDatos() {
		$conexion = "almacendatos";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		ksort ( $this->informacion );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarProyectosAlmacen', $this->informacion );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "actualizar" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'registrarProyectosAlmacen', $this->informacion );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar" );
	}
	
	public function consultarPaqueteTrabajo($proyecto = '', $nombre_paquete = '', $tipo = '') {
		
		$contenido = '';
		
		foreach ( $proyecto ['campos_parametrizados'] as $key => $value ) {
			
			if($tipo != ""){
					if ($value['nombre_formulario'] == $nombre_paquete && $value['tipo'] == $tipo ) {
						
						if(isset($value['paquetesTrabajo'])){
							$contenido = $value['paquetesTrabajo'];
						}
						
					} 
			}else{
				
				if ($value ['sub_tipo'] == $nombre_paquete) {
				
					if(isset($value['paquetesTrabajo'])){
						$contenido = $value['paquetesTrabajo'];
					}
				}
			}
		}
		
		if ($contenido == '') {
			
			$contenido = false;
		}
		
		return $contenido;
	}
	
	public function compactarAvances($proyecto = '', $tema = '', $tipo = '') {
		
		$contenido = '';
		foreach ( $proyecto ['campos_parametrizados'] as $key => $value ) {
			
			if ($tipo != '' && $value [$tipo] == $tema) {
				
				foreach ( $value ['actividades'] as $llave => $valor ) {
					
					$fecha_actividad = substr ( $valor ['createdAt'], 0, 10 );
					
					$contenido .= "(" . $fecha_actividad . ") " . $valor ['comment'] ['raw'] . "\n";
				}
			} elseif ($value ['sub_tipo'] == $tema) {

				if(isset($value ['paquetesTrabajo'])){
					
					foreach ( $value ['paquetesTrabajo']['actividades'] as $llave => $valor ) {
							
						$fecha_actividad = substr ( $valor ['createdAt'], 0, 10 );
							
						$contenido .= "(" . $fecha_actividad . ") " . $valor ['comment'] ['raw'] . "\n";
					}
					
				}
				
			}
		}
		
		if ($contenido == '') {
			
			$contenido = false;
		} else {
			$piezas = explode ( "\n", $contenido );
			
			$piezas = array_unique ( $piezas );
			
			$contenido = implode ( "\n", $piezas );
		}
		
		return $contenido;
	}
	
}
$miProcesador = new GenerarReporteExcelInstalaciones ( $this->miSql, $this->proyectos );

?>

