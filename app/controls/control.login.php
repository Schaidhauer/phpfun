<?php
require_once("app/core/class.core.php");
require_once("app/core/class.crud.php");

Class LoginController{

	public $core;
	public $crud;
	public $objVar;
	public $formconfig;
	
	public function __construct(){
		$this->core = new Core();
		
		$this->crud = new CrudBootstrap();
	}
	
	function conteudo()
	{
		
	}
	
}

?>