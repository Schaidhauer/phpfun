<?php


Class Hello{

	public $hello;
	
	public function __construct()
	{
		$this->hello = new HelloController(); //controller
		
		Core::$session->validaSessao();
		
	}
	
	function view($identificador,$arguments)
	{
		Core::$html->head();
		Core::$html->bodyBegin();
		
		if (!$this->my->crud->getById($identificador))
			Core::$html->mensagemErro();
		else
			$this->my->view($identificador);
		
		
		Core::$html->bodyEnd();
	}
	
	function listar()
	{
		Core::$html->head();
		Core::$html->bodyBegin();
		$this->hello->conteudo();
		echo "<div class='row'>";
			echo "<p>site.com/<a href='".Core::$html->path."/hello/'>hello</a>/</p>";
			echo "<p>site.com/hello/<a href='".Core::$html->path."/hello/add/'>add</a>/</p>";
			echo "<p>site.com/hello/<a href='".Core::$html->path."/hello/edit/'>edit</a>/</p>";
			//acho que aqui pode passar os parametros e dizer quem vai chamar quem
		//site.com/hello/  == metodo default
		//site.com/hello/add  == metodo add
		//site.com/hello/list  == metodo list
		//site.com/hello/v/32342/444  == metodo v com os parametros 32342 e 444
		echo "</div>";
		Core::$html->bodyEnd(@$jquery);
	}
	
	/*
	function login(){
		Core::$session->start_session('_s', false);
		
		$_SESSION['something'] = '27';
		echo "lendo: ".$_SESSION['something'];
	}
	
	function destroy(){
		Core::$session->start_session('_s', false);
		session_destroy();
		echo "lendo: ".$_SESSION['something'];
	}	
	function logued(){
		Core::$session->start_session('_s', false);
		echo "lendo: ".$_SESSION['something'];
	}	
	function logoff(){
		if(isset($_SESSION)){
                session_destroy();
            }else{echo "logofffail";}
		echo "lendo: ".$_SESSION['something'];
	}
	
	function verSession(){
		Core::$session->start_session('_s', false);
		
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