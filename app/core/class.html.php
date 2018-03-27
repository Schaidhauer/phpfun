<?php

	

Class Html{

	public $title;
	public $favicon;
	public $menu;
	//public $top_search_action;
	public $login;
	public $orientacao;
	public $atual;
	
	public $customCSS = "";
	public $customJS = "";
	
	public $path;
	public $session;

	public function Html($core_config,$html_config,$path)
	{
		$this->menu            = $core_config['menu'];
		$this->title           = $html_config['sysTitulo'];
		$this->sysDescricao    = $html_config['sysDescricao'];
		$this->sysAutor        = $html_config['sysAutor'];
		$this->favicon         = $html_config['favicon'];
		//$this->top_search_action = $core_config['top_search_action'];
		$this->defineOrientacao($core_config['tela_horizontal']);
		$this->path = $path;
		
		$this->session = new Sessao();
	}
	
	public function setLogin($login)
	{
		$this->login = $login;
	}
	
	public function defineOrientacao($arOrientacao){
		//$atual = basename($_SERVER['PHP_SELF']);
		$atual = $this->atual;
		
		$this->orientacao = 'v';
		
		foreach($arOrientacao as $o){
		
			if ($o == $atual)
				$this->orientacao = 'h';
		
		}
	}
	
	public function changeTitle($title)
	{
		$this->title = $title;
	}
	
	public function addCSS($cssfile)
	{
		$this->customCSS .= '<link href="'.$this->path."/".$cssfile.'" rel="stylesheet">';
	}
	
	public function addJS($jsfile)
	{
		$this->customJS .= '<script src="'.$this->path."/".$jsfile.'"></script>';
	}
	
	public function head()
	{
		echo '<!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<meta name="description" content="'.$this->sysDescricao.'">
			<meta name="author" content="'.$this->sysAutor.'">
			<link rel="shortcut icon" type="image/png" href="'.$this->path.'/'.$this->favicon.'"/>

			<title>'.$this->title.'</title>

			<!-- Bootstrap Core CSS -->
			<link href="'.$this->path.'/assets/css/bootstrap.min.css" rel="stylesheet">

			<!-- Custom CSS -->
			<link href="'.$this->path.'/assets/css/sb-admin.css" rel="stylesheet">

			<!-- Morris Charts CSS -->
			<link href="'.$this->path.'/assets/css/plugins/morris.css" rel="stylesheet">

			<!-- Custom Fonts -->
			<link href="'.$this->path.'/assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
			
			<!-- Custom CSS-->
			'.$this->customCSS.'

			<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
			<!-- WARNING: Respond.js doesnt work if you view the page via file:// -->
			<!--[if lt IE 9]>
				<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
				<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
			<![endif]-->
		</head>
		';
	}
	
	public function getNavFixed()
	{
		//RIANNE
		return "
		<!-- Brand and toggle get grouped for better mobile display -->
				<div class='navbar-header'>
					<button type='button' class='navbar-toggle' data-toggle='collapse' data-target='.navbar-ex1-collapse'>
						<span class='sr-only'>Toggle navigation</span>
						<span class='icon-bar'></span>
						<span class='icon-bar'></span>
						<span class='icon-bar'></span>
					</button>
					<a class='navbar-brand' href='#' style='color:#fff'>".$this->title."</a>
				</div>
				<!--<div class='navbar-header'>
					<form action='#' method='get' class='navbar-form'>
						<input type='text' name='search' class='form-control' />
						<input type='submit' value='Buscar' class='form-control' />
					</form>
				</div>-->
				<!-- Top Menu Items -->
				<ul class='nav navbar-right top-nav'>
					<li class='dropdown'>
						<a href='#' class='dropdown-toggle' data-toggle='dropdown'><i class='fa fa-user'></i> ".$this->login." <b class='caret'></b></a>
						<ul class='dropdown-menu'>
							<li>
								<a href='".$this->path."/login/logout'><i class='fa fa-fw fa-power-off'></i> Log Out</a>
							</li>
						</ul>
					</li>
				</ul>
		";
		
	}
	
	public function montaPesquisa(){
	
		return "
			<li class='searchFun'>
				<form action='".$this->path."/search/' method='get' class='navbar-form'>
					<input type='text' name='search' id='searchInput' value='Pesquisar...' />
				</form>
			</li>
		";
		
	}
	
	public function montaMenu(){

		//$atual = basename($_SERVER['PHP_SELF']);
		
		if ($this->orientacao == 'v')
			$nav_options = "nav navbar-nav side-nav";
		else
			$nav_options = "nav navbar-left top-nav";
			
		
		
		echo "
		<div class='collapse navbar-collapse navbar-ex1-collapse'>
			<ul class='".$nav_options."'>";
			
			echo $this->montaPesquisa();
			echo $this->montaOpcoesMenuInterno();
				
		echo "</ul>
		</div>
		";
		

	}
	
	public function nav(){
	
		echo '<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">';
			echo $this->getNavFixed();
			$this->montaMenu();
		echo '</nav>';
	
	}
	
	public function mensagemErro()
	{
		echo "<div>";
			echo "<div class='avisoFun'>";
				echo "<img src='".$this->path."/assets/img/alert.png' height=24 style='float:left;' />";
				echo "404: Essa página não existe.";
			echo "</div>";
		echo "</div>";
	
	}
	
	function jsLibs(){
	
		echo '
		
		<!-- jQuery -->
		<script src="'.$this->path.'/assets/js/jquery.js"></script>

		<!-- Bootstrap Core JavaScript -->
		<script src="'.$this->path.'/assets/js/bootstrap.min.js"></script>

		<!-- Loader for Google graphs -->
		<script src="'.$this->path.'/assets/js/loader.js"></script>
		
		<!-- Custom JS-->
		'.$this->customJS.'
		
		';
	
	}
	
	public function setAtual($atual)
	{
		$this->atual = $atual;
	}
	
	function montaOpcoesMenuInterno(){
	
		//$atual = basename($_SERVER['PHP_SELF']);
		$atual = $this->atual;
		
		$h_menu = "";
		foreach ($this->menu as $m){
			if (@$m['link']){
				$h_menu .= $this->montaOpcaoMenu($m['label'],$m['icon'],$m['link'],$atual);
			}else if (@$m['dropdown']){
				
				//foreach para prever selecionando um filho, deixar highlight no pai
				//usar o extra_highlights ou query_highlights
				foreach($m['dropdown'] as $d)
				{
					if ($atual == $d['link'])
						$atual = $m['linkdrop'];
				}
				if (@$m['extra_highlights'])
				{
					foreach(@$m['extra_highlights'] as $h)
					{
						if ($atual == $h)
							$atual = $m['linkdrop'];
					}
				}
				if (@$m['query_highlights'])
				{
					foreach(@$m['query_highlights'] as $q)
					{
						$atual_param = $_SERVER['QUERY_STRING'];
						if ($atual_param == $q)
							$atual = $atual."?".$atual_param; 
					}
				}
				$lis = "";
				foreach($m['dropdown'] as $sub)
				{
					$lis .= $this->montaOpcaoMenu($sub['label'],$sub['icon'],$sub['link'],$atual);
				}
				//tratamento para esconder o menu lateral em certas paginas.
				if ($this->orientacao == 'h'){
					$h_menu .= $this->dropDownHorizontal($m['label'],$m['icon'],$lis,'',$atual);
				}else{
					$h_menu .= $this->dropDownVertical($m['label'],$m['icon'],$lis,$m['id'],$m['linkdrop'],$atual);
				}
			
			}
		}
		
		return $h_menu;
	}
	
	function montaOpcaoMenu($nome,$icon = 'fa-dashboard',$link,$atual)
	{
		$arrow = "";
	
		if ($atual == $link)
		{
			$menu = "<li class='active'>";
			if ($this->orientacao == 'v')
				$arrow = "<span class='side-nav-selected-arrow'></span>";
		}
		else
		{
			$menu = "<li>";
		}
		$menu .= "<a href='".$this->path."/".$link."/'><i class='fa fa-fw ".$icon."'></i>".$arrow." ".$nome."</a>";
		$menu .= "</li>";
		
		
		if (sizeof($this->session->permissoes)>0)
		{
			if (in_array($link, $this->session->permissoes))
				return $menu;
			else
				return "";
		}
		else
		{
			return $menu;
		}
		
		
	}

	function dropDownHorizontal($label,$icon = 'fa-user',$lis,$link,$atual){

		if ($atual == $link)
		{
			$menu = "<li class='active'>";
			
		}
		else
			$menu = "<li>";

		return "
		
		<li class='dropdown'>
			<a href='#' class='dropdown-toggle' data-toggle='dropdown'><i class='fa fa-user'></i> ".$label." <b class='caret'></b></a>
			<ul class='dropdown-menu'>
				".$lis."
			</ul>
		</li>
		
		";

	}

	function dropDownVertical($label,$icon = 'fa-wrench',$lis,$id,$link,$atual){
		$arrow = "";
		
		if ($atual == $link)
		{
			$menu = "<li class='active'>";
			if ($this->orientacao == 'v')
				$arrow = "<span class='side-nav-selected-arrow'></span>";
		}
		else
			$menu = "<li>";
		

		return $menu."
		
		
			<a href='javascript:;' data-toggle='collapse' data-target='#".$id."'><i class='fa fa-fw ".$icon."'></i>".$arrow." ".$label." <i class='fa fa-fw fa-caret-down'></i></a>
			<ul id='".$id."' class='collapse'>
				".$lis."
			</ul>
		</li>
		
		";

	}
	
	function bodyBeginBlank($menu = true){
		
		
		echo "<body>";
	
	}
	
	function bodyBegin(){
		
		$menu = $this->nav();
		
		
		echo "
		
			<body>
			<div id='wrapper'>
				<!-- Navigation -->
				".$menu."
				<div id='page-wrapper'>
					<div class='container-fluid'>
		
		";
	
	}
	
	function bodyEndBlank($jquery){
		
		echo "
			".$this->jsLibs()."
			".$this->jQueryReady($jquery)."
		</body>
		</html>
		
		";
	
	}
	
	function bodyEnd($jquery){
		
		echo "
		
					</div>
					<!-- /.container-fluid -->
				</div>
				<!-- /#page-wrapper -->
			</div>
			<!-- /#wrapper -->
			".$this->jsLibs()."
			".$this->jQueryReady($jquery)."
		</body>
		</html>
		
		";
	
	}
	
	function jQueryReady($jquery){
	
		echo "
		<script>
		$( document ).ready(function() {

			$('#searchInput').click(function() {
				$(this).val('');
			});
		
		
			".@$jquery."
			
		});
		</script>
		";
	
	}

}


?>