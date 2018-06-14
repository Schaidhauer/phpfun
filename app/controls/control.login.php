<?php
require_once("app/core/class.crud.php");

Class LoginController{

	public $crud;
	public $objVar;
	public $formconfig;
	
	public function __construct()
	{
		$this->crud = new CrudBootstrap();
	}
	
	function conteudo()
	{
		
	}
	
}

?>