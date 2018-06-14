<?php
require_once("class.config.php");
require_once("class.password.php");

Class Sessao
{
    public $idUsuario;
	 
	public $logoutPage;
	public $loginPage;
	public $permissoes;
	 
	public $config;
	public $core;
	

	function Sessao()
	{
		$this->config  = new Config();
		$this->e       = new Encryption();
		$this->bdconn  = new Conexao();
		
		$this->loginPage = $this->config->config['protocolo']."://".$_SERVER['HTTP_HOST'].$this->config->config['systemFolder']."/login/";
		$this->defaultPage = $this->config->config['protocolo']."://".$_SERVER['HTTP_HOST'].$this->config->config['systemFolder']."/".$this->config->config['defaultClass']."/";
	
		session_name('_phpfun_'.$this->config->config_html['sysName']);
        @session_start();
		
		if (!$this->config->config['allowAllClasses'])
		{
			if (isset($_SESSION['_idUsuario']))
			{
				//buscar no BD os grupos deste usuario
				$grupos = $this->getGruposUsuarioComPermissao();
				$usuario = $this->getPermissoes($this->getIdUsuario());			
				$permissoes_padrao = array($this->config->config['defaultClass'],"login");
				
				//print_r($grupos);
				
				$permissoes = array_merge($permissoes_padrao,$grupos,$usuario);
				//$permissoes = implode(',',$permissoes);
				$this->permissoes = $permissoes;
			}
			else
			{
				//$this->logout();
			}
		}
	}
	
	function getGruposUsuarioComPermissao()
	{
		$sql = "SELECT idGrupo FROM ".$this->config->config['login_bd_rel_grupo']." WHERE idUsuario = '".$this->getIdUsuario()."'";
		//echo $sql;
		$retorno_grupos = $this->bdconn->select($sql);
		
		if ($retorno_grupos)
		{
			foreach($retorno_grupos as $g)
			{
				$grupos_temp[] = $this->getPermissoes($g['idGrupo']);
			}
			$grupos=array();
			foreach($grupos_temp as $gg)
			{
				
				$grupos = array_merge($grupos,$gg);
			
			}
			//return implode(','.$grupos);
			return $grupos;
		}
		else
			return array();

	}
	
	function getPermissoes($idUsuario)
	{
		$sql = "SELECT permissao FROM ".$this->config->config['login_bd_permissoes']." WHERE idUsuario = '".$idUsuario."'";
		$ret = $this->bdconn->select($sql);
		
		if ($ret)
		{
			foreach($ret as $g)
			{
				$perms[] = $g['permissao'];
			}
			//return implode(','.$perms);
			return $perms;
		}
		else
			return array();
	}
	
	function novaSessao()
	{
        //$this->Sessao();

        $_SESSION['_expira'] = $this->config->config['session_timeout'];
        $_SESSION['_session_start'] = time();
		$_SESSION['_session_id'] = session_id();
	}

    function validaSessao($validar=true)
	{
		if ($validar)
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
		}
		session_write_close();
	}

	function login($usuario)
	{
		$this->novaSessao();
		$usuario = $this->e->encode($usuario);
		$_SESSION['_idUsuario'] = $usuario;
		header("Location: ".$this->defaultPage);
	}

	function getValores()
	{
    	$this->idUsuario = $this->e->decode($_SESSION['_idUsuario']);
	}

    public function getIdUsuario()
	{
		$this->getValores();
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