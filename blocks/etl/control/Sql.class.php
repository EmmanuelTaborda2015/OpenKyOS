<?php
namespace reportes\instalacionesGenerales;
if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/connection/Sql.class.php");

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
	var $miConfigurador;
	function getCadenaSql($tipo, $variable = '') {
		
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
			case 'consultarBloques' :
				
				$cadenaSql = " SELECT id_bloque, nombre, descripcion, grupo ";
				$cadenaSql .= " FROM " . $prefijo . "bloque;";
				
				break;
			
			case 'insertarBloque' :
				$cadenaSql = 'INSERT INTO ';
				$cadenaSql .= $prefijo . 'bloque ';
				$cadenaSql .= '( ';
				$cadenaSql .= 'nombre,';
				$cadenaSql .= 'descripcion,';
				$cadenaSql .= 'grupo';
				$cadenaSql .= ') ';
				$cadenaSql .= 'VALUES ';
				$cadenaSql .= '( ';
				$cadenaSql .= '\'' . $_REQUEST ['nombre'] . '\', ';
				$cadenaSql .= '\'' . $_REQUEST ['descripcion'] . '\', ';
				$cadenaSql .= '\'' . $_REQUEST ['grupo'] . '\' ';
				$cadenaSql .= '); ';
				break;
				
			case 'registrarProyectosAlmacen' :
				
				$cadenaSql = "";
				$cont = 0;
				
				foreach ($variable as $valor) {
				
					if($cont == 0){
						
						$cadenaSql = "INSERT INTO public.reporte_semanal(";
						
						foreach ($valor as $key => $value){
							$cadenaSql .= "" . $key . ",";
						}
						
						$cadenaSql = substr($cadenaSql, 0, (strlen($cadenaSql) - 1));
						
						$cadenaSql .= ") VALUES ";
					}
					
					$cadenaSql .= "(";
					
					foreach ($valor as $key => $value){
						
						$cadenaSql .= "'" . $value . "',";
						
					}
					
					$cadenaSql = substr($cadenaSql, 0, (strlen($cadenaSql) - 1));
					
					$cadenaSql .= "),";
					
					$cont++;
				}
				
                $cadenaSql = substr($cadenaSql, 0, (strlen($cadenaSql) - 1));

                break;
                
                case 'actualizarProyectosAlmacen' :
                	
                	$cadenaSql = "UPDATE public.reporte_semanal ";
                	$cadenaSql .= "SET ";
                	$cadenaSql .= "estado_registro=FALSE ";
                	$cadenaSql .= "WHERE ";
                	$cadenaSql .= "fecha_registro::timestamp::date=(SELECT current_timestamp::timestamp::date)";
                	
                	break;
		}
		
		return $cadenaSql;
	}
}
?>

