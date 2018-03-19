<?php
//EXEMPLO
/*
$formconfig = array(
	'form_action'=>'produtos.php',
	'form_dbtable'=>'produtos',
	'form_title'=>'Produtos',
	'campos'=>array(
		array("Nome","nome","text",250),
		array("Componente","descricao","textarea",250),
		array("Ambiente","ambiente","select",250,array(1=>"Produção",2=>"Homologação",3=>"Desenvolvimento")),
		array("Produto","produto","select",250,"produtos"),
	)
);

$crud = new CrudBootstrap($formconfig);
$crud->criaFormAdd();
*/

require_once("class.config.php");
require_once("class.ldap.php");

Class CrudBootstrap{

	public $formconfig;
	
	public $campos;
	public $filtroWhere;
	public $historyFiltro;
	
	public $config;
	
	public $bdconn;

	public function CrudBootstrap($formconfig = ''){
	
		$this->config        = new Config();
	
		if ($formconfig != '')
		{
			$this->form_action   = $formconfig['form_action'];
			$this->form_dbtable  = $formconfig['form_dbtable'];
			$this->form_title    = $formconfig['form_title'];
			
			$this->campos        = $formconfig['campos'];
		}
	
		$this->bdconn = new Conexao();
		
	}
	
	public function post(){
		if ($_POST)
		{
			if ($this->campos)
			{
				foreach ($this->campos as $campo => $v){
					$post[$v['name']] = $_POST[$v['name']];
				}
			}
			/*
			print_r($_POST);
			print_r($post);
			die();
			*/
			if (@$_POST['crud'] == 'edit')
			{
				$this->editCRUD($_POST['id'],$post);
				echo "<p class='banner-information'>Editado com sucesso!</p>";
			}
			else if (@$_POST['crud'] == 'add')
			{
				$this->insertCRUD($post);
				echo "<p class='banner-information'>Criado com sucesso!</p>";
			}
			else if (@$_POST['crud'] == 'login')
			{
				$this->loginCRUD($_POST['usuario'],$_POST['senha']);
				//echo "<p class='banner-information'>Criado com sucesso!</p>";
			}
			else if (@$_POST['crud'] == 'filtro')
			{
				$w;
				foreach ($post as $p => $v)
				{
					if ($v <> '')
					{
						if (substr($p,0,2) == 'id')
							$w[] = " ".$p." = '".$v."' ";
						else
							$w[] = " ".$p." LIKE '%".$v."%' ";
						
					}
				}
				if (sizeof(@$w) > 0)
				{
					$this->historyFiltro = $post;
					$where = implode(' AND ',$w);
					
					$this->setListWhere($where);
				}
			}
			
		}
	}
	
	public function criaFormLogin()
	{
		echo "<div class='loginFun'>";
			echo "<h3>Login</h3>";
		
			echo "<form action='' method='post' class='navbar-form navbar-left' style='margin-left:auto;margin-right:auto;'>";
				echo "<input type='hidden' value='login' name='crud'/>";
				echo "<table class='table'>";
					echo "<tr>";
						echo "<td>Usuário:</td>";
						echo "<td><input type='text' name='usuario' value='' class='form-control' style='width:250px' autocomplete='off'/></td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>Senha:</td>";
						echo "<td><input type='password' name='senha' value='' class='form-control' style='width:250px' autocomplete='off'/></td>";
					echo "</tr>";
						
					echo "<tr style='text-align: center;'>";
						echo "<td colspan='2'><button type='submit' class='btn btn-success'>Acessar</button></td>";
					echo "</tr>";
				echo "</table>";
			echo "</form>";
		echo "</div>";
	}
	
	public function criaFormEdit($id = 0)
	{
		
		
		if ($id > 0){
			$crud = $this->getCRUDInfo($this->form_dbtable,$id);
			echo "<form action='../' method='post' class='navbar-form navbar-left'>";

				echo "<input type='hidden' value='edit' name='crud'/>";
				echo "<input type='hidden' value='".$id."' name='id'/>";
				
				echo "<table class='table'>";
					foreach ($this->campos as $campo)
					{ 
						echo "<tr>";
						
						if ($campo['type'] == 'password'){
							$e = new Encryption();
							$value_text = $e->decode($crud[$campo['name']]);
						}else{
							$value_text = $crud[$campo['name']];
						}
					
						echo "<td><label for='sel1'>".$campo['label'].":</label></td>";
						echo "<td>".$this->formGeraElemento($campo,$value_text)."</td>";
						//echo "<td><input type='".$campo[2]."' name='".$campo[1]."' class='form-control' value='".$value_text."' style='width:".$campo[3]."px'/></td>";
						
						echo "</tr>";
					}
					
					echo "<tr style='text-align: center;'>";
						echo "<td colspan='2'><button type='button' onclick=\"location.href='../';\"  class='btn btn-default'>Cancelar</button> <button type='submit' class='btn btn-default'>Salvar</button></td>";
					echo "</tr>";
				echo "</table>";
			echo "</form>";
		}else{
			echo "ID não informado.";
		}
	}
	
	public function criaFormAdd()
	{
		
		echo "<h3>Adicionando ".$this->form_title."</h3>";
		
			echo "<form action='../' method='post' class='navbar-form navbar-left' style='margin-left:auto;margin-right:auto;'>";
				echo "<input type='hidden' value='add' name='crud'/>";
				echo "<table class='table'>";
					foreach ($this->campos as $campo)
					{
						echo "<tr>";
							echo "<td>".$campo['label'].":</td>";
							echo "<td>".$this->formGeraElemento($campo,'')."</td>";
						echo "</tr>";
					}
					echo "<tr style='text-align: center;'>";
						echo "<td colspan='2'><button type='button' onclick=\"location.href='../';\"  class='btn btn-default'>Cancelar</button> <button type='submit' class='btn btn-default'>Adicionar</button></td>";
					echo "</tr>";
				echo "</table>";
			echo "</form>";
		
	}
	
	public function setListWhere($where = '')
	{
		$this->filtroWhere = $where;
	}
	
	public function getList($campos)
	{
		if ($campos != '*')
			$campos = 'id,'.$campos;
	
		if ($this->filtroWhere == '')
			$sql = "SELECT ".$campos." FROM ".$this->form_dbtable.";";
		else
			$sql = "SELECT ".$campos." FROM ".$this->form_dbtable." WHERE ".$this->filtroWhere.";";
			
		
		//echo $sql; 
		
		$res = $this->bdconn->select($sql);
		
		return $res;
	
	}
	
	public function jqueryFiltro()
	{
		//echo " <span class='label label-success' style='cursor:pointer;' title='Mostrar filtro'><a href='#' id='btnFiltro' style='color:#fff;'>Filtro</a></span>";
		return "
		
			$(document).on('click', '#btnFiltro', function()
			{
				event.preventDefault();
				$('#divFiltro').toggle('slow');
			});
			$(document).on('click', '#btnFiltroClear', function()
			{
				//alert('limpando filtro');
				//event.preventDefault();
				$('#filterForm').find('input:text, input:password, input:file, select, textarea').val('');
				$('#filterForm').find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
			});
		
		";
	}
	
	public function botaoCriar()
	{
		echo "<div id='divBotoes' style='clear:both;'>";
			echo "<span class='label label-success' style='cursor:pointer;' title='Criar novo'><a href='add/' style='color:#fff;'>Adicionar</a></span>";
			echo " <span class='label label-success' style='cursor:pointer;' title='Mostrar filtro'><a href='#' id='btnFiltro' style='color:#fff;'>Filtro</a></span>";
		echo "</div>";
	}
	
	public function criaFiltro()
	{
		echo "<div id='divFiltro' style='display:none;'>";
			echo "<form action='' method='post' class='navbar-form navbar-left' id='filterForm'>";
				echo "<input type='hidden' value='filtro' name='crud'/>";
				echo "<table class='table'>";
				
					//print_r($this->historyFiltro);
						foreach ($this->campos as $campo)
						{
							echo "<tr>";
								echo "<td style='text-align:right;'>".$campo['label'].":</td>";
								echo "<td>".$this->formGeraElemento($campo,$this->historyFiltro[$campo['name']],true)."</td>";
							echo "</tr>";
						}
						
					
					echo "<tr>";
							echo "<td colspan=2><input class='btn btn-info' type='button' id='btnFiltroClear' value='Limpar'/> <input class='btn btn-success' type='submit' value='Filtrar'/></td>";
					echo "</tr>";
				echo "</table>";
			echo "</form>";
		echo "</div>";
	}
	
	public function formGetSelectContent($tbl){
		$sql = "SELECT id,nome FROM ".$tbl;
		
		$res = $this->bdconn->select($sql);
		
		return $res;
	}
	
	public function formGeraElemento($campo,$value,$primeiroBranco=false){
		if (($campo['type'] == 'text') || ($campo['type'] == 'password')){
		
			return "<input type='".$campo['type']."' name='".$campo['name']."' value='".$value."' class='form-control' style='width:".$campo['size']."px' autocomplete='off'/>";
		
		}else if($campo['type'] == 'textarea'){
		
			return "<textarea name='".$campo['name']."' class='form-control' style='width:".$campo['size']."px'/>".$value."</textarea>";
		
		}else if($campo['type'] == 'select'){
		
			if (!is_array($campo['options']))
				$sel = $this->formGetSelectContent($campo['options']);
			else
				$sel = $campo['options'];
				
				
			$return_sel = "";
			
			$return_sel .= "<select name='".$campo['name']."' style='width:".@$campo['size']."px' class='form-control'>";
			
			if ($primeiroBranco)
				$return_sel .= "<option value='' selected>&nbsp;</option>";
				
			foreach ($sel as $v => $s){
				//print_r($s);
				if (!is_array($campo['options'])){
					if ($value == $s['id'])
						$return_sel .= "<option value='".$s['id']."' selected>".$s['nome']."</option>";
					else
						$return_sel .= "<option value='".$s['id']."'>".$s['nome']."</option>";
				}else{
					if ($value == $v)
						$return_sel .= "<option value='".$v."' selected>".$s."</option>";
					else
						$return_sel .= "<option value='".$v."'>".$s."</option>";
				}
			
			}
			$return_sel .= "</select>";
			
			return $return_sel;
		}
		
	
	}
	
	public function editCRUD($id,$post){

		$sql = "UPDATE ".$this->form_dbtable." SET ";
		
		foreach ($post as $p => $v){
			if (($p == 'password')||($p == 'senha')){
					
			//$converter = new Encryption;
			//$encoded = $converter->encode($v);
			$e = new Encryption();
			$encoded = $e->encode($v);
				
				$sql .= $p."='".$encoded."',";
			}else{
				$sql .= $p."='".$v."',";
			}
			
		
		}
		$sql = rtrim($sql,",");
		$sql .=  " WHERE id=".$id.";";

		//echo $sql;
		//die();
		$this->bdconn->executa($sql);

	}
		
	public function logoutCRUD()
	{
		$s = new Sessao();
		$s->logout();
	}
	
	public function loginCRUD($usuario,$senha)
	{
		$canLogIn = false;
	
		if ($this->config->config['login_tipo'] == 'bd')
		{
			$sql = "SELECT id FROM ".$this->config->config['login_bd_table']." WHERE ".$this->config->config['login_bd_usuario']." = '".$usuario."' AND  ".$this->config->config['login_bd_senha']." = '".md5($senha)."';";
			$res = $this->bdconn->select($sql);
			
			if (sizeof($res > 0))
			{
				$canLogIn = true;
			}
		}
		else if ($this->config->config['login_tipo'] == 'ldap')
		{
			$ldap = new LDAP($usuario,$senha);
			
			if ($ldap->login())
				$canLogIn = true;
		}
		else
		{
		
			die("ERRO: Sem tipo de login");
		
		}
		
		if ($canLogIn)
		{
			$s = new Sessao();
			$s->login($usuario);
			
		
		}else{
		
			echo "<div>";
				echo "<div class='avisoFun'>";
					echo "Login ou senha incorretos.";
				echo "</div>";
			echo "</div>";
		
		}

		
	}
	
		
	public function insertCRUD($post){
	
		$colunas = "";
		$valores = "";
		
		//print_r($post);
	
		foreach ($post as $p => $v){
			
			if (($p == 'password')||($p == 'senha')){
				//$converter = new Encryption;
				//$encoded = $converter->encode($v);
				$e = new Encryption();
				$encoded = $e->encode($v);
				
				$colunas .= $p.",";
				$valores .= "'".$encoded."',";
			}else{
			
				$colunas .= $p.",";
				$valores .= "'".$v."',";
				
			}
			
		}	
		
		$colunas = rtrim($colunas,",");
		$valores = rtrim($valores,",");

		$sql = "INSERT INTO ".$this->form_dbtable." (".$colunas.") VALUES (".$valores.");";
		
		//echo $sql;
		
		$this->bdconn->executa($sql);
	}
	
	public function getColumnCRUDInfo($col,$table,$id)
	{
		$sql = "SELECT ".$col." FROM ".$table." WHERE id = ".$id.";";
		
		$res = $this->bdconn->select($sql);
		
		//return utf8_encode($res[0][$col]);
		return $res[0][$col];
	}
	
	public function getCRUDInfo($table,$id)
	{
		$sql = "SELECT * FROM ".$table." WHERE id = ".$id.";";
		
		$res = $this->bdconn->select($sql);
		
		return $res[0];
	}

}

?>