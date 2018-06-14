<?php
Class Login{

	public $my;
	
	public function __construct(){
		//$this->core = new Core();
		$this->my = new LoginController(); //controller
		
	}
	
	function view($identificador,$arguments)
	{
		Core::$html->head();
		Core::$html->bodyBegin();
		
		if (!$this->my->crud->getById($identificador))
			Core::$html->mensagemErro();
		else
			$this->listar();
		
		
		Core::$html->bodyEnd();
	}
	
	function listar()
	{
		Core::$html->head();
		Core::$html->bodyBeginBlank();
		$this->my->crud->post();
		$this->my->crud->criaFormLogin();
		Core::$html->bodyEndBlank(@$jquery);
	}
	
	function logout()
	{
		$this->my->crud->logoutCRUD();
	}
	
}

?>