<?php

Class Config{
	
	public $config_menu = array();
	public $config_html = array();
	public $config_db = array();
	public $config = array();
	
	public function __construct(){
		$this->loadConfig();
		$this->loadDBConfig();
		$this->loadHTMLConfig();
		$this->loadMenuConfig();
	}
	
	public function loadMenuConfig(){
		include("app/config/menu.php");
		$this->config_menu = $config_menu;
	}
	
	public function loadConfig(){
		include("app/config/config.php");
		$this->config = $config_g;
	}
	
	public function loadDBConfig(){
		include("app/config/database.php");
		$this->config_db = $config_db;
	}
	
	public function loadHTMLConfig(){
		include("app/config/html.php");
		$this->config_html = $config_html;
	}
}


?>