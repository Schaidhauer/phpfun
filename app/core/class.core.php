<?php

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
	public $config;
	public $system_folder;
	
	//chamar nas outras como core::$system_path; e core::$session
	public static $system_path;
	public static $session;
	public static $html;
	
	public function __construct($htmlmode = true)
	{
		$this->config  = new Config();
		$this->help    = new Help();
		//$this->session = new Sessao();
		self::$session = new Sessao();
		
		$this->setPath();
		if ($htmlmode)
		{
			//$this->html    = new HTML($this->config->config_menu,$this->config->config_html,$this->system_path);
			//$this->html    = new HTML($this->config->config_menu,$this->config->config_html,self::$system_path);
			self::$html    = new HTML($this->config->config_menu,$this->config->config_html,self::$system_path);
		
			$this->getURL();
			//$this->html->setAtual($this->cmd[0],$this->cmd[1]);
			self::$html->setAtual($this->cmd[0],$this->cmd[1]);
			//echo "[FOI ATUAL] ";
		}else{
			$this->getURL();
			//echo "[NAO FOI ATUAL] ";
		}
	}
	
	public function setPath()
	{
		$basedirHTML = $this->config->config['protocolo']."://".$_SERVER['HTTP_HOST'].$this->config->config['systemFolder'];
		//$this->system_path = $basedirHTML;
		self::$system_path = $basedirHTML;
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
		$this->splitMethodsAndArgs();
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
			
			
			//fazer o uso do method_exists aqui, ou is_callable.
			//o filtro dentro das classes nao pode ser via __call, tem que ser menos complexo dentro de cada classe.
			
			$page = new $this->cmd[0];
			
			//$http_argumentos = sizeof($this->cmd);
			
			//ver se o metodo nao ta em branco
			if (($this->http_method == "") || ($this->http_method == " "))
			{
				//metodo padrao
				call_user_func(array($page, "listar"));
			
			}
			else
			{
				//metodo não é em branco
				
				//validar se o metodo que quer chamar, ao menos é chamável (com ou sem __call)
				if (is_callable(array($page, $this->http_method)))
				{
					//agora verificamos se o metodo existe mesmo (ignorando o call, este vai ser o papel deste if para nao precisar definir __call em todas as views - que é um saco)
					if (method_exists($page, $this->http_method))
					{
						//se existir e for em branco, enviar para o metodo padrao
						//if (($this->http_method == "") || ($this->http_method == " "))
						//	call_user_func(array($page, "listar"));
						//else
						//{
							if ($this->http_argumentos == 2)
							{
								call_user_func(array($page, $this->http_method));
							}
							else if ($this->http_argumentos > 2)
							{
								call_user_func_array(array($page, $this->http_method), $this->http_args); 
							}
						//}
					}
					
				
					/*
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
					*/
				}
				else
				{
					//pode ser que o método não exista
					//mas vamos ver se não é um segundo parametro, tratar isso no metodo VIEW
					//validar antes o http_args, pois pode ser um ? (do GET)
					if (is_array(@$this->http_args))
						call_user_func_array(array($page, 'view'), array_merge(array($this->http_method),$this->http_args)); 
					else
					{
						//é sinal que é um meotodo que nao existe, e provavelmente um '?', entao tentar enviar pro padrao
						call_user_func(array($page, "listar"));
					}
					
					/*
					//não existe, da um erro.
					//call_user_func(array($page, "listar"));
					self::$html->head();
					self::$html->bodyBegin();
						echo "Página não existe (".$this->http_method.")";
					self::$html->bodyEnd(@$jquery);
					*/
				}
			}
		}
		else
		{
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
				/*$this->html = new HTML($this->config->config_menu,$this->config->config_html,$this->system_path);
				
				$this->html->head();
				$this->html->bodyBeginBlank();
				$this->html->mensagemErro();
				$this->html->bodyEndBlank(@$jquery);
				*/
				
				self::$html = new HTML($this->config->config_menu,$this->config->config_html,self::$system_path);
				
				self::$html->head();
				self::$html->bodyBeginBlank();
				self::$html->mensagemErro();
				self::$html->bodyEndBlank();
				
			}
			
			
		}
		
	}
	
	public function splitMethodsAndArgs()
	{
		$this->http_argumentos = sizeof($this->cmd);
		
		if ($this->http_argumentos == 1)
			$this->http_method = " ";
		else if ($this->http_argumentos == 2)
		{
			$this->http_method = $this->cmd[1];
		}
		else if ($this->http_argumentos > 2)
		{
			for($i=2;$i<$this->http_argumentos;$i++)
			{
				$args[] = $this->cmd[$i];
			}
			$this->http_method = $this->cmd[1];
			$this->http_args   = $args;
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