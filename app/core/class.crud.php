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
require_once("class.password.php");
require_once("class.help.php");

Class CrudBootstrap{

	public $formconfig;
	
	public $campos;
	public $filtroWhere;
	public $historyFiltro;
	
	public $config;
	
	public $bdconn;
	
	public $paginar;
	public $paginarMax;
	public $paginarQuery;
	public $paginarPaginas;
	public $paginarStart;
	public $paginarParametros;
	public $paginarTotal;

	public function CrudBootstrap($formconfig = ''){
	
		$this->config        = new Config();
		$this->core          = new Core(false);
		$this->help          = new Help();
	
		if ($formconfig != '')
		{
			$this->form_action   = $formconfig['form_action'];
			$this->form_dbtable  = $formconfig['form_dbtable'];
			
			//Para os casos de nao informar a class, assumir o nome do dbtable
			if (@$formconfig['form_class'] != '')
				$this->form_class    = $formconfig['form_class'];
			else
				$this->form_class    = $formconfig['form_dbtable'];
				
			$this->form_title    = $formconfig['form_title'];
			
			$this->campos        = $formconfig['campos'];
			
			if (isset($formconfig['paginar']))
			{
				//echo "PAGINAR NAO DEFINIDO";
				$this->paginar       = $formconfig['paginar'];
			}
			else
			{
				//echo "PAGINAR DEFINIDO";
				$this->paginar       = true;
			}	
				
		}
	
		$this->bdconn = new Conexao();
		
	}
	
	public function post($debug = false)
	{
		if ($_POST)
		{
			if ($this->campos)
			{
				foreach ($this->campos as $campo => $v)
				{
					//se o campo estiver no array de campos como hidden é sinal de campo custom, como controle de datas e etc
					//Entao, verificamos se este campo está em branco, se tiver, nem envia pro post.
					//Exemplo de campos de data de criação, só vai fazer insert do valor na hora da criação e não mais na edição da linha.
					if ($v['type'] != 'hidden')
					{
						if ((!is_array(@$_POST[$v['name']])) && (@$v['type'] != 'selectRel'))
						//if (!is_array(@$_POST[$v['name']]))
						{
							if (@$v['post_bd_mask'] != '')
							{
								$post_bd_mask = $this->help->fixDate($v['post_bd_mask'],$_POST[$v['name']]);
								$post[$v['name']] = $post_bd_mask;
							}
							else
								$post[$v['name']] = @$_POST[$v['name']];
						}
						else
						{
							//se for um array, é um sinal que é de um campo multiplo, enviar as informacoes no $post compiladas
							$post[$v['name']] = array(
								'tableRel'=>$v['tableRel'],
								'idPai'=>$v['idPai'],
								'idFilhos'=>$v['idFilhos'],
								'values'=>@$_POST[$v['name']]
								);
						}
					}
					else
					{ 
						if (@$_POST[$v['name']] != '')
							$post[$v['name']] = @$_POST[$v['name']];
					}
						
				}
			}
			
			if ($debug)
			{
				print_r($_POST);
				echo "<hr/>";
				print_r($post);
				die();
			}
			
			if (@$_POST['crud'] == 'edit')
			{
				$this->editCRUD($_POST['id'],$post);
				//echo "<p class='banner-information'>Editado com sucesso!</p>";
				echo "<div class='alert alert-success' style='text-align: center;'>Editado com sucesso!</div>";
			}
			else if (@$_POST['crud'] == 'add')
			{
				$this->insertCRUD($post);
				//echo "<p class='banner-information'>Criado com sucesso!</p>";
				echo "<div class='alert alert-success' style='text-align: center;'>Criado com sucesso!</div>";
			}
			else if (@$_POST['crud'] == 'login')
			{
				$this->loginCRUD($_POST['usuario'],$_POST['senha']);
				//echo "<p class='banner-information'>Criado com sucesso!</p>";
			}
			else if (@$_POST['crud'] == 'filtro')
			{
				//print_r($_POST);
				//die();
				//echo "LISTA FILRO";
				//die();
				$w;
				foreach ($post as $p => $v)
				{
					if ($v <> '')
					{
						if (!is_array($v))
						{
							if (substr($p,0,2) == 'id')
								$w[] = " ".$p." = '".$v."' ";
							else
								$w[] = " ".$p." LIKE '%".$v."%' ";
						}
						else
						{
							
							$rel_join = @implode(',',$v['values']);
							if ($rel_join != '')
							{
								$relacionamentos = " SELECT ".$v['idPai']." FROM ".$v['tableRel']." WHERE ".$v['idFilhos']." IN (".$rel_join.") ";
								//echo $relacionamentos;
								$res = $this->bdconn->select($relacionamentos);
								
								//existe relacionamentos
								if ($res)
								{
									//print_r($res);
									
									
									//o history dos componentes ainda nao funcionam
									
									$rels = implode(',',$res[0]);
									//agrega os IDs do pai na consulta principal
									$w[] = " id IN (".$rels.") ";
								}
								else
								{
									//se não tiver nenhum dos relcionamentos, faz add um where fake para nao trazer nenhum.
									$w[] = " id IN (-1) ";
								
								}
							}
						}
					}
				}
				if (sizeof(@$w) > 0)
				{
					$this->historyFiltro = $post;
					$where = implode(' AND ',$w);
					
					$this->setListWhere($where);
					
					$_SESSION['search'][$this->form_dbtable]['where'] = $where;
					$_SESSION['search'][$this->form_dbtable]['history'] = $this->historyFiltro;
					
				}
				else
				{
					//se o filtro for em branco, limpar a pesquisa via SESSION
					if (@$_SESSION['search'][$this->form_dbtable])
					{
						unset($_SESSION['search'][$this->form_dbtable]['where']);
						unset($_SESSION['search'][$this->form_dbtable]['history']);
					}
				}
			}
			
		}
		else if (@$_SESSION['search'][$this->form_dbtable])
		{
			//echo "FIZ FILTRO";
			//print_r($_SESSION);
			//die();
			//para o filtro na paginacao
			$this->historyFiltro = $_SESSION['search'][$this->form_dbtable]['history'];
			$this->setListWhere($_SESSION['search'][$this->form_dbtable]['where']);
		}
	}
	
	public function criaFormLogin()
	{
		
		echo "<div class='container'>
				<div class='row'>
					<div class='col-md-4 col-md-offset-4'>
						<div class='login-panel panel panel-default'>
							<div class='panel-heading'>
								<h3 class='panel-title'>Login</h3>
							</div>
							<div class='panel-body'>
								<form role='form' method='post' action=''>
									<fieldset>
										<input type='hidden' value='login' name='crud'/>
										<div class='form-group'>
											<input class='form-control' placeholder='E-mail' name='usuario' type='text' autofocus autocomplete='off'>
										</div>
										<div class='form-group'>
											<input class='form-control' placeholder='Password' name='senha' type='password' autocomplete='off'>
										</div>
										<!-- <div class='checkbox'>
											<label>
												<input name='remember' type='checkbox' value='Remember Me'>Remember Me
											</label>
										</div>-->
										
										<button type='submit' class='btn btn-success'>Acessar</button>
										<!--Change this to a button or input when using this as a form 
										<a href='index.html' class='btn btn-lg btn-success btn-block'>Login</a>-->
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>";
		/*
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
		echo "</div>";*/
	}
	
	public function criaFormEdit($id = 0)
	{
		
		
		if ($id > 0){
			$crud = $this->getCRUDInfo($this->form_dbtable,$id);
			if ($crud)
			{
				echo "<div class='col-lg-12'>";
					echo "<form action='../../' role='form' id='crudForm' data-toggle='validator' method='post' class='navbar-left' style='width: 100%;'>";

						echo "<input type='hidden' value='edit' name='crud'/>";
						echo "<input type='hidden' value='".$id."' name='id'/>";
						
						//echo "<table class='table'>";
							foreach ($this->campos as $campo)
							{ 
								
								
								if ($campo['type'] == 'password'){
									$e = new Encryption();
									$value_text = $e->decode($crud[$campo['name']]);
								}else{
									$value_text = @$crud[$campo['name']];
								}
								
								if ($campo['type']=='selectRel')
								{
									/*
									'tableRel'=>'relComponentesKeyfun',
									'idPai'=>'idKeyfun',
									'options'=>'componentes',
									'idFilhos'=>'idComponente',
									*/
									$reltemp = $this->getColumnCRUDInfoMulti($campo['idFilhos'],$campo['tableRel'],$campo['idPai'],$id);
									//print_r($reltemp);
									//foreach($reltemp as $r)
									//{
									//	$value_text[] = $r[$campo['idFilhos']];
									//}
									$value_text = $reltemp;
									
								}
								//print_r($reltemp[0]);
								if ($campo['type']!='hidden')
								{
									/*
									<div class="form-group has-error">
										<label class="control-label" for="inputError">Input with error</label>
										<input type="text" class="form-control" id="inputError">
									</div>
									*/
								
									/*
									echo "<tr>";
										echo "<td><label for='sel1'>".$campo['label'].":</label></td>";
										echo "<td>".$this->formGeraElemento($campo,$value_text)."</td>";
										//echo "<td><input type='".$campo[2]."' name='".$campo[1]."' class='form-control' value='".$value_text."' style='width:".$campo[3]."px'/></td>";
									echo "</tr>";
									*/
									if (@$campo['required'] && ($value_text == ''))
										$classreq = ' has-error';
									else
										$classreq = '';
									
									//echo "<tr><td>";
										echo "<div class='form-group".$classreq."' style='margin-bottom: 0px;'>";
											echo "<label class='control-label' for='id".$campo['name']."'>".$campo['label']."</label>";
											echo $this->formGeraElemento($campo,$value_text);
											//echo "<td><input type='".$campo[2]."' name='".$campo[1]."' class='form-control' value='".$value_text."' style='width:".$campo[3]."px'/></td>";
										echo "</div>";
									//echo "</td></tr>";
								}
								else
								{
									$hiddens[] = $this->formGeraElemento($campo,'');
								}
							}
							
							//echo "<tr style='text-align: center;'><td>";
								echo "<div class='form-group' style='margin-top: 10px;'>";
									echo "<button type='button' onclick=\"location.href='../../';\"  class='btn btn-default'>Cancelar</button> ";
									echo "<button type='submit' class='btn btn-success'>Salvar</button>";
								echo "</div>";
							//echo "</td></tr>";
						//echo "</table>";
						
						if (@$hiddens)
							foreach($hiddens as $h)
								echo $h;
								
					echo "</form>";
				echo "</div>";
			}
			else
			{
				echo "ID não encontrado.";
			}
		}else{
			echo "ID não informado.";
		}
	}
	
	public function criaFormAdd()
	{	
			echo "<div class='col-lg-12'>";
				echo "<h3>Adicionando ".$this->form_title."</h3>";
			
				echo "<form action='../' role='form' id='crudForm' data-toggle='validator' method='post' class='navbar-left' style='margin-left:auto;margin-right:auto;width: 100%;'>";
					echo "<input type='hidden' value='add' name='crud'/>";
					//echo "<table class='table'>";
						foreach ($this->campos as $campo)
						{
							if ($campo['type']!='hidden')
							{
								/*
								echo "<tr>";
									echo "<td>".$campo['label'].":</td>";
									echo "<td>".$this->formGeraElemento($campo,'')."</td>";
								echo "</tr>";*/
								if (@$campo['required'])
									$classreq = ' has-error';
								else
									$classreq = '';
								
								
								if (@$campo['default']!='')
									$value=$campo['default'];
								else
									$value='';
								
								echo "<div class='form-group".$classreq."' style='margin-bottom: 0px;'>";
									echo "<label class='control-label' for='id".$campo['name']."'>".$campo['label']."</label>";
									echo $this->formGeraElemento($campo,$value,true);
								echo "</div>";
							}
							else
							{
								$hiddens[] = $this->formGeraElemento($campo,'');
							}
							
						}
						
						/*echo "<tr style='text-align: center;'>";
							echo "<td colspan='2'><button type='button' onclick=\"location.href='../';\"  class='btn btn-default'>Cancelar</button> <button type='submit' class='btn btn-default'>Adicionar</button></td>";
						echo "</tr>";*/
						
						echo "<div class='form-group' style='margin-top: 10px;'>";
							echo "<button type='button' onclick=\"location.href='../';\"  class='btn btn-default'>Cancelar</button> ";
							echo "<button type='submit' class='btn btn-success'>Salvar</button>";
						echo "</div>";
					//echo "</table>";
					
					if (@$hiddens)
						foreach($hiddens as $h)
							echo $h;
							
				echo "</form>";
			echo "</div>";
	}
	
	public function criaView($id)
	{
	
		$path = $this->core->system_path;
		$obj = $this->getById($id);
		
		echo "<div class='row'>";
			$link_edit = $path."/".$this->form_class."/edit/".$obj['id'];
			echo "<a href='".$link_edit."'><span class='label label-default' title='Editar'>Editar</span></a> ";
			echo "<a href='../'><span class='label label-default' title='Voltar para lista'>Ver lista</span></a> ";

			//Se tiver um campo com NOME, escreve grande na tela
			//if (@$obj['nome'] != '')
			//	echo "<h2>".$obj['nome']."</h2>";
			echo "<br/><br/>";
			
			foreach ($this->campos as $campo)
			{
				if ($campo['type'] == 'selectRel')
				{
					if (@$campo['selectLabel']!='')
						$selectLabel = $campo['selectLabel'];
					else
						$selectLabel = "nome";
						
					$relNames = $this->formGetSelectContent($campo['options'],$selectLabel);
					$reltemp = $this->getColumnCRUDInfoMulti($campo['idFilhos'],$campo['tableRel'],$campo['idPai'],$id);
					
					echo "<p><b>".$campo['label'].":</b></p>";
					foreach($relNames as $r)
					{
						foreach ($reltemp as $tmp)
						{
							if (@$r['id'] == $tmp)
								$ret[] = "<a href='".$path."/".$campo['options']."/".@$r['id']."'>".@$r['nome']."</a>";
						}
					}
					$value_text = implode(',',$ret);
					echo "<p>".$value_text."</p>";
					
				}
				else if ($campo['type'] == 'select')
				{
					if (@$campo['selectLabel']!='')
						$selectLabel = $campo['selectLabel'];
					else
						$selectLabel = "nome";
						
					$relNames = $this->formGetSelectContent($campo['options'],$selectLabel);
					
					//print_r($relNames);
					//echo "<p><b>".$campo['label'].":</b> ".$id." </p>";
					
					foreach($relNames as $r)
					{
						if (@$r['id'] == $obj[$campo['name']])
							echo "<p><b>".$campo['label'].":</b> <a href='".$path."/".$campo['options']."/".@$r['id']."'>".@$r['nome']."</a></p>";
					}
					//print_r($relNames);
					//$reltemp = $this->getColumnCRUDInfoMulti($campo['idFilhos'],$campo['tableRel'],$campo['idPai'],$id);
					
					//foreach($reltemp as $r)
					//{
						//$ret = "<a href='".$path."/".$campo['options']."/".@$relNames[$r]['id']."'>".@$relNames[$r]['nome']."</a>";
					//}
					//$value_text = implode(',',$ret);
					//echo "<p>".$ret."</p>";
					
				}
				else				
					echo "<p><b>".$campo['label'].":</b> ".$obj[$campo['name']]."</p>";
			}
			
		echo "</div>";
	
	}
	
	public function getById($id = '')
	{
		return $this->getCRUDInfo($this->form_dbtable,$id);
	}
	
	public function criaFormList($colunas = array('Nome'=>'nome'))
	{
		$res = $this->getList("*");
		$this->botaoCriar();
		
		$path = $this->core->system_path;
		
		
		if ($res)
		{
			if ($this->paginarTotal['COUNT(id)'] > 0)
				echo "<p style='clear: both;'> Mostrando ".sizeof($res)." registros de ".$this->paginarTotal['COUNT(id)'].".</p>";
			else
				echo "<p style='clear: both;'> ".sizeof($res)." registros.</p>";
			
			echo "<table class='table'>";
				echo "<tr style='color:#000; background:#fff; text-align:left;'>";
					echo "<th>&nbsp;</td>";
					foreach ($colunas as $c => $v)
					{
						echo "<th style='color:#000;'>".$c."</td>";
					}
				echo "</tr>";
				foreach ($res as $v)
				{
					$link_edit = $path."/".$this->form_class."/edit/".$v['id'];
					$link_view = $path."/".$this->form_class."/".$v['id'];
					
					$btn = "<a href='".$link_edit."'><span class='badge' style='background-color:#cfcfcf' title='Editar'>
						<span class='fa fa-edit' aria-hidden='true'></span> </span></a>";
					$btnView = "<a href='".$link_view."'><span class='badge' style='background-color:#cfcfcf' title='Ver'>
						<span class='fa fa-eye' aria-hidden='true'></span> </span></a>";
					
					
				
					echo "<tr style='color:#000; background:#fff; text-align:left;'>";
						echo "<td style='color:#000;'>".$btn." ".$btnView."</td>";
						foreach ($colunas as $cr => $vr)
						{
							if (is_array($vr))
							{
								if (isset($vr['relTable']))
								{
									$rels_table = array();
									$rels = $this->getColumnCRUDInfoMulti($vr['field'],$vr['relTable'],$vr['fieldPai'],$v['id']);
									
									if (sizeof($rels) > 0)
									{
										foreach ($rels as $r)
										{
										
											//se vier com valor no relURL, é sinal que a classe é outra, e não a mesma do table
											if (@$vr['relURL'] != '')
												$relURL = $vr['relURL'];
											else
												$relURL = $vr['table'];
											
											//se vier FALSE é pq não devemos ter link neste CRUD
											if (@$vr['linkURL'])
												$relURL = "";
											
											$rels_table[] = $this->getColumnCRUDInfo($vr['return'],$vr['table'],$r,$relURL);
										}
										$rtb = implode(",",$rels_table);
									}
									else
									{
										$rtb = '';
									}
									
									echo "<td style='color:#000;'>".$rtb."</td>";
									
								}
								else if (is_array(@$vr['options']))
								{
									/*
									//se vier com valor no relURL, é sinal que a classe é outra, e não a mesma do table
									if (@$vr['relURL'] != '')
										$relURL = $vr['relURL'];
									else
										$relURL = $vr['table'];
									
									//se vier FALSE é pq não devemos ter link neste CRUD
									if (@$vr['linkURL'])
										$relURL = "";
									
									if ($relURL =! '')
										echo "<a href='".$path."/".$relURL."/".$id."'>".$vr['options'][$v[$vr['field']]]."</a>";
									else*/
										echo "<td style='color:#000;'>".$vr['options'][$v[$vr['field']]]."</td>";
								}
								else
								{
									//se vier com valor no relURL, é sinal que a classe é outra, e não a mesma do table
									if (@$vr['relURL'] != '')
										$relURL = $vr['relURL'];
									else
										$relURL = $vr['table'];
									
									//se vier FALSE é pq não devemos ter link neste CRUD
									if (isset($vr['linkURL']))
										if (!$vr['linkURL'])
											$relURL = "";	
												
									echo "<td style='color:#000;'>".$this->getColumnCRUDInfo($vr['return'],$vr['table'],$v[$vr['field']],$relURL)."</td>";
								}
							}
							else
								echo "<td style='color:#000;'>".$v[$vr]."</td>";
						}
					echo "</tr>";
				}
			echo "</table>";
			
			if ($this->paginar)
				$this->mostraPaginacao();
		}
		else
		{
			echo "<p style='clear: both;'> Nenhum registro de ".$this->form_title." encontrado.</p>";
		}
	}
	
	public function startPaginar()
	{
		$this->paginarMax = $this->config->config['paginarMax'];
		//$this->paginar = true;
		//$this->paginarStart = $start;
		
		$this->paginarTotal = $this->getListTotal();
		//print_r($total);
		//die("total: ");
		// Qunatas paginas devem aparecer
		$this->paginarPaginas = ceil($this->paginarTotal['COUNT(id)'] / $this->paginarMax);
		
		
		$this->paginarParametros = $this->core->cmd;
		//print_r($parametros);
		//2 = p
		//3 = numero da pagina
		
		if (@$this->paginarParametros[2] == 'p')
		{
			if (@$this->paginarParametros[3] > 0)
				$this->paginarStart = ($this->paginarParametros[3] - 1) * $this->paginarMax;
			else
				$this->paginarStart = 0;
		}
		else
		{
			$this->paginarStart = 0;
		}
		
		// verifica a PAGINA atual(se for enviada) 
		//if (@$_GET['p'])
		//	$this->paginarStart = (@$_GET['p'] - 1) * $this->paginarMax;
		//else 
		//	$this->paginarStart = 1;
		
		$this->paginarQuery = " LIMIT ".$this->paginarStart.", ".$this->paginarMax;
	}
	
	public function mostraPaginacao()
	{
		$path = $this->core->system_path;
	
		//echo "<span class='label label-default' style='cursor:pointer;' ><a href='/listar/p/".$i."' style='color:#fff;'>".$i."</a></span> ";
		//if ($this->paginarStart == 0)
		//	$this->paginarStart = 1;
			
		for($i=1;$i<=$this->paginarPaginas;$i++)
		{
			//foi enviado alguma pagina na URL?
			if (@$this->paginarParametros[3] != '')
			{
				if (@$this->paginarParametros[3] == $i)
					echo "<span class='label' style='color:#aaa; border: 1px solid #ccc' title='Pagina de resultado atual'>".$i."</span> ";
				else
					echo "<span class='label label-default' title='Ir para pagina de resultado ".$i."'><a href='".$path."/".$this->form_class."/listar/p/".$i."' style='color:#fff;'>".$i."</a></span> ";
			}
			else
			{//nada na URL
				if (($this->paginarStart == 0) && ($i == 1))
					echo "<span class='label' style='color:#aaa; border: 1px solid #ccc' title='Pagina de resultado atual'>".$i."</span> ";
				else
					echo "<span class='label label-default' title='Ir para pagina de resultado ".$i."'><a href='".$path."/".$this->form_class."/listar/p/".$i."' style='color:#fff;'>".$i."</a></span> ";
			}
		}
		//echo "| >";
	}
	
	public function setListWhere($where = '')
	{
		$this->filtroWhere = $where;
	}
	
	public function getListTotal()
	{
		if ($this->filtroWhere == '')
			$sql = "SELECT COUNT(id) FROM ".$this->form_dbtable.";";
		else
			$sql = "SELECT COUNT(id) FROM ".$this->form_dbtable." WHERE ".$this->filtroWhere.";";
			
		//echo $sql; 
		
		$res = $this->bdconn->select($sql);
		
		return $res[0];
	
	}
	
	public function getList($campos)
	{
		if ($this->paginar)
			$this->startPaginar();
	
		if ($campos != '*')
			$campos = 'id,'.$campos;
	
		if ($this->filtroWhere == '')
			$sql = "SELECT ".$campos." FROM ".$this->form_dbtable." ";
		else
			$sql = "SELECT ".$campos." FROM ".$this->form_dbtable." WHERE ".$this->filtroWhere." ";
			
		if ($this->paginar)
			$sql = $sql.$this->paginarQuery;
		
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
		$path = $this->core->system_path;
		
		//para o botao filtro ficar verde quando tiver filtro ativo
		if (@$this->historyFiltro != '')
			$corFiltro = 'success';
		else
			$corFiltro = 'default';
		
		echo "<div id='divBotoes' style='clear:both;'>";
			echo "<span class='label label-default' style='cursor:pointer;' title='Criar novo'><a href='".$path."/".$this->form_class."/add/' style='color:#fff;'>Adicionar</a></span>";
			echo " <span class='label label-".$corFiltro."' style='cursor:pointer;' title='Mostrar filtro'><a href='#' id='btnFiltro' style='color:#fff;'>Filtro</a></span>";
		echo "</div>";
	}
	
	public function criaFiltro()
	{
		$path = $this->core->system_path;
		
		echo "<div id='divFiltro' style='display:none;'>";
			echo "<form action='".$path."/".$this->form_class."/listar/' method='post' class='navbar-form navbar-left' id='filterForm'>";
				echo "<input type='hidden' value='filtro' name='crud'/>";
				echo "<table class='table'>";
				
					//print_r($this->historyFiltro);
						foreach ($this->campos as $campo)
						{
							if ($campo['type']!='hidden')
							{
								
								//print_r($this->historyFiltro[$campo['name']]);
								
								echo "<tr>";
									echo "<td style='text-align:right;' title='".$campo['type']."'>".$campo['label'].":</td>";
									
									if ($campo['type'] != 'selectRel')
									{
										//ajusta o filtro caso não venha nada, colocar o padrão (se setado)
										if (($this->historyFiltro[$campo['name']] == '') && (@$campo['default']!=''))
											$value=$campo['default'];
										else
											$value=$this->historyFiltro[$campo['name']];
										
										echo "<td>".$this->formGeraElemento($campo,$value,true,true)."</td>";
									}
									else
										echo "<td>".$this->formGeraElemento($campo,$this->historyFiltro[$campo['name']]['values'],false,true)."</td>";
										
								echo "</tr>";
							
							}
							else
							{
								
								//$hiddens[] = $this->formGeraElemento($campo,$this->historyFiltro[$campo['name']],true);
								
							}
							
						}
						
					
					echo "<tr>";
							echo "<td colspan=2><input class='btn btn-info' type='button' id='btnFiltroClear' value='Limpar'/> <input class='btn btn-success' type='submit' value='Filtrar'/></td>";
					echo "</tr>";
				echo "</table>";
				
				//foreach(@$hiddens as $h)
				//	echo $h;
					
			echo "</form>";
		echo "</div>";
	}
	
	public function formGetSelectContent($tbl,$label='nome')
	{
		//validar para ver se não veio uma query MySQL ao invez do nome da tabela ('show procedure status')
		//verifico se não tem um ESPACO na string tbl, se tiver, sinal que não é uma tabela
		
		if (preg_match('/\s/',$tbl))
		{
			$sql = $tbl;
			$res = $this->bdconn->select($sql);
			foreach ($res as $r)
			{
				$ret[] = array('id'=>$r['Name'],'nome'=>$r['Name']);
			}
			return $ret;
		}
		else
		{
			$sql = "SELECT id,".$label." FROM ".$tbl;
			$res = $this->bdconn->select($sql);
			return $res;
		}
		
		
		
	}
	
	public function formGeraElemento($campo,$value,$primeiroBranco=false,$ignoraRequired=false)
	{
		//print_r($value);
		
		//form validation
		$selectLabel = 'nome';
		
		
		if (@$campo['required'] && !$ignoraRequired)
			$req = 'required';
		else
			$req = '';
	
		if (@$campo['size'] > 0)
			$size = $campo['size']."px";
		else
			$size = "100%";
			
		if (($campo['type'] == 'text') || ($campo['type'] == 'password'))
		{
			//verifica se é necessário tratamento de data 
			if ((@$campo['dt_tela_mask'] != '') && ($value!=''))
			{
				//echo ">>".$campo['dt_tela_mask']."||".$value."<<";
				$value = $this->help->fixDate($campo['dt_tela_mask'],$value);
			}
			
			return "<input type='".$campo['type']."' id='id".$campo['name']."' name='".$campo['name']."' value='".$value."' class='form-control ".@$campo['class']."' style='width:".$size."' autocomplete='off' ".$req."/>";
		}
		else if ($campo['type'] == 'hidden')
		{
			return "<input type='hidden' name='".$campo['name']."' value='".$value."' autocomplete='off'/>";
		}
		else if($campo['type'] == 'textarea')
		{
			return "<textarea name='".$campo['name']."' class='form-control ".@$campo['class']."' style='width:".$size."' ".$req."/>".$value."</textarea>";
		}
		else if($campo['type'] == 'select')
		{
			if (@$campo['selectLabel']!='')
				$selectLabel = $campo['selectLabel'];
		
			if (!is_array($campo['options']))
				$sel = $this->formGetSelectContent($campo['options'],$selectLabel);
			else
				$sel = $campo['options'];
				
				
			$return_sel = "";
			
			$return_sel .= "<select name='".$campo['name']."' style='width:".$size."' class='form-control ".@$campo['class']."' ".$req.">";
			
			if ($primeiroBranco)
				$return_sel .= "<option value='' selected>&nbsp;</option>";
				
			if ($sel)
			{
				foreach ($sel as $v => $s)
				{
					//print_r($s);
					if (!is_array($campo['options'])){
						if ($value == $s['id'])
							$return_sel .= "<option value='".$s['id']."' selected>".$s[$selectLabel]."</option>";
						else
							$return_sel .= "<option value='".$s['id']."'>".$s[$selectLabel]."</option>";
					}else{
						if ($value == $v)
							$return_sel .= "<option value='".$v."' selected>".$s."</option>";
						else
							$return_sel .= "<option value='".$v."'>".$s."</option>";
					}
				
				}
			}
			$return_sel .= "</select>";
			
			return $return_sel;
		}
		else if($campo['type'] == 'selectRel')
		{
			//print_r($value);
			//echo "<hr/>DEBUG:";
			//echo "<p>values: [".$value."]";
			//print_r($value);
			//echo "<hr/>";
			
			if (@$campo['selectLabel']!='')
				$selectLabel = $campo['selectLabel'];
			
			if (!is_array($campo['options']))
				$sel = $this->formGetSelectContent($campo['options'],$selectLabel);
			else
				$sel = $campo['options'];
				
				
			$return_sel = "";
			
			$return_sel .= "<select name='".$campo['name']."[]' style='width:".$size."' class='form-control ".@$campo['class']." select2' multiple='multiple' ".$req.">";
			
			//if (($primeiroBranco) && ($value == ''))
			//	$return_sel .= "<option value='' selected>&nbsp;</option>";
			
			$options_rel = array();
			
			if ($sel)
			{
				foreach ($sel as $v => $s)
				{
					//print_r($value);
					if (!is_array($campo['options']))
					{
						//relacionamentos que vem do BD de outra tabela
						if (is_array($value))
						{
							/*
							foreach($value as $rv)
							{
							
								if ($rv[$campo['idFilhos']] == $s['id'])
									$return_sel .= "<option value='".$s['id']."' selected>".$s['nome']."</option>";
								else
									$return_sel .= "<option value='".$s['id']."'>".$s['nome']."</option>";
							}
							*/
							//print_r($value);
							
							if (in_array($s['id'], $value))
								$return_sel .= "<option value='".$s['id']."' selected>".$s[$selectLabel]."</option>";
							else
								$return_sel .= "<option value='".$s['id']."'>".$s[$selectLabel]."</option>";
							
							//$return_sel
						}
						else
						{
							if ($value == $s['id'])
								$return_sel .= "<option value='".$s['id']."' selected>".$s[$selectLabel]."</option>";
							else
								$return_sel .= "<option value='".$s['id']."'>".$s[$selectLabel]."</option>";
						}
					}
					else
					{
						if ($value == $v)
							$return_sel .= "<option value='".$v."' selected>".$s."</option>";
						else
							$return_sel .= "<option value='".$v."'>".$s."</option>";
					}
				
				}
			}
			$return_sel .= "</select>";
			
			return $return_sel;
		}
		
	
	}
	
	public function editCRUD($id,$post)
	{
		$sql = "UPDATE ".$this->form_dbtable." SET ";
		$contem_relacionamentos = false;
		$rel_inserts = array();
		$rel_delete = array();
		
		foreach ($post as $p => $v){
			if (($p == 'password')||($p == 'senha'))
			{
				$e = new Encryption();
				$encoded = $e->encode($v);
				
				$sql .= $p."='".$encoded."',";
			}
			else
			{
				//if ((is_array($v)) || (@$v['type'] == 'selectRel'))
				if (is_array($v))
				{//se for um array, é um sinal que é de um campo multiplo, que precisa de uma tabela de relacionamento
					$contem_relacionamentos = true;
					
					$rel_delete[] = "DELETE FROM ".$v['tableRel']." WHERE ".$v['idPai']." = ".$id.";";
					
					if (sizeof($v['values']) > 0)
					{
						foreach ($v['values'] as $i)
						{
							$rel_inserts[] = "INSERT INTO ".$v['tableRel']." (".$v['idFilhos'].",".$v['idPai'].") VALUES (".$i.",";
						}
					}
				}
				else
				{
					$sql .= $p."='".addslashes($v)."',";
				}
			}
			
		
		}
		$sql = rtrim($sql,",");
		$sql .=  " WHERE id=".$id.";";

		//echo $sql;
		//die();
		$this->bdconn->executa($sql);
		
		if ($contem_relacionamentos)
		{
			//deletar todos os relacionamentos primeiro
			//echo $rel_delete;
			//print_r($rel_inserts);
			foreach($rel_delete as $del)
			{
				$this->bdconn->executa($del);
			}
			
			
			foreach ($rel_inserts as $ins)
			{
				$this->bdconn->executa($ins.$id.")");
			}
		}

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
		$contem_relacionamentos = false;
		$rel_inserts = array();
		//print_r($post);
		//die();
		
		
		foreach ($post as $p => $v)
		{
			//print_r($p);
			//echo "<hr/>";
			if (($p == 'password')||($p == 'senha')){
				//$converter = new Encryption;
				//$encoded = $converter->encode($v);
				$e = new Encryption();
				$encoded = $e->encode($v);
				
				$colunas .= $p.",";
				$valores .= "'".$encoded."',";
			}
			else
			{
			
				//if ((is_array($v)) || (@$v['type'] == 'selectRel'))
				if (is_array($v))
				{
					//se for um array, é um sinal que é de um campo multiplo, que precisa de uma tabela de relacionamento
					
					
					if (sizeof($v['values']) > 0)
					{
						$contem_relacionamentos = true;
						
						foreach ($v['values'] as $i)
						{
							$rel_inserts[] = "INSERT INTO ".$v['tableRel']." (".$v['idFilhos'].",".$v['idPai'].") VALUES (".$i.",";
						}
					}
					
				}
				else
				{
					$colunas .= $p.",";
					$valores .= "'".addslashes($v)."',";
				}
			}
			
		}	
		
		$colunas = rtrim($colunas,",");
		$valores = rtrim($valores,",");

		$sql = "INSERT INTO ".$this->form_dbtable." (".$colunas.") VALUES (".$valores.");";
		
		//echo $sql;
		//die();
		
		$last_id = $this->bdconn->insert($sql);
		//pega o retorno do INSERT
		//$last_id = 0;
		
		//print_r($sql);
		//print_r($rel_inserts);
		//print_r($last_id);
		//die();
		
		if ($contem_relacionamentos)
		{
			foreach ($rel_inserts as $ins)
			{
				$this->bdconn->executa($ins.$last_id.")");
			}
		}
		
	}
	
	public function getColumnCRUDInfoMulti($col,$table,$where,$id)
	{
		$sql = "SELECT ".$col." FROM ".$table." WHERE ".$where." = ".$id.";";
		
		$res = $this->bdconn->select($sql);
		
		//echo $sql;
		//print_r($res);
		$values = array();
		
		if ($res)
		{
			//print_r($res);
			foreach($res as $r)
			{
				$values[] = $r[$col];
			}
			
		}
		return $values;
	}
	
	public function getColumnCRUDInfo($col,$table,$id,$relURL='')
	{
		$path = $this->core->system_path;
		
		$sql = "SELECT ".$col." FROM ".$table." WHERE id = ".$id.";";
		
		if ($id != '')
		{
			$res = $this->bdconn->select($sql);
			if ($relURL != '')
				return "<a href='".$path."/".$relURL."/".$id."'>".$res[0][$col]."</a>";
			else
				return $res[0][$col];
		}
		else
			return '';
	}
	
	public function getCRUDInfo($table,$id)
	{
		$sql = "SELECT * FROM ".$table." WHERE id = ".$id.";";
		
		$res = $this->bdconn->select($sql);
		
		return $res[0];
	}

}

?>