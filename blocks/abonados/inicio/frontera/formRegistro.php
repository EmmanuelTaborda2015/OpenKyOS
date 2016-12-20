<?php

namespace cambioClave\formRegistro;

include_once ("core/auth/SesionSso.class.php");

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class Formulario {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miSql;
	var $miSesionSso;
	
	function __construct($lenguaje, $formulario, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		
		$this->lenguaje = $lenguaje;
		
		$this->miFormulario = $formulario;
		
		$this->miSql = $sql;
		
		$this->miSesionSso = \SesionSso::singleton ();
		
	}
	function formulario() {
		
		/**
		 * IMPORTANTE: Este formulario está utilizando jquery.
		 * Por tanto en el archivo script/ready.php y script/ready.js se declaran
		 * algunas funciones js que lo complementan.
		 */
		$conexion = "estructura";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		// Rescatar los datos de este bloque
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		// Rescatar los datos de este bloque
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		$miPaginaActual = $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		
		$directorio = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$directorio .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
		$directorio .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$rutaBloque .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/";
		$rutaBloque .= $esteBloque ['grupo'] . '/' . $esteBloque ['nombre'];
		
		$this->_rutaBloque = $rutaBloque;
		// ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
		/**
		 * Atributos que deben ser aplicados a todos los controles de este formulario.
		 * Se utiliza un arreglo independiente debido a que los atributos individuales se reinician cada vez que se
		 * declara un campo.
		 *
		 * Si se utiliza esta técnica es necesario realizar un mezcla entre este arreglo y el específico en cada control:
		 * $atributos= array_merge($atributos,$atributosGlobales);
		 */
		
		$atributosGlobales ['campoSeguro'] = 'true';
		
		if (! isset ( $_REQUEST ['tiempo'] )) {
			$_REQUEST ['tiempo'] = time ();
		}
		
		// -------------------------------------------------------------------------------------------------
		
		// ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
		$esteCampo = $esteBloque ['nombre'];
		$atributos ['id'] = $esteCampo;
		$atributos ['nombre'] = $esteCampo;
		
		// Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
		$atributos ['tipoFormulario'] = '';
		
		// Si no se coloca, entonces toma el valor predeterminado 'POST'
		$atributos ['metodo'] = 'POST';
		
		// Si no se coloca, entonces toma el valor predeterminado 'index.php' (Recomendado)
		$atributos ['action'] = 'index.php';
		$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo );
		
		// Si no se coloca, entonces toma el valor predeterminado.
		$atributos ['estilo'] = 'main';
		$atributos ['marco'] = true;
		$tab = 1;
		// ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
		
		// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
		$atributos ['tipoEtiqueta'] = 'inicio';
		echo $this->miFormulario->formularioBootstrap ( $atributos );
		unset ( $atributos );
		
		$info_usuario = $this->miSesionSso->getParametrosSesionAbierta ();
		
		$cadena_sql = $this->miSql->getCadenaSql ( "consultarColor" );
		$colores = $esteRecursoDB->ejecutarAcceso ( $cadena_sql, "busqueda" ) [0];
		
		if ($colores) {
			$esteCampo = 'color1';
			$atributos ["id"] = $esteCampo; // No cambiar este nombre
			$atributos ["tipo"] = "hidden";
			$atributos ['valor'] = $colores ['color1'];
			$atributos ['estilo'] = '';
			$atributos ["obligatorio"] = false;
			$atributos ['marco'] = true;
			$atributos ["etiqueta"] = "";
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTexto ( $atributos );
			unset ( $atributos );
			
			$esteCampo = 'color2';
			$atributos ["id"] = $esteCampo; // No cambiar este nombre
			$atributos ["tipo"] = "hidden";
			$atributos ['valor'] = $colores ['color2'];
			$atributos ['estilo'] = '';
			$atributos ["obligatorio"] = false;
			$atributos ['marco'] = true;
			$atributos ["etiqueta"] = "";
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTexto ( $atributos );
			unset ( $atributos );
			
			$esteCampo = 'color3';
			$atributos ["id"] = $esteCampo; // No cambiar este nombre
			$atributos ["tipo"] = "hidden";
			$atributos ['valor'] = $colores ['color3'];
			$atributos ['estilo'] = '';
			$atributos ["obligatorio"] = false;
			$atributos ['marco'] = true;
			$atributos ["etiqueta"] = "";
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTexto ( $atributos );
			unset ( $atributos );
		}

		echo ' <div class="main">
      			<div class="row">
        			<div class="col-xs-14 col-lg-11">
          				<div class="panel panel-primary">
            				<div class="panel-body">
								<div class="row">';
		
		
		
		echo '<div id="page-content">
<div class="content-header content-header-media">
<div class="header-section">
<div class="row">
<div class="col-md-4 col-lg-6 hidden-xs hidden-sm">
<h1>Hola,  <strong>' . /*$info_usuario['uid'][0]*/ 'Emmanuel Taborda' . '</strong><br><small>Que gusto que estes aquí!</small></h1>
</div>
<div class="col-md-8 col-lg-6">
<div class="row text-center">
</div>
</div>
</div>
</div>
<img src="' . $rutaBloque . '/frontera/css/imagen/paris-1283583.jpg" alt="header image" class="animation-pulseSlow">
</div>
<div class="row">
<div class="col-sm-6 col-lg-3">
<a href="#" class="widget widget-hover-effect1">
<div class="widget-simple">
<div class="widget-icon pull-left themed-background-autumn animation-fadeIn">
<i class="gi gi-user"></i>
</div>
<h3 class="widget-content text-right animation-pullDown">
New <strong>Article</strong><br>
<small>Mountain Trip</small>
</h3>
</div>
</a>
</div>
<div class="col-sm-6 col-lg-3">
<a href="page_comp_charts.php" class="widget widget-hover-effect1">
<div class="widget-simple">
<div class="widget-icon pull-left themed-background-spring animation-fadeIn">
<i class="gi gi-home"></i>
</div>
<h3 class="widget-content text-right animation-pullDown">
+ <strong>250%</strong><br>
<small>Sales Today</small>
</h3>
</div>
</a>
</div>
<div class="col-sm-6 col-lg-3">
<a href="page_ready_inbox.php" class="widget widget-hover-effect1">
<div class="widget-simple">
<div class="widget-icon pull-left themed-background-fire animation-fadeIn">
<i class="gi gi-envelope"></i>
</div>
<h3 class="widget-content text-right animation-pullDown">
5 <strong>Messages</strong>
<small>Support Center</small>
</h3>
</div>
</a>
</div>
<div class="col-sm-6 col-lg-3">
<a href="page_comp_gallery.php" class="widget widget-hover-effect1">
<div class="widget-simple">
<div class="widget-icon pull-left themed-background-amethyst animation-fadeIn">
<i class="gi gi-picture"></i>
</div>
<h3 class="widget-content text-right animation-pullDown">
+30 <strong>Photos</strong>
<small>Gallery</small>
</h3>
</div>
</a>
</div>
<div class="col-sm-6">
<a href="page_comp_charts.php" class="widget widget-hover-effect1">
<div class="widget-simple">
<div class="widget-icon pull-left themed-background animation-fadeIn">
<i class="gi gi-wallet"></i>
</div>
<div class="pull-right">
<span id="mini-chart-sales"></span>
</div>
<h3 class="widget-content animation-pullDown visible-lg">
Latest <strong>Sales</strong>
<small>Per hour</small>
</h3>
</div>
</a>
</div>
<div class="col-sm-6">
<a href="page_widgets_stats.php" class="widget widget-hover-effect1">
<div class="widget-simple">
<div class="widget-icon pull-left themed-background animation-fadeIn">
<i class="gi gi-crown"></i>
</div>
<div class="pull-right">
<span id="mini-chart-brand"></span>
</div>
<h3 class="widget-content animation-pullDown visible-lg">
Our <strong>Brand</strong>
<small>Popularity over time</small>
</h3>
</div>
</a>
</div>
</div>
<div class="row">
<div class="col-md-6">
<div class="widget">
<div class="widget-extra themed-background-dark">
<div class="widget-options">
<div class="btn-group btn-group-xs">
<a href="javascript:void(0)" class="btn btn-default" data-toggle="tooltip" title="Edit Widget"><i class="fa fa-pencil"></i></a>
<a href="javascript:void(0)" class="btn btn-default" data-toggle="tooltip" title="Quick Settings"><i class="fa fa-cog"></i></a>
</div>
</div>
<h3 class="widget-content-light">
Latest <strong>News</strong>
<small><a href="page_ready_timeline.php"><strong>View all</strong></a></small>
</h3>
</div>
<div class="widget-extra">
<div class="timeline">
<ul class="timeline-list">
<li class="active">
<div class="timeline-icon"><i class="gi gi-airplane"></i></div>
<div class="timeline-time"><small>just now</small></div>
<div class="timeline-content">
<p class="push-bit"><a href="page_ready_user_profile.php"><strong>Jordan Carter</strong></a></p>
<p class="push-bit">The trip was an amazing and a life changing experience!!</p>
<p class="push-bit"><a href="page_ready_article.php" class="btn btn-xs btn-primary"><i class="fa fa-file"></i> Read the article</a></p>
<div class="row push">
<div class="col-sm-6 col-md-4">
<a href="img/placeholders/photos/photo1.jpg" data-toggle="lightbox-image">
<img src="img/placeholders/photos/photo1.jpg" alt="image">
</a>
</div>
<div class="col-sm-6 col-md-4">
<a href="img/placeholders/photos/photo22.jpg" data-toggle="lightbox-image">
<img src="img/placeholders/photos/photo22.jpg" alt="image">
</a>
</div>
</div>
</div>
</li>
<li class="active">
<div class="timeline-icon themed-background-fire themed-border-fire"><i class="fa fa-file-text"></i></div>
<div class="timeline-time"><small>5 min ago</small></div>
<div class="timeline-content">
<p class="push-bit"><a href="page_ready_user_profile.php"><strong>Administrator</strong></a></p>
<strong>Free courses</strong> for all our customers at A1 Conference Room - 9:00 <strong>am</strong> tomorrow!
</div>
</li>
<li class="active">
<div class="timeline-icon"><i class="gi gi-drink"></i></div>
<div class="timeline-time"><small>3 hours ago</small></div>
<div class="timeline-content">
<p class="push-bit"><a href="page_ready_user_profile.php"><strong>Ella Winter</strong></a></p>
<p class="push-bit"><strong>Happy Hour!</strong> Free drinks at <a href="javascript:void(0)">Cafe-Bar</a> all day long!</p>
<div id="gmap-timeline" class="gmap"></div>
</div>
</li>
<li class="active">
<div class="timeline-icon"><i class="fa fa-cutlery"></i></div>
<div class="timeline-time"><small>yesterday</small></div>
<div class="timeline-content">
<p class="push-bit"><a href="page_ready_user_profile.php"><strong>Patricia Woods</strong></a></p>
<p class="push-bit">Today I had the lunch of my life! It was delicious!</p>
<div class="row push">
<div class="col-sm-6 col-md-4">
<a href="img/placeholders/photos/photo23.jpg" data-toggle="lightbox-image">
<img src="img/placeholders/photos/photo23.jpg" alt="image">
</a>
</div>
</div>
</div>
</li>
<li class="active">
<div class="timeline-icon themed-background-fire themed-border-fire"><i class="fa fa-smile-o"></i></div>
<div class="timeline-time"><small>2 days ago</small></div>
<div class="timeline-content">
<p class="push-bit"><a href="page_ready_user_profile.php"><strong>Administrator</strong></a></p>
To thank you all for your support we would like to let you know that you will receive free feature updates for life! You are awesome!
</div>
</li>
<li class="active">
<div class="timeline-icon"><i class="fa fa-pencil"></i></div>
<div class="timeline-time"><small>1 week ago</small></div>
<div class="timeline-content">
<p class="push-bit"><a href="page_ready_user_profile.php"><strong>Nicole Ward</strong></a></p>
<p class="push-bit">Consectetur adipiscing elit. Maecenas ultrices, justo vel imperdiet gravida, urna ligula hendrerit nibh, ac cursus nibh sapien in purus. Mauris tincidunt tincidunt turpis in porta. Integer fermentum tincidunt auctor. Vestibulum ullamcorper, odio sed rhoncus imperdiet, enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate.</p>
Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt sollicitudin sem nec ultrices. Sed at mi velit. Ut egestas tempor est, in cursus enim venenatis eget! Nulla quis ligula ipsum.
</div>
</li>
<li class="text-center">
<a href="javascript:void(0)" class="btn btn-xs btn-default">View more..</a>
</li>
</ul>
</div>
</div>
</div>
</div>
<div class="col-md-6">
<div class="widget">
<div class="widget-extra themed-background-dark">
<div class="widget-options">
<div class="btn-group btn-group-xs">
<a href="javascript:void(0)" class="btn btn-default" data-toggle="tooltip" title="Edit Widget"><i class="fa fa-pencil"></i></a>
<a href="javascript:void(0)" class="btn btn-default" data-toggle="tooltip" title="Quick Settings"><i class="fa fa-cog"></i></a>
</div>
</div>
<h3 class="widget-content-light">
Your <strong>VIP Plan</strong>
<small><a href="page_ready_pricing_tables.php"><strong>Upgrade</strong></a></small>
</h3>
</div>
<div class="widget-extra-full">
<div class="row text-center">
<div class="col-xs-6 col-lg-3">
<h3>
<strong>35</strong> <small>/50</small><br>
<small><i class="fa fa-folder-open-o"></i> Projects</small>
</h3>
</div>
<div class="col-xs-6 col-lg-3">
<h3>
<strong>25</strong> <small>/100GB</small><br>
<small><i class="fa fa-hdd-o"></i> Storage</small>
</h3>
</div>
<div class="col-xs-6 col-lg-3">
<h3>
<strong>65</strong> <small>/1k</small><br>
<small><i class="fa fa-building-o"></i> Clients</small>
</h3>
</div>
<div class="col-xs-6 col-lg-3">
<h3>
<strong>10</strong> <small>k</small><br>
<small><i class="fa fa-envelope-o"></i> Emails</small>
</h3>
</div>
</div>
</div>
</div>
<div class="widget">
<div class="widget-advanced widget-advanced-alt">
<div class="widget-header text-center themed-background">
<h3 class="widget-content-light text-left pull-left animation-pullDown">
<strong>Sales</strong> &amp; <strong>Earnings</strong><br>
<small>Last Year</small>
</h3>
<div id="dash-widget-chart" class="chart"></div>
</div>
<div class="widget-main">
<div class="row text-center">
<div class="col-xs-4">
<h3 class="animation-hatch"><strong>7.500</strong><br><small>Clients</small></h3>
</div>
<div class="col-xs-4">
<h3 class="animation-hatch"><strong>10.970</strong><br><small>Sales</small></h3>
</div>
<div class="col-xs-4">
<h3 class="animation-hatch">$<strong>31.230</strong><br><small>Earnings</small></h3>
</div>
</div>
</div>
</div>
</div>
<div class="widget">
<div class="widget-advanced widget-advanced-alt">
<div class="widget-header text-left">
<img src="img/placeholders/headers/widget5_header.jpg" alt="background" class="widget-background animation-pulseSlow">
<h3 class="widget-content widget-content-image widget-content-light clearfix">
<span class="widget-icon pull-right">
<i class="fa fa-sun-o animation-pulse"></i>
</span>
Weather <strong>Station</strong><br>
<small><i class="fa fa-location-arrow"></i> The Mountain</small>
</h3>
</div>
<div class="widget-main">
<div class="row text-center">
<div class="col-xs-6 col-lg-3">
<h3>
<strong>10&deg;</strong> <small>C</small><br>
<small>Sunny</small>
</h3>
</div>
<div class="col-xs-6 col-lg-3">
<h3>
<strong>80</strong> <small>%</small><br>
<small>Humidity</small>
</h3>
</div>
<div class="col-xs-6 col-lg-3">
<h3>
<strong>60</strong> <small>km/h</small><br>
<small>Wind</small>
</h3>
</div>
<div class="col-xs-6 col-lg-3">
<h3>
<strong>5</strong> <small>km</small><br>
<small>Visibility</small>
</h3>
</div>
</div>
</div>
</div>
</div>
<div class="widget">
<div class="widget-advanced">
<div class="widget-header text-center themed-background-dark">
<h3 class="widget-content-light clearfix">
Awesome <strong>Gallery</strong><br>
<small>4 Photos</small>
</h3>
</div>
<div class="widget-main">
<a href="page_comp_gallery.php" class="widget-image-container">
<span class="widget-icon themed-background"><i class="gi gi-picture"></i></span>
</a>
<div class="gallery gallery-widget" data-toggle="lightbox-gallery">
<div class="row">
<div class="col-xs-6 col-sm-3">
<a href="img/placeholders/photos/photo15.jpg" class="gallery-link" title="Image Info">
<img src="img/placeholders/photos/photo15.jpg" alt="image">
</a>
</div>
<div class="col-xs-6 col-sm-3">
<a href="img/placeholders/photos/photo5.jpg" class="gallery-link" title="Image Info">
<img src="img/placeholders/photos/photo5.jpg" alt="image">
</a>
</div>
<div class="col-xs-6 col-sm-3">
<a href="img/placeholders/photos/photo6.jpg" class="gallery-link" title="Image Info">
<img src="img/placeholders/photos/photo6.jpg" alt="image">
</a>
</div>
<div class="col-xs-6 col-sm-3">
<a href="img/placeholders/photos/photo13.jpg" class="gallery-link" title="Image Info">
<img src="img/placeholders/photos/photo13.jpg" alt="image">
</a>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<footer class="clearfix">
<div class="pull-right">
Crafted with <i class="fa fa-heart text-danger"></i> by <a href="http://goo.gl/vNS3I" target="_blank">pixelcave</a>
</div>
<div class="pull-left">
<span id="year-copy"></span> &copy; <a href="http://goo.gl/TDOSuC" target="_blank">ProUI 3.6</a>
</div>
</footer>
</div>
</div>
</div>
<a href="#" id="to-top"><i class="fa fa-angle-double-up"></i></a>
<div id="modal-user-settings" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header text-center">
<h2 class="modal-title"><i class="fa fa-pencil"></i> Settings</h2>
</div>
<div class="modal-body">
<form action="index.php" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
<fieldset>
<legend>Vital Info</legend>
<div class="form-group">
<label class="col-md-4 control-label">Username</label>
<div class="col-md-8">
<p class="form-control-static">Admin</p>
</div>
</div>
<div class="form-group">
<label class="col-md-4 control-label" for="user-settings-email">Email</label>
<div class="col-md-8">
<input type="email" id="user-settings-email" name="user-settings-email" class="form-control" value="admin@example.com">
</div>
</div>
<div class="form-group">
<label class="col-md-4 control-label" for="user-settings-notifications">Email Notifications</label>
<div class="col-md-8">
<label class="switch switch-primary">
<input type="checkbox" id="user-settings-notifications" name="user-settings-notifications" value="1" checked>
<span></span>
</label>
</div>
</div>
</fieldset>
<fieldset>
<legend>Password Update</legend>
<div class="form-group">
<label class="col-md-4 control-label" for="user-settings-password">New Password</label>
<div class="col-md-8">
<input type="password" id="user-settings-password" name="user-settings-password" class="form-control" placeholder="Please choose a complex one..">
</div>
</div>
<div class="form-group">
<label class="col-md-4 control-label" for="user-settings-repassword">Confirm New Password</label>
<div class="col-md-8">
<input type="password" id="user-settings-repassword" name="user-settings-repassword" class="form-control" placeholder="..and confirm it!">
</div>
</div>
</fieldset>
<div class="form-group form-actions">
<div class="col-xs-12 text-right">
<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
<button type="submit" class="btn btn-sm btn-primary">Save Changes</button>
</div>
</div>
</form>
</div>
</div>
</div>
</div>';
		
		$barraSuperior = '';
		$barraSuperior .= '<div class="well well-lg superior">
						   <div class="row">
						   <div class="col-md-4">Hola, ';
				
		$beneficiario = $info_usuario['uid'][0];
		$barraSuperior .= $beneficiario;
		
		$barraSuperior .= '</div>
						   <div align="center" id="bannerReloj" class="col-md-4"></div>
  						   <div align="right" class="col-md-4"><span id="bgcolor" class="glyphicon glyphicon-cog" aria-hidden="true"></span></div>
					       </div>
		                   </div>';
							
		
		echo $barraSuperior;
		
		
		$barraTipoBeneficiario= '<div class="col-lg-4 col-md-4 col-sm-12">
          				  <div class="small-box bg-blue">
						  <div class="inner">
              			  <h4>';
		
		$tipoBeneficiario = 'VIP';
		$barraTipoBeneficiario .= $tipoBeneficiario;
		
		$barraTipoBeneficiario .= '</h4><br>
						   <p>Tipo de Abonado</p>
            			   </div>
            			   <div class="icon">
              			   <i class="ion ion-person"></i>
            			   </div>
          				   </div>
       				       </div>';
		
		echo $barraTipoBeneficiario;
		
		$barraContrato = '<div class="col-lg-4 col-md-4 col-sm-12">
          				  <div class="small-box bg-blue">
						  <div class="inner">
              			  <h4>';
		
		$numeroContrato = '44';
		$barraContrato .= $numeroContrato;
		
		$barraContrato .= '</h4><br>
						   <p>Número de Contrato</p>
            			   </div>
            			   <div class="icon">
              			   <i class="ion ion-document-text"></i>
            			   </div>
          				   </div>
       				       </div>';
		
		echo $barraContrato;
		
		$barraEstadoServicio = '<div class="col-lg-4 col-md-4 col-sm-12">
          						<div class="small-box bg-blue">
            					<div class="inner">
              					<h4>';
		
		$pagoFactura = '23/12/2016';
		$barraEstadoServicio .= $pagoFactura;
		
		$barraEstadoServicio .= '</h4><br>
              					<p>Fecha Limite de Pago</p>
            					</div>
            					<div class="icon">
              					<i class="ion ion-ios-alarm"></i>
            					</div>
          						</div>
        						</div>';

		echo $barraEstadoServicio;
		
		$barraEstadoServicio = '<div class="col-lg-4 col-md-4 col-sm-12">
          						<div class="small-box bg-blue">
            					<div class="inner">
              					<h4>';
		
		$estadoServicio = 'Instalado';
		$barraEstadoServicio .= $estadoServicio;
		
		$barraEstadoServicio .= '</h4><br>
              					 <p>Estado del Servicio</p>
            					 </div>
            					 <div class="icon">
              					 <i class="ion ion-stats-bars"></i>
            					 </div>
          						 </div>
        						 </div>';
		
		
		echo '</div>
			  <div class="row">';
		
		
		$noticiasBeneficiario = array(array("imagen"=>'bg1.jpg', "noticia" => 'noticia1'),array("imagen"=>'bg2.jpg', "noticia" => 'noticia2'),array("imagen"=>'main-feature.png', "noticia" => 'noticia3'));
		
		$noticias = '<div class="col-lg-8 col-md-8 col-sm-12  text-center">
					 <div id="myCarousel" class="carousel slide" data-ride="carousel">
					 <ol class="carousel-indicators">';
		
		$contador = 0;
		
		foreach ($noticiasBeneficiario as $not){
			
			$noticias .= '<li data-target="#myCarousel" data-slide-to="';
			$noticias .= $contador;
			
			if ($not === reset($noticiasBeneficiario)) {
				$noticias .= '" class="active"></li>';
			}else{
				$noticias .= '"></li>';
			}
				
			$contador++;
		}
		
		$noticias .= '</ol>
					  <div class="carousel-inner" role="listbox">';
		
		foreach ($noticiasBeneficiario as $not){
			
			if ($not === reset($noticiasBeneficiario)) {
				$noticias .= '<div class="item active">';
			}else{
				$noticias .= '<div class="item ">';
			}
				
			$noticias .= '<img src="';
			$noticias .=  $rutaBloque . '/frontera/css/imagen/' .$not['imagen'];
			$noticias .= '" alt="';
			$noticias .= 'Chania';
			$noticias .= '" width="460" height="345">';
			$noticias .= '</div>';
				
		}
		
		
		
		$noticias .= '</div>
   					  <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
      				  <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
      				  <span class="sr-only">Previous</span>
    				  </a>
    				  <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
      				  <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
      				  <span class="sr-only">Next</span>
    				  </a>
					  </div>
					  </div>';

		echo $noticias;
		
		$redesSociales = '<div class="col-lg-4 col-md-4 col-sm-12  text-center">
						  <div class="home-doctors  clearfix">
						  <div class="text-center doc-item">
					      <div class="common-doctor animated fadeInUp clearfix ae-animation-fadeInUp">
						  <ul class="list-inline social-lists animate">
						  <li><a href="#"><i class="fa fa-skype"></i></a></li>
						  <li><a href="#"><i class="fa fa-skype"></i></a></li>
						  <li><a href="#"><i class="fa fa-twitter"></i></a></li>
						  <li><a href="#"><i class="fa fa-facebook"></i></a></li>
						  </ul>
		                  <figure>
						  <img width="670" height="500" src="' . $rutaBloque . '/frontera/css/imagen/finger-769300_1920.jpg" class="doc-img animate attachment-gallery-post-single wp-post-image" alt="doctor-2"> 
						  </figure>
						  </div>
		                  <div class="visible-sm clearfix margin-gap"></div>
		                  </div>
						  </div>
						  </div>';
		
		echo $barraEstadoServicio;
		
		echo $redesSociales;
		
		echo '</div>
			  </div>';
		
		echo '		</div>
					</div>
					</div>
					</div>
		            </div>
        			</div>';
		/**
		 * En algunas ocasiones es útil pasar variables entre las diferentes páginas.
		 * SARA permite realizar esto a través de tres
		 * mecanismos:
		 * (a). Registrando las variables como variables de sesión. Estarán disponibles durante toda la sesión de usuario. Requiere acceso a
		 * la base de datos.
		 * (b). Incluirlas de manera codificada como campos de los formularios. Para ello se utiliza un campo especial denominado
		 * formsara, cuyo valor será una cadena codificada que contiene las variables.
		 * (c) a través de campos ocultos en los formularios. (deprecated)
		 */
		
		// En este formulario se utiliza el mecanismo (b) para pasar las siguientes variables:
		
		// Paso 1: crear el listado de variables
		
		$valorCodificado = "action=" . $esteBloque ["nombre"];
		$valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		$valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
		$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
		$valorCodificado .= "&opcion=generarCertificacion";
		/**
		 * SARA permite que los nombres de los campos sean dinámicos.
		 * Para ello utiliza la hora en que es creado el formulario para
		 * codificar el nombre de cada campo.
		 */
		$valorCodificado .= "&campoSeguro=" . $_REQUEST ['tiempo'];
		// Paso 4: codificar la cadena resultante
		$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
		
		$atributos ["id"] = "formSaraData"; // No cambiar este nombre
		$atributos ["tipo"] = "hidden";
		$atributos ['estilo'] = '';
		$atributos ["obligatorio"] = false;
		$atributos ['marco'] = true;
		$atributos ["etiqueta"] = "";
		$atributos ["valor"] = $valorCodificado;
		echo $this->miFormulario->campoCuadroTexto ( $atributos );
		unset ( $atributos );
		
		// ----------------FIN SECCION: Paso de variables -------------------------------------------------
		
		// ---------------- FIN SECCION: Controles del Formulario -------------------------------------------
		
		// ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
		// Se debe declarar el mismo atributo de marco con que se inició el formulario.
		
		$atributos ['marco'] = true;
		$atributos ['tipoEtiqueta'] = 'fin';
		echo $this->miFormulario->formulario ( $atributos );
	}
	public function mensaje() {
		switch ($_REQUEST ['mensaje']) {
			
			case 'sucess' :
				$estilo_mensaje = 'success'; // information,warning,error,validation
				$mensa = explode ( "\n", $_REQUEST ['valor'] );
				$atributos ["mensaje"] = "";
				foreach ( $mensa as $m ) {
					$atributos ["mensaje"] .= $m . "<br>";
				}
				break;
			
			case 'error' :
				$estilo_mensaje = 'error'; // information,warning,error,validation
				$mensa = explode ( "\n", $_REQUEST ['valor'] );
				$atributos ["mensaje"] = "";
				foreach ( $mensa as $m ) {
					$atributos ["mensaje"] .= $m . "<br>";
				}
				
				break;
		}
		// ------------------Division para los botones-------------------------
		$atributos ['id'] = 'divMensaje';
		$atributos ['estilo'] = 'marcoBotones';
		echo $this->miFormulario->division ( "inicio", $atributos );
		
		// -------------Control texto-----------------------
		$esteCampo = 'mostrarMensaje';
		$atributos ["tamanno"] = '';
		$atributos ["etiqueta"] = '';
		$atributos ["estilo"] = $estilo_mensaje;
		$atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
		echo $this->miFormulario->campoMensaje ( $atributos );
		unset ( $atributos );
		
		// ------------------Fin Division para los botones-------------------------
		echo $this->miFormulario->division ( "fin" );
		unset ( $atributos );
	}
}

$miFormulario = new Formulario ( $this->lenguaje, $this->miFormulario, $this->sql );

$miFormulario->formulario ();

?>