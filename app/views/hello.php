<?php

require_once("app/core/class.core.php");
//require_once("app/controls/control.hello.php");


Class Hello{

	public $core;
	public $hello;
	
	public function __construct()
	{
		$this->core = new Core();
		$this->hello = new HelloController(); //controller
		
		$this->core->session->validaSessao();
		
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
			//echo "Erro ao chamar metodo ".$name;
			
			$this->core->html->head();
			$this->core->html->bodyBegin();
			$this->core->html->mensagemErro();
			$this->core->html->bodyEnd(@$jquery);
		}
	}
	
	function listar()
	{
		$this->core->html->head();
		$this->core->html->bodyBegin();
		$this->hello->conteudo();
		echo "<div class='row'>";
			echo "<p>site.com/<a href='".$this->core->html->path."/hello/'>hello</a>/</p>";
			echo "<p>site.com/hello/<a href='".$this->core->html->path."/hello/add/'>add</a>/</p>";
			echo "<p>site.com/hello/<a href='".$this->core->html->path."/hello/edit/'>edit</a>/</p>";
			//acho que aqui pode passar os parametros e dizer quem vai chamar quem
		//site.com/hello/  == metodo default
		//site.com/hello/add  == metodo add
		//site.com/hello/list  == metodo list
		//site.com/hello/v/32342/444  == metodo v com os parametros 32342 e 444
		echo "</div>";
		$this->core->html->bodyEnd(@$jquery);
	}
	
	/*
	function login(){
		$this->core->session->start_session('_s', false);
		
		$_SESSION['something'] = '27';
		echo "lendo: ".$_SESSION['something'];
	}
	
	function destroy(){
		$this->core->session->start_session('_s', false);
		session_destroy();
		echo "lendo: ".$_SESSION['something'];
	}	
	function logued(){
		$this->core->session->start_session('_s', false);
		echo "lendo: ".$_SESSION['something'];
	}	
	function logoff(){
		if(isset($_SESSION)){
                session_destroy();
            }else{echo "logofffail";}
		echo "lendo: ".$_SESSION['something'];
	}
	
	function verSession(){
		$this->core->session->start_session('_s', false);
		
		//verificar sessao valida
		//verificar validade
		//adicionar ID do usuario logado

		$_SESSION['something'] = '23';
		echo "lendo: ".$_SESSION['something'];
	}*/
	/*
	function crudEdit(){
		$this->hello->sayHello();
		$viewInfos = $this->hello->getViewInfos();
		
		
		echo "<div>..".$viewInfos['var1']."</div>";
		//echo "Comando: ".$this->cmd[1];
	}
	
	function conteudo(){
		$this->hello->sayHello();
		$viewInfos = $this->hello->getViewInfos();
		
		
		echo "<div>..".$viewInfos['var1']."</div>";
		//echo "Comando: ".$this->cmd[1];
	}*/
	
}

?>