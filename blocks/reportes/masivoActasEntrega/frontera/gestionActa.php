<?php

// namespace reportes\masivoActas\frontera;
use reportes\masivoActas\entidad\GenerarDocumento;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}
class GestionarContrato {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public $miSql;
	public $ruta;
	public $rutaURL;
	public function __construct($lenguaje, $formulario, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		
		$this->lenguaje = $lenguaje;
		
		$this->miFormulario = $formulario;
		
		$this->miSql = $sql;
		
		$esteBloque = $this->miConfigurador->configuracion ['esteBloque'];
		
		$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		$this->rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
		
		if (! isset ( $esteBloque ["grupo"] ) || $esteBloque ["grupo"] == "") {
			$ruta .= "/blocks/" . $esteBloque ["nombre"] . "/";
			$this->rutaURL .= "/blocks/" . $esteBloque ["nombre"] . "/";
		} else {
			$this->ruta .= "/blocks/" . $esteBloque ["grupo"] . "/" . $esteBloque ["nombre"] . "/";
			$this->rutaURL .= "/blocks/" . $esteBloque ["grupo"] . "/" . $esteBloque ["nombre"] . "/";
		}
	}
	public function formulario() {
		include_once $this->ruta . "entidad/guardarDocumentoCertificacion.php";
		
		$beneficiarios = explode ( ", ", $_REQUEST ['beneficiario'] );
		
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		$miPaginaActual = $this->miConfigurador->getVariableConfiguracion ( "pagina" );
		
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$contratos = explode ( ", ", $_REQUEST ['beneficiario'] );
		
		$prefijo = substr ( md5 ( uniqid ( time () ) ), 0, 6 );
		
		foreach ( $contratos as $generarActa ) {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarInformacionActa', $generarActa );
			$infoCertificado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
			if ($infoCertificado) {
				
				$_REQUEST = $infoCertificado;
				
				$_REQUEST ['fecha_instalacion'] = date ( "Y" ) . "-" . date ( "m" ) . "-" . date ( "d" );
				$miDocumento = new GenerarDocumento ();
				$miDocumento->crearActa ( $this->miSql, $this->rutaURL, $generarActa, $prefijo );
				
				unset ( $miDocumento );
				unset ( $_REQUEST );
			}
		}
		
		$this->rutaCarpeta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		$this->rutaCarpeta .= '/archivos/actas_entrega_portatil_servicios/';
		$this->urlCarpeta = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
		$this->urlCarpeta .= '/archivos/actas_entrega_portatil_servicios/';
		
		$nombre = $prefijo . "_actas_servicio.zip";
		$enlace = $this->rutaCarpeta . $prefijo . "/";
		$url = $this->urlCarpeta . $prefijo . "/";
		
		$this->comprimir ( $enlace, $enlace . $nombre );
		
		header ( "Content-type: application/octet-stream" );
		header ( "Content-Type: application/force-download" );
		header ( "Content-Disposition: attachment; filename=\"$nombre\"\n" );
		readfile ( $enlace . $nombre );
		
		$datos = array (
				'ruta' => $url . $nombre,
				'tipo_masivo' => 'Acta Entrega Servicios',
				'nombre_archivo' => $prefijo 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'registrarMasivo', $datos );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		// URL base
		$url = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$url .= $this->miConfigurador->getVariableConfiguracion ( "site" );
		$url .= "/index.php?";
		
		// Variables para Con
		$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
		
		// Codificar las variables
		$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar, $enlace );
		
		// URL Consultar Proyectos
		$recargarPagina = $url . $cadena;
		
		$pagina1 = "http://localhost//OpenKyOS/index.php?data=L00lIJQOho5BVKGwUMRYmuzzH9-vnw4keACt_Lm6GEE";
		$pagina2 = "pagina.php";
		
		header ( "location:$recargarPagina" );
		
		// ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
		// Se debe declarar el mismo atributo de marco con que se inició el formulario.
		$atributos ['marco'] = true;
		$atributos ['tipoEtiqueta'] = 'fin';
		echo $this->miFormulario->formulario ( $atributos );
	}
	public function comprimir($ruta, $zip_salida, $handle = false, $recursivo = false) {
		
		/* Declara el handle del objeto */
		if (! $handle) {
			$handle = new ZipArchive ();
			if ($handle->open ( $zip_salida, ZipArchive::CREATE ) === false) {
				return false; /* Imposible crear el archivo ZIP */
			}
		}
		
		var_dump ( $ruta );
		/* Procesa directorio */
		if (is_dir ( $ruta )) {
			/* Aseguramos que sea un directorio sin carácteres corruptos */
			$handle->addEmptyDir ( $ruta ); /* Agrega el directorio comprimido */
			foreach ( glob ( $ruta . '/*' ) as $url ) { /* Procesa cada directorio o archivo dentro de el */
				$this->comprimir ( $url, $zip_salida, $handle, true ); /* Comprime el subdirectorio o archivo */
			}
			
			/* Procesa archivo */
		} else {
			$handle->addFile ( $ruta );
		}
		
		/* Finaliza el ZIP si no se está ejecutando una acción recursiva en progreso */
		if (! $recursivo) {
			$handle->close ();
		}
		
		return true; /* Retorno satisfactorio */
	}
	public function mensajeModal() {
		switch ($_REQUEST ['mensaje']) {
			
			case 'insertoInformacionContrato' :
				$mensaje = "Exito en el registro información del Acta de Entrega";
				$atributos ['estiloLinea'] = 'success'; // success,error,information,warning
				break;
			case 'errorGenerarArchivo' :
				$mensaje = "Error en el registro de información del Acta de Entrega";
				$atributos ['estiloLinea'] = 'error'; // success,error,information,warning
				
				break;
		}
	}
}

$miSeleccionador = new GestionarContrato ( $this->lenguaje, $this->miFormulario, $this->sql );

$miSeleccionador->formulario ();

?>
