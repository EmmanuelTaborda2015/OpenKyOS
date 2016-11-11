<?php

namespace reportes\actaEntregaPortatil;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

include_once "core/manager/Configurador.class.php";
include_once "core/connection/Sql.class.php";
include_once "core/auth/SesionSso.class.php";

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
	public $miConfigurador;
	public $miSesionSso;
	public function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miSesionSso = \SesionSso::singleton ();
	}
	public function getCadenaSql($tipo, $variable = '') {
		$info_usuario = $this->miSesionSso->getParametrosSesionAbierta ();
		
		foreach ( $info_usuario ['description'] as $key => $rol ) {
			
			$info_usuario ['rol'] [] = $rol;
		}
		
		/**
		 * 1.
		 * Revisar las variables para evitar SQL Injection
		 */
		$prefijo = $this->miConfigurador->getVariableConfiguracion ( "prefijo" );
		$idSesion = $this->miConfigurador->getVariableConfiguracion ( "id_sesion" );
		
		switch ($tipo) {
			
			/**
			 * Clausulas específicas
			 */
			
			case 'consultaInformacionBeneficiario' :
				$cadenaSql = " SELECT bn.*,pr.descripcion as descripcion_tipo , cn.id id_contrato, cn.numero_contrato  ";
				$cadenaSql .= " FROM interoperacion.beneficiario_potencial bn ";
				$cadenaSql .= " JOIN parametros.parametros pr ON pr.codigo= bn.tipo_beneficiario::text ";
				$cadenaSql .= "JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro= pr.rel_parametro AND rl.descripcion='Tipo de Beneficario o Cliente' ";
				$cadenaSql .= " LEFT JOIN interoperacion.contrato cn ON cn.id_beneficiario= bn.id_beneficiario AND cn.estado_registro=TRUE ";
				$cadenaSql .= " WHERE bn.estado_registro = TRUE ";
				$cadenaSql .= " AND pr.estado_registro = TRUE ";
				$cadenaSql .= " AND bn.id_beneficiario= '" . $_REQUEST ['id'] . "';";
				break;
			
			case 'consultarBeneficiariosPotenciales' :
				$cadenaSql = " SELECT DISTINCT identificacion ||' - ('||nombre||' '||primer_apellido||' '||segundo_apellido||')' AS  value, id_beneficiario  AS data  ";
				$cadenaSql .= " FROM  interoperacion.beneficiario_potencial ";
				$cadenaSql .= "WHERE estado_registro=TRUE ";
				$cadenaSql .= "AND  cast(identificacion  as text) ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "OR nombre ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "OR primer_apellido ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "OR segundo_apellido ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "LIMIT 10; ";
				
				break;
			
			case 'registrarActaEntrega' :
				$cadenaSql = " UPDATE interoperacion.acta_entrega_servicios";
				$cadenaSql .= " SET estado_registro='FALSE'";
				$cadenaSql .= " WHERE id_beneficiario='" . $variable ['id_beneficiario'] . "';";
				$cadenaSql .= " INSERT INTO interoperacion.acta_entrega_servicios(";
				$cadenaSql .= " id_beneficiario,";
				$cadenaSql .= " nombre,";
				$cadenaSql .= " primer_apellido,";
				$cadenaSql .= " segundo_apellido,";
				$cadenaSql .= " tipo_documento,";
				$cadenaSql .= " identificacion, ";
				$cadenaSql .= " fecha_instalacion,";
				$cadenaSql .= " tipo_beneficiario,";
				$cadenaSql .= " estrato,";
				$cadenaSql .= " direccion,";
				$cadenaSql .= " urbanizacion,";
				$cadenaSql .= " id_urbanizacion,";
				$cadenaSql .= " departamento,";
				$cadenaSql .= " municipio,";
				$cadenaSql .= " codigo_dane,";
				$cadenaSql .= " contacto,";
				$cadenaSql .= " telefono,";
				$cadenaSql .= " tipo_tecnologia,";
				$cadenaSql .= " ciudad_expedicion_identificacion,";
				$cadenaSql .= " ciudad_firma,";
				$cadenaSql .= " ruta_firma)";
				$cadenaSql .= " VALUES ('" . $variable ['id_beneficiario'] . "',";
				$cadenaSql .= " '" . $variable ['nombres'] . "',";
				$cadenaSql .= " '" . $variable ['primer_apellido'] . "',";
				$cadenaSql .= " '" . $variable ['segundo_apellido'] . "',";
				$cadenaSql .= " '" . $variable ['tipo_documento'] . "', ";
				$cadenaSql .= " '" . $variable ['identificacion'] . "',";
				$cadenaSql .= " '" . $variable ['fecha_instalacion'] . "', ";
				$cadenaSql .= " '" . $variable ['tipo_beneficiario'] . "', ";
				$cadenaSql .= " '" . $variable ['estrato'] . "', ";
				$cadenaSql .= " '" . $variable ['direccion'] . "', ";
				$cadenaSql .= " '" . $variable ['id_urbanizacion'] . "', ";
				$cadenaSql .= " '" . $variable ['urbanizacion'] . "', ";
				$cadenaSql .= " '" . $variable ['departamento'] . "', ";
				$cadenaSql .= " '" . $variable ['municipio'] . "', ";
				$cadenaSql .= " '" . $variable ['codigo_dane'] . "', ";
				$cadenaSql .= " '" . $variable ['contacto'] . "', ";
				$cadenaSql .= " '" . $variable ['telefono'] . "', ";
				$cadenaSql .= " '" . $variable ['tipo_tecnologia'] . "', ";
				$cadenaSql .= " '" . $variable ['ciudad_expedicion_identificacion'] . "',";
				$cadenaSql .= " '" . $variable ['ciudad_firma'] . "',";
				$cadenaSql .= " '" . $variable ['ruta_firma'] . "');";
				break;
			
			case 'consultarParametro' :
				$cadenaSql = " SELECT pr.id_parametro, pr.descripcion, pr.codigo ";
				$cadenaSql .= " FROM parametros.parametros pr";
				$cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pr.rel_parametro";
				$cadenaSql .= " WHERE ";
				$cadenaSql .= " pr.estado_registro=TRUE ";
				$cadenaSql .= " AND rl.descripcion='Tipologia Archivo'";
				$cadenaSql .= " AND pr.codigo='" . $variable . "' ";
				$cadenaSql .= " AND rl.estado_registro=TRUE ";
				
				break;
			
			case 'registrarDocumentoCertificado' :
				$cadenaSql = " UPDATE interoperacion.acta_entrega_servicios";
				$cadenaSql .= " SET nombre_documento='" . $variable ['nombre_contrato'] . "', ruta_documento='" . $variable ['ruta_contrato'] . "' ";
				$cadenaSql .= " WHERE id_beneficiario='" . $_REQUEST ['id_beneficiario'] . "' AND estado_registro='TRUE';";
				break;
			
			case 'consultaInformacionCertificado' :
				$cadenaSql = " SELECT *";
				$cadenaSql .= " FROM interoperacion.acta_entrega_servicios";
				$cadenaSql .= " WHERE id_beneficiario ='" . $_REQUEST ['id_beneficiario'] . "'";
				$cadenaSql .= " AND estado_registro='TRUE';";
				break;
			
			case 'registrarRequisito' :
				$cadenaSql = " INSERT INTO interoperacion.documentos_contrato(";
				$cadenaSql .= " id_beneficiario, ";
				$cadenaSql .= " tipologia_documento,";
				$cadenaSql .= " nombre_documento, ";
				$cadenaSql .= " ruta_relativa, ";
				$cadenaSql .= " usuario)";
				$cadenaSql .= " VALUES ('" . $variable ['id_beneficiario'] . "',";
				$cadenaSql .= " '" . $variable ['tipologia'] . "',";
				$cadenaSql .= " '" . $variable ['nombre_documento'] . "',";
				$cadenaSql .= " '" . $variable ['ruta_relativa'] . "',";
				$cadenaSql .= " '" . $info_usuario ['uid'] [0] . "');";
				
				break;
			
			case "parametroTipoVivienda" :
				$cadenaSql = "SELECT        ";
				$cadenaSql .= " codigo, ";
				$cadenaSql .= "param.descripcion ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "parametros.parametros as param ";
				$cadenaSql .= "INNER JOIN ";
				$cadenaSql .= "parametros.relacion_parametro as rparam ";
				$cadenaSql .= "ON ";
				$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "rparam.descripcion = 'Tipo de Vivienda' ";
				break;
			
			case "parametroDepartamento" :
				$cadenaSql = "SELECT ";
				$cadenaSql .= "codigo_dep, ";
				$cadenaSql .= "departamento ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "parametros.departamento ";
				break;
			
			case "parametroMunicipio" :
				$cadenaSql = "SELECT ";
				$cadenaSql .= "codigo_mun, ";
				$cadenaSql .= "municipio ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "parametros.municipio ";
				break;
			
			case "parametroTipoBeneficiario" :
				$cadenaSql = "SELECT        ";
				$cadenaSql .= "codigo, ";
				$cadenaSql .= "param.descripcion ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "parametros.parametros as param ";
				$cadenaSql .= "INNER JOIN ";
				$cadenaSql .= "parametros.relacion_parametro as rparam ";
				$cadenaSql .= "ON ";
				$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "rparam.descripcion = 'Tipo de Beneficario o Cliente' ";
				break;
			
			case "parametroEstrato" :
				$cadenaSql = "SELECT        ";
				$cadenaSql .= " codigo, ";
				$cadenaSql .= "param.descripcion ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "parametros.parametros as param ";
				$cadenaSql .= "INNER JOIN ";
				$cadenaSql .= "parametros.relacion_parametro as rparam ";
				$cadenaSql .= "ON ";
				$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "rparam.descripcion = 'Estrato' ";
				break;
			
			case "parametroTipoTecnologia" :
				$cadenaSql = "SELECT        ";
				$cadenaSql .= "codigo, ";
				$cadenaSql .= "param.descripcion ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "parametros.parametros as param ";
				$cadenaSql .= "INNER JOIN ";
				$cadenaSql .= "parametros.relacion_parametro as rparam ";
				$cadenaSql .= "ON ";
				$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "rparam.descripcion = 'Tipo de Tecnología' ";
				break;
		}
		
		return $cadenaSql;
	}
}
?>

