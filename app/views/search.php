<?php
ini_set('max_execution_time', 300); 
require_once("app/controls/control.alertas.php");

Class Search{
	
	public $jqueryAlerts;
	
	public function __construct()
	{
		$this->my         = new SearchController();
		$this->alertas    = new AlertasController();
		
		Core::$session->validaSessao();
		$this->path = Core::$system_path;
		
		$this->jqueryAlerts = "
		
		";
	}
	
	function view($identificador,$arguments)
	{
		Core::$session->validaSessao();
		Core::$html->head();
		Core::$html->bodyBegin();
		
		if (!$this->my->crud->getById($identificador))
			Core::$html->mensagemErro();
		else
			$this->my->view($identificador);
		
		
		Core::$html->bodyEnd($this->jquery);
	}
	
	function listar()
	{
		Core::$session->validaSessao();
		
		Core::$html->head();
		Core::$html->bodyBegin();
		
		$this->my->conteudo();
		
		$r = "<div class=\"panel panel-default\">";
			$r .= "<div class=\"panel-heading\">";
				$r .= "<div class=\"row\">";
					$r .= "<div class=\"col-xs-9\">";
						$r .= "<div>Logs hoje</div>";
					$r .= "</div>";
				$r .= "</div>";
			$r .= "</div>";
			$r .= "<div class=\"panel-footer\">";
			$r .= "<img src=\"".$this->path."/assets/img/ajax-loader.gif\" height=32/>";
			$r .= "</div>";
		$r .= "</div>";
		
		$jquery = "
		
			$('#divSearchLogs').html('".$r."');
			
			$.ajax({
				method: 'POST',
				url: '".$this->path."/search/getLogsSearch/".$_GET['s']."'
			})
			.done(function( msg ) {
				$('#divSearchLogs').html(msg);
			})
			.fail(function() {
				$('#divSearchLogs').html( 'Error 789456' );
			});
		
		";
		
		Core::$html->bodyEnd(@$jquery);
	}
	
	public function getLogsSearch($search)
	{
	
		echo $this->my->montaPainelLogs($search);
	
	}
	
	
}

?>