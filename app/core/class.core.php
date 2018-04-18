<?php
//require_once("class.router.php");
require_once("class.bd.php");
require_once("class.conexao.php");
require_once("class.help.php");
require_once("class.html.php");
require_once("class.config.php");
require_once("class.session2.php");

Class Core{
	
	//variaveis para os objetos do sistema
	public $bd;
	public $help;
	public $html;
	public $config;
	public $session;
	
	public $system_folder;
	public $system_path;
	
	public function __construct($htmlmode = true)
	{
		$this->config  = new Config();
		$this->help    = new Help();
		$this->session = new Sessao();
		
		$this->setPath();
		if ($htmlmode)
		{
			$this->html    = new HTML($this->config->config_menu,$this->config->config_html,$this->system_path);
		
			$this->getURL();
			$this->html->setAtual($this->cmd[0],$this->cmd[1]);
			//echo "[FOI ATUAL] ";
		}else{
			$this->getURL();
			//echo "[NAO FOI ATUAL] ";
		}
	}
	
	public function setPath()
	{
		$basedirHTML = $this->config->config['protocolo']."://".$_SERVER['HTTP_HOST'].$this->config->config['systemFolder'];
		$this->system_path = $basedirHTML;
	}
	
	public function conectaBD()
	{
		$this->bd = new Mysqlidb (
			$this->config->config_db['dbHost'], 
			$this->config->config_db['dbUser'], 
			$this->config->config_db['dbPass'], 
			$this->config->config_db['dbDatabase'] 
		);
	}
	
	public function checkPermissions()
	{
		$permissoes_deste_usuario = $this->session->permissoes;
		
		if (in_array($this->cmd[0], $permissoes_deste_usuario))
			return true;
		else
			return false;
		
	}
	
	public function redir()
	{
		$file    = "app/views/".$this->cmd[0].".php";
		$control = "app/controls/control.".$this->cmd[0].".php";
		
		//se as permissoes por classes estiverem ativas
		if (!$this->config->config['allowAllClasses'])
			$canRedir = $this->checkPermissions();
		else
			$canRedir = true;
		
		
		if ((file_exists($file) == true) && $canRedir)
		{
			require_once($file);
			
			if (file_exists($control) == true)
				require_once($control);
			
			
			$page = new $this->cmd[0];
			$http_argumentos = sizeof($this->cmd);
			
			if ($http_argumentos == 1)
				call_user_func(array($page, " "));
			else if ($http_argumentos == 2)
				call_user_func(array($page, $this->cmd[1]));
			else if ($http_argumentos > 2)
			{
				for($i=2;$i<$http_argumentos;$i++)
				{
					$args[] = $this->cmd[$i];
				}
				call_user_func_array(array($page, $this->cmd[1]), $args); 
			}
			
			
		}else{
			
			//se nao for enviado nada, vai pro metodo padrao
			if ($this->cmd[0] == '')
			{
				//Aqui tem que dar um redir para o metodo padrao
				header("Location: ".$this->config->config['protocolo']."://".$_SERVER['HTTP_HOST'].$this->config->config['systemFolder']."/".$this->config->config['defaultClass']."/"); /* Redirect browser */
				exit();
			}
			else
			{
				//Se for algo que nao existe, pagina de erro
				$this->html = new HTML($this->config->config_menu,$this->config->config_html,$this->system_path);
				
				$this->html->head();
				$this->html->bodyBeginBlank();
				$this->html->mensagemErro();
				$this->html->bodyEndBlank(@$jquery);
				
			}
			
			
		}
		
	}
	
	public function getURL()
	{
		$requestURI = explode('/', $_SERVER['REQUEST_URI']);
		$scriptName = explode('/',$_SERVER['SCRIPT_NAME']);

		for($i= 0;$i < sizeof($scriptName);$i++)
		{
			if ($requestURI[$i] == $scriptName[$i])
			{
				unset($requestURI[$i]);
			}
		}

		$this->cmd = array_values($requestURI);
	}
	
	public function getCommand()
	{
		return $this->cmd;
	}
	
	
}

?>