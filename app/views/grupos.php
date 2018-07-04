<?php
require_once("app/controls/control.usuarios.php");

Class Grupos{

	public $my;
	
	public function __construct()
	{
		$this->my = new UsuariosController(2); //controller
			
		Core::$session->validaSessao();
	}
	
	function view($identificador,$arguments)
	{
		Core::$html->head();
		Core::$html->bodyBegin();
		
		if (!$this->my->crud->getById($identificador))
			Core::$html->mensagemErro();
		else
			$this->my->view($identificador);
		
		Core::$html->bodyEnd();
	}
	
	function listar()
	{
		Core::$html->head();
		Core::$html->bodyBegin();
		$this->my->crud->post();
		$this->my->crud->criaFiltro();
		$this->my->conteudo();
		Core::$html->bodyEnd($this->my->crud->jqueryFiltro());
	}
	
	function add()
	{
		Core::$html->head();
		Core::$html->bodyBegin();
		$this->my->crud->criaFormAdd();
		Core::$html->bodyEnd(@$jquery);
	}
	
	function edit($id = 0)
	{
		Core::$html->head();
		Core::$html->bodyBegin();
		$this->my->crud->criaFormEdit($id);
		Core::$html->bodyEnd(@$jquery);
		
	}
	
}

?>