<?php

require_once("class.config.php");

Class Sessao{
    var $idUsuario;
	 
	var $logoutPage;
	var $loginPage;
	var $permissoes;
	 
	var $config;
	var $core;
	

	function Sessao()
	{
		$this->config        = new Config();
		
		$this->loginPage = $this->config->config['protocolo']."://".$_SERVER['HTTP_HOST'].$this->config->config['systemFolder']."/login/";
		$this->defaultPage = $this->config->config['protocolo']."://".$_SERVER['HTTP_HOST'].$this->config->config['systemFolder']."/".$this->config->config['defaultClass']."/";
	
		session_name('_phpfun_'.$this->config->config_html['sysName']);
        @session_start();
		
		if (!$this->config->config['allowAllClasses'])
		{
			if (isset($_SESSION['_idUsuario']))
			{
				//buscar do BD
				$this->permissoes = array($this->config->config['defaultClass'],"ambientes");
			}
		}
	}
	
	function novaSessao()
	{
        //$this->Sessao();

        $_SESSION['_expira'] = 3600;//1 hora
        $_SESSION['_session_start'] = time();
		$_SESSION['_session_id'] = session_id();
	}

    function validaSessao()
	{
		//verifica se tem sessao montada antes de tudo
        if (isset($_SESSION['_idUsuario']))
		{
        	$tempo_sessao = time() - @$_SESSION['_session_start'];
            if($tempo_sessao > @$_SESSION['_expira'])
			{
            	session_destroy();
                //redireciona para uma pagina de saida
                header("Location: ".$this->loginPage);
			}
			else
			{
				$this->renovarSessao();
                //$this->get();
				
            }
        }
		else
		{
        	session_destroy();
            header("Location: ".$this->loginPage);
            exit;
        }
		
		session_write_close();
	}

	function login($usuario)
	{
		$this->novaSessao();
		$_SESSION['_idUsuario'] = $usuario;
		header("Location: ".$this->defaultPage);
	}

	function getValores(){
    	$this->idUsuario = $_SESSION['_idUsuario'];
	}

    function getIdUsuario(){
    	return $this->idUsuario;
    }

	function logout()
	{
		if(isset($_SESSION))
		{
			session_destroy();
			header("Location: ../../login/");
		}
	}

	function renovarSessao()
	{
		$_SESSION['_session_start'] = time();
	}

	function debugSession()
	{
		echo "<pre>";
		print_r($_SESSION);
		echo "</pre>";
	}



}



?>