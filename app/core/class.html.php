<?php

Class Html{

	public $title;
	public $favicon;
	public $menu;
	//public $top_search_action;
	public $login;
	//public $orientacao;
	//public $orientacao_horizontais;
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
		//$this->orientacao_horizontais = $core_config['tela_horizontal'];
		//$this->defineOrientacao($core_config['tela_horizontal']);
		//$this->orientacao = 'v';
		$this->mostraMenuEsquerda = true;
		$this->path = $path;
		
		$this->session = new Sessao();
		
		//custom feio
		$this->montaFavoritos();
	}
	
	//custom feio
	public function montaFavoritos()
	{
		$bdconn = new Conexao();
		$sql = "SELECT id,title
			FROM wiki WHERE favorito = 1;";
		$res = $bdconn->select($sql);	
		
		if ($res)
		{
			foreach($res as $f)
			{
				$favoritos[]=array('label'=>$f['title'],'icon'=>'fa-star','link'=>'wiki/'.$f['id']);
			}
			$this->menu[] = array('label'=>'Favoritos','icon'=>'fa-star','id'=>'idFav','dropdown'=>$favoritos);
		}
	}
	
	public function setLogin($login)
	{
		$this->login = $login;
	}
	
	public function setMenuEsquerda($show)
	{
		$this->mostraMenuEsquerda = $show;
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
			<link href="'.$this->path.'/assets/theme/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

			<!-- MetisMenu CSS -->
			<link href="'.$this->path.'/assets/theme/vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

			<!-- SBADMIN THEME -->
			<link href="'.$this->path.'/assets/theme/dist/css/sb-admin-2.min.css" rel="stylesheet">
			<link href="'.$this->path.'/assets/css/logfun.css" rel="stylesheet">
			
			<!-- Custom CSS-->
			'.$this->customCSS.'
			
			<!-- PLUGINS -->
			<link href="'.$this->path.'/assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css">
			<link href="'.$this->path.'/assets/plugins/datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">

			<!-- Morris Charts CSS -->
			<!--link href="'.$this->path.'/assets/theme/vendor/morrisjs/morris.css" rel="stylesheet"-->

			<!-- Custom Fonts -->
			<link href="'.$this->path.'/assets/theme/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

			<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
			<!-- WARNING: Respond.js doesnt work if you view the page via file:// -->
			<!--[if lt IE 9]>
				<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
				<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
			<![endif]-->
		</head>
		';
	}
	
	public function menuSuperior()
	{
		return "
		<div class='navbar-header' style='width: 251px; border-right:1px solid #e7e7e7;'>
			<button type='button' class='navbar-toggle' data-toggle='collapse' data-target='.navbar-collapse'>
				<span class='sr-only'>Toggle navigation</span>
				<span class='icon-bar'></span>
				<span class='icon-bar'></span>
				<span class='icon-bar'></span>
			</button>
			<a class='navbar-brand' href='".$this->path."/'>".$this->title."</a>
			<a class='navbar-brand' href='javascript:return false;' id='escondeMenu' title='Esconde/Mostrar Menu lateral' style='float:right;'><i class='fa fa-list fa-fw'></i> </a>
			
		</div>
		<form action='".$this->path."/search/' method='get' class='navbar-form' style='float:left;' id='formSearch'>
			<div class='input-group custom-search-form'>
				<input type='text' name='s' class='form-control' placeholder='Search...' value='".@$_GET['s']."' style='width: 100%; min-width: 300px;'>
				<span class='input-group-btn'>
				<button class='btn btn-default' type='button' id='idBtnSearch'>
					<i class='fa fa-search'></i>
				</button>
			</span>
			</div>
		</form>
		<!-- /.navbar-header -->
		
		<ul class='nav navbar-top-links navbar-right'>
			<li class='dropdown'>
				<a class='dropdown-toggle' data-toggle='dropdown' href='#'>
					<i class='fa fa-envelope fa-fw'></i> <i class='fa fa-caret-down'></i>
				</a>
				<ul class='dropdown-menu dropdown-messages'>
					<li>
						<a href='#'>
							<div>
								<strong>John Smith</strong>
								<span class='pull-right text-muted'>
									<em>Yesterday</em>
								</span>
							</div>
							<div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
						</a>
					</li>
					<li class='divider'></li>
					<li>
						<a href='#'>
							<div>
								<strong>John Smith</strong>
								<span class='pull-right text-muted'>
									<em>Yesterday</em>
								</span>
							</div>
							<div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
						</a>
					</li>
					<li class='divider'></li>
					<li>
						<a href='#'>
							<div>
								<strong>John Smith</strong>
								<span class='pull-right text-muted'>
									<em>Yesterday</em>
								</span>
							</div>
							<div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
						</a>
					</li>
					<li class='divider'></li>
					<li>
						<a class='text-center' href='#'>
							<strong>Read All Messages</strong>
							<i class='fa fa-angle-right'></i>
						</a>
					</li>
				</ul>
				<!-- /.dropdown-messages -->
			</li>
			<!-- /.dropdown -->
			<li class='dropdown'>
				<a class='dropdown-toggle' data-toggle='dropdown' href='#'>
					<i class='fa fa-tasks fa-fw'></i> <i class='fa fa-caret-down'></i>
				</a>
				<ul class='dropdown-menu dropdown-tasks'>
					<li>
						<a href='#'>
							<div>
								<p>
									<strong>Task 1</strong>
									<span class='pull-right text-muted'>40% Complete</span>
								</p>
								<div class='progress progress-striped active'>
									<div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='40' aria-valuemin='0' aria-valuemax='100' style='width: 40%'>
										<span class='sr-only'>40% Complete (success)</span>
									</div>
								</div>
							</div>
						</a>
					</li>
					<li class='divider'></li>
					<li>
						<a href='#'>
							<div>
								<p>
									<strong>Task 2</strong>
									<span class='pull-right text-muted'>20% Complete</span>
								</p>
								<div class='progress progress-striped active'>
									<div class='progress-bar progress-bar-info' role='progressbar' aria-valuenow='20' aria-valuemin='0' aria-valuemax='100' style='width: 20%'>
										<span class='sr-only'>20% Complete</span>
									</div>
								</div>
							</div>
						</a>
					</li>
					<li class='divider'></li>
					<li>
						<a href='#'>
							<div>
								<p>
									<strong>Task 3</strong>
									<span class='pull-right text-muted'>60% Complete</span>
								</p>
								<div class='progress progress-striped active'>
									<div class='progress-bar progress-bar-warning' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' style='width: 60%'>
										<span class='sr-only'>60% Complete (warning)</span>
									</div>
								</div>
							</div>
						</a>
					</li>
					<li class='divider'></li>
					<li>
						<a href='#'>
							<div>
								<p>
									<strong>Task 4</strong>
									<span class='pull-right text-muted'>80% Complete</span>
								</p>
								<div class='progress progress-striped active'>
									<div class='progress-bar progress-bar-danger' role='progressbar' aria-valuenow='80' aria-valuemin='0' aria-valuemax='100' style='width: 80%'>
										<span class='sr-only'>80% Complete (danger)</span>
									</div>
								</div>
							</div>
						</a>
					</li>
					<li class='divider'></li>
					<li>
						<a class='text-center' href='#'>
							<strong>See All Tasks</strong>
							<i class='fa fa-angle-right'></i>
						</a>
					</li>
				</ul>
				<!-- /.dropdown-tasks -->
			</li>
			<!-- /.dropdown -->
			<li class='dropdown'>
				<a class='dropdown-toggle' data-toggle='dropdown' href='#'>
					<i class='fa fa-bell fa-fw'></i> <i class='fa fa-caret-down'></i>
				</a>
				<ul class='dropdown-menu dropdown-alerts'>
					<li>
						<a href='#'>
							<div>
								<i class='fa fa-comment fa-fw'></i> New Comment
								<span class='pull-right text-muted small'>4 minutes ago</span>
							</div>
						</a>
					</li>
					<li class='divider'></li>
					<li>
						<a href='#'>
							<div>
								<i class='fa fa-twitter fa-fw'></i> 3 New Followers
								<span class='pull-right text-muted small'>12 minutes ago</span>
							</div>
						</a>
					</li>
					<li class='divider'></li>
					<li>
						<a href='#'>
							<div>
								<i class='fa fa-envelope fa-fw'></i> Message Sent
								<span class='pull-right text-muted small'>4 minutes ago</span>
							</div>
						</a>
					</li>
					<li class='divider'></li>
					<li>
						<a href='#'>
							<div>
								<i class='fa fa-tasks fa-fw'></i> New Task
								<span class='pull-right text-muted small'>4 minutes ago</span>
							</div>
						</a>
					</li>
					<li class='divider'></li>
					<li>
						<a href='#'>
							<div>
								<i class='fa fa-upload fa-fw'></i> Server Rebooted
								<span class='pull-right text-muted small'>4 minutes ago</span>
							</div>
						</a>
					</li>
					<li class='divider'></li>
					<li>
						<a class='text-center' href='#'>
							<strong>See All Alerts</strong>
							<i class='fa fa-angle-right'></i>
						</a>
					</li>
				</ul>
				<!-- /.dropdown-alerts -->
			</li>
			<!-- /.dropdown -->
			<li class='dropdown'>
				<a class='dropdown-toggle' data-toggle='dropdown' href='#'>
					<i class='fa fa-user fa-fw'></i> <i class='fa fa-caret-down'></i>
				</a>
				<ul class='dropdown-menu dropdown-user'>
					<li><a href='#'><i class='fa fa-user fa-fw'></i> User Profile</a>
					</li>
					<li><a href='#'><i class='fa fa-gear fa-fw'></i> Settings</a>
					</li>
					<li class='divider'></li>
					<li><a href='".$this->path."/login/logout/'><i class='fa fa-sign-out fa-fw'></i> Logout</a>
					</li>
				</ul>
				<!-- /.dropdown-user -->
			</li>
			<!-- /.dropdown -->
		</ul>
		<!-- /.navbar-top-links -->
		
		
		<!-- Brand and toggle get grouped for better mobile display 
				
				<ul class='nav navbar-right top-nav'>
					<li class='dropdown'>
						<a href='#' class='dropdown-toggle' data-toggle='dropdown'><i class='fa fa-user'></i> ".$this->login." <b class='caret'></b></a>
						<ul class='dropdown-menu'>
							<li>
								<a href='".$this->path."/login/logout'><i class='fa fa-fw fa-power-off'></i> Log Out</a>
							</li>
						</ul>
					</li>
				</ul>-->
		";
		
	}
	
	public function montaPesquisa(){
	
		if (!$this->mostraMenuEsquerda)
			$escondeMenuCss = " style='display:none;'";
	
		return "
			
			<!--<li><a href='javascript:return false;' id='escondeMenu2'><i class='fa fa-list fa-fw'></i> </a></li>-->
			
			<!--
			<li class='sidebar-search'".@$escondeMenuCss." style='padding: 0px; '>
				<form action='".$this->path."/search/' method='get' class='navbar-form'>
					<div class='input-group custom-search-form'>
						<input type='text' class='form-control' placeholder='Search...'>
						<span class='input-group-btn'>
						<button class='btn btn-default' type='button'>
							<i class='fa fa-search'></i>
						</button>
					</span>
					</div>
				</form>
				
			</li>-->
		";
		
	}
	
	public function menuLateral()
	{		
		if ($this->mostraMenuEsquerda)
			$nav_options = "";
		else
			$nav_options = " lf_sidebar_closed";
		
		echo "
		<div class='navbar-default sidebar ".$nav_options."' id='idMenuLateral' role='navigation'>
            <div class='sidebar-nav navbar-collapse'>
				<ul class='nav' id='side-menu'>";
				
				echo $this->montaPesquisa();
				echo $this->montaOpcoesMenuInterno();
				
				
		echo "  </ul>
			</div>
		</div>
		";
	}
	
	public function nav(){
	
		echo '<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">';
			echo $this->menuSuperior();
			$this->menuLateral();
		echo '</nav>';
	
	}
	
	public function mensagemErro()
	{
		/*
		echo "<div>";
			echo "<div class='avisoFun'>";
				echo "<img src='".$this->path."/assets/img/alert.png' height=24 style='float:left;' />";
				echo "404: Essa página não existe.";
			echo "</div>";
		echo "</div>";
		*/
		echo "
			<div class='alert alert-danger' style='text-align: center;'>
				404: Essa página não existe.
			</div>
		";
	
	}
	
	function jsLibs(){
	
		echo '
		
		
		<!-- jQuery -->
		<script src="'.$this->path.'/assets/theme/vendor/jquery/jquery.min.js"></script>

		<!-- Bootstrap Core JavaScript -->
		<script src="'.$this->path.'/assets/theme/vendor/bootstrap/js/bootstrap.min.js"></script>

		<!-- Metis Menu Plugin JavaScript -->
		<script src="'.$this->path.'/assets/theme/vendor/metisMenu/metisMenu.min.js"></script>

		<!-- Morris Charts JavaScript -->
		<!--script src="'.$this->path.'/assets/theme/vendor/raphael/raphael.min.js"></script-->
		<!--script src="'.$this->path.'/assets/theme/vendor/morrisjs/morris.min.js"></script-->
		<!--script src="'.$this->path.'/assets/theme/data/morris-data.js"></script-->

		<!-- Custom Theme JavaScript -->
		<script src="'.$this->path.'/assets/theme/dist/js/sb-admin-2.min.js"></script>
		
		<!-- PLUGINS -->
		<script src="'.$this->path.'/assets/plugins/select2/js/select2.min.js"></script>
		<script src="'.$this->path.'/assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
		<script src="'.$this->path.'/assets/plugins/validator/validator.min.js"></script>
		
		<!-- Custom JS-->
		'.$this->customJS.'
		
		';
	
	}
	
	public function setAtual($atual,$segundo='')
	{
		//echo "[setAtual: ".$atual."]";
		$this->atual = $atual;
		$this->atualSegundo = $segundo;
	}
	
	function montaOpcoesMenuInterno()
	{
		if ($this->atualSegundo == '')
			$atual = $this->atual;
		else
			$atual = $this->atualSegundo;
		//echo "[usando atual: ".$atual."]";
		//echo "[usando atualSegundo: ".$this->atualSegundo."]";
		
		$h_menu = "";
		foreach ($this->menu as $m)
		{
			if (@$m['link'])
			{
				$h_menu .= $this->montaOpcaoMenu($m['label'],$m['icon'],$m['link'],$atual);
			}
			else if (@$m['dropdown'])
			{
				$paiHighlight = false;
				$netoHighlight = false;
				//foreach para prever selecionando um filho, deixar highlight no pai
				//usar o extra_highlights ou query_highlights
				foreach($m['dropdown'] as $d)
				{
					//echo $d['link'];
					//se um dor filhos for o highlight, o pai deve expandir (colocando classe IN)
					if (@$d['link'])
					{
						//echo "[ $atual == ".$d['link']."]";
						if ($atual == $d['link'])
							$paiHighlight = true;
					}
					else
					{
						foreach($d['dropdown'] as $t)
						{
							//se for o neto, entao é o pai tambem
							if ($atual == $t['link'])
							{
								$paiHighlight = true;
								$netoHighlight = true;
							}
						}
					}
					//	$atual = $m['linkdrop'];
				}
				/*
				if (@$m['extra_highlights'])
				{
					foreach(@$m['extra_highlights'] as $h)
					{
						//if ($atual == $h)
							//$atual = $m['linkdrop'];
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
				}*/
				
				$lis = "";
				$lis_trd = "";
				foreach($m['dropdown'] as $sub)
				{
					$lis_trd = "";
					//verificar se tem mais um nivel de dropdown
					if (@$sub['dropdown'])
					{
						foreach($sub['dropdown'] as $trd)
						{
							$lis_trd .= $this->montaOpcaoMenu($trd['label'],$trd['icon'],$trd['link'],$atual);
						}
						
						//if ($this->orientacao == 'h'){
						//	$lis .= $this->dropDownHorizontal($sub['label'],$sub['icon'],$lis_trd,'',$atual,true);
						//}else{
							$lis .= $this->dropDownVertical($sub['label'],$sub['icon'],$lis_trd,'',$atual,true,$netoHighlight);
						//}
						
					}
					else
						$lis .= $this->montaOpcaoMenu($sub['label'],$sub['icon'],$sub['link'],$atual);
				}
				//tratamento para esconder o menu lateral em certas paginas.
				//if ($this->orientacao == 'h'){
				//	$h_menu .= $this->dropDownHorizontal($m['label'],$m['icon'],$lis,'',$atual);
				//}else{
					//$h_menu .= $this->dropDownVertical($m['label'],$m['icon'],$lis,$m['id'],$m['linkdrop'],$atual);
					$h_menu .= $this->dropDownVertical($m['label'],$m['icon'],$lis,'',$atual,false,$paiHighlight);
				//}
			
			}
		}
		
		return $h_menu;
	}
	
	function montaOpcaoMenu($nome,$icon = 'fa-dashboard',$link,$atual)
	{
		//$arrow = "";
		if (!$this->mostraMenuEsquerda)
			$escondeMenuCss = " style='display:none;'";
		
		//echo "($atual == $link)";
		if ($atual == $link)
		{
			
			$menu = "<li class='active'>";
			$aclass = "class='active'";
			//if ($this->orientacao == 'v')
			//	$arrow = "<span class='side-nav-selected-arrow'></span>";
		}
		else
		{
			$menu = "<li>";
			$aclass = "";
		}
		//$menu .= "<a href='".$this->path."/".$link."/'><i class='fa fa-fw ".$icon."'></i>".$arrow." ".$nome."</a>";
		$menu .= "<a href='".$this->path."/".$link."/' ".$aclass." title='".$nome."'><i class='fa ".$icon." fa-fw'></i> <span class='menuTitles'".@$escondeMenuCss.">".$nome."</span></a>";
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

	function dropDownHorizontal($label,$icon = 'fa-user',$lis,$link,$atual,$terceiro=false)
	{

		if ($atual == $link)
		{
		
			$menu = "<li class='active'>";
			
		}
		else
			$menu = "<li>";
			
		if ($terceiro)
			$class_drop = 'nav nav-third-level';
		else
			$class_drop = 'nav nav-second-level';

		return "
		
		<li>
			<a href='#'><i class='fa ".$icon." fa-fw'></i> ".$label."<span class='fa arrow'></span></a>
			<ul class='nav ".$class_drop."'>
				".$lis."
			</ul>
		</li>
		
		";

	}

	function dropDownVertical($label,$icon = 'fa-wrench',$lis,$link,$atual,$terceiro=false,$paiHighlight=false){
		//$arrow = "";
		
		if (!$this->mostraMenuEsquerda)
			$escondeMenuCss = " style='display:none;'";
		
		if ($paiHighlight){
			$class_in = " in ";
			//echo "<p>[PAI: ".$label." ";
		}else{
			//echo "<p>[PAI: ".$label." ";
			$class_in = "";
		}
		
		if ($terceiro)
			$class_drop = 'nav nav-third-level'.$class_in;
		else
			$class_drop = 'nav nav-second-level'.$class_in;
			
			
		//echo "class_drop: ".$class_drop." ]";
		//echo "(3- $atual == $link)";
		
		/*if ($atual == $link)
		{
		
			$menu = "<li class='active'>";
			//if ($this->orientacao == 'v')
				//$arrow = "<span class='side-nav-selected-arrow'></span>";
		}
		else*/
			$menu = "<li>";
		
		//<a href='javascript:;' data-toggle='collapse' data-target='#".$id."'><i class='fa fa-fw ".$icon."'></i>".$arrow." ".$label." <i class='fa fa-fw fa-caret-down'></i></a>
		return $menu."
		
			<a href='#' title='".$label."'><i class='fa ".$icon." fa-fw'></i> <span class='menuTitles'".@$escondeMenuCss.">".$label."<span class='fa arrow'></span></span></a>
			<ul class='nav ".$class_drop."'>
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
		if (!$this->mostraMenuEsquerda)
			$escondeMenuCss = " style='margin-left:55px;'";
		
		echo "
		
			<body>
			<div id='wrapper'>
				<!-- Navigation -->
				".$menu."
				<div id='page-wrapper'".@$escondeMenuCss.">
					
		
		";
	
	}
	
	function bodyEndBlank($jquery=''){
		
		echo "
			".$this->jsLibs()."
			".$this->jQueryReady($jquery)."
		</body>
		</html>
		
		";
	
	}
	
	function bodyEnd($jquery='')
	{
		echo "
		
					
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
	
	function jQueryReady($jquery)
	{
		echo "
		<script>
		$( document ).ready(function() {
			
			$('#idBtnSearch').click(function()
			{
				$('#formSearch').submit();
			});
			
			
			$('#escondeMenu').click(function()
			{
				if ($('#idMenuLateral').hasClass('lf_sidebar_closed'))
				{
					$('#idMenuLateral').removeClass('lf_sidebar_closed');
					$('.sidebar-search').show();
					$('.menuTitles').show();
					$('#page-wrapper').css('margin-left','250px');
				}
				else
				{
					$('#idMenuLateral').addClass('lf_sidebar_closed');
					$('.sidebar-search').hide();
					$('.menuTitles').hide();
					$('#page-wrapper').css('marginLeft','55px');
				}
			});
			
			
			$('#crudForm').validator();
			
			$('#searchInput').click(function() {
				$(this).val('');
			});
			
			$('.select2').select2();
			$('.datepicker').datepicker({
				format: 'dd/mm/yyyy',
				language: 'pt-BR',
				
				pickerPosition: 'top-left',
				autoclose: true
			});
		
			".@$jquery."
			
		});
		</script>
		";
	}
}


?>