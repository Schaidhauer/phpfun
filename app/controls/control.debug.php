<?php
require_once("app/core/class.help.php");

Class DebugController{

	public $crud;

	public $active = false;
	public $color = "#FFF";
	
	public function __construct()
	{
		$this->bdconn     = new Conexao();
		$this->help       = new Help();
	}
	
	function setDebugAtive($newState)
	{
		$this->active = $newState;
	}
	
	function setDebugColor($newColor)
	{
		$this->color = $newColor;
	}
	
	function configDebug($arg1,$arg2)
	{
		if ($arg2 == '')
			$arg2 = 'INFO';
			
		if (strtoupper($arg1) == 'DEBUG')
		{
			$this->setDebugAtive(true);
			$this->setDebugColor('#0F0');
		}
		else
		{
			$this->setDebugAtive(false); 
			$this->setDebugColor('#FFF');
		}
		
		$this->setDebugLevel($arg2);
		$this->debugBody();
	}
	
	function setDebugLevel($level = 'INFO')
	{
		$this->level = $level;
	}
	
	function debugBody()
	{
		echo "<body style='background-color:#000; color: ".$this->color.";'>";
	}
	
	function debugFooter()
	{
		echo "</body>";
	}
	
	function debug($textLevel,$text)
	{
		$debugLevel = strtoupper($this->level);
		//TRACE,INFO,WARN,ERROR
		if ($this->active)
		{	
			if (
			($debugLevel == 'TRACE') || 
			(($debugLevel == 'INFO') && (($textLevel == 'INFO') || ($textLevel == 'ERROR') || ($textLevel == 'WARN'))) ||
			(($debugLevel == 'WARN') && (($textLevel == 'ERROR') || ($textLevel == 'WARN'))) ||
			(($debugLevel == 'ERROR') && (($textLevel == 'ERROR')))
			)
			{
				if ($textLevel == 'INFO')
					$textLevel = 'INFOR';
					
				
				if ($textLevel == 'TRACE')
					$textLevel = "<span style='color:blue'>TRACE</span>";
				if ($textLevel == 'ERROR')
					$textLevel = "<span style='color:red'>ERROR</span>";
				if ($textLevel == 'WARN')
					$textLevel = "<span style='color:yellow'>WARNI</span>";
			
				echo date("d/m/Y H:i:s");
				echo " ".$textLevel;
				echo " ".$text;
				echo "<br/>";
			}
		}
	}
	
	
}

?>