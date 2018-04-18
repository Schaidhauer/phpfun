<?php

require_once("app/core/class.core.php");


Class Login{

	public $core;
	public $my;
	
	public function __construct(){
		$this->core = new Core();
		$this->my = new LoginController(); //controller
		
	}
	
	public function __call($name, $arguments)
	{	
		//Se enviar o metodo em branco, chama um metodo em branco
		if (($name == '') || ($name == ' '))
		{
			$this->listar();
		}
		else
		{
			//Se não achar o metodo, envia um erro.
			echo "Erro ao chamar metodo ".$name;
			
			$this->core->html->head();
			$this->core->html->bodyBegin();
			$this->core->html->mensagemErro();
			$this->core->html->bodyEnd(@$jquery);
		}
	}
	
	function listar()
	{
		$this->core->html->head();
		$this->core->html->bodyBeginBlank();
		$this->my->crud->post();
		$this->my->crud->criaFormLogin();
		$this->core->html->bodyEndBlank(@$jquery);
	}
	
	function logout()
	{
		$this->my->crud->logoutCRUD();
	}
	
}

?>