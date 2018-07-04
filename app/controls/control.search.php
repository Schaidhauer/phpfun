<?php
require_once("app/core/class.crud.php");

require_once("app/controls/control.debug.php");

Class SearchController{

	public $crud;
	public $objVar;
	public $bdconn;
	public $formconfig;
	
	public $dashProduto;
	public $path;
	
	public function __construct()
	{
		$this->bdconn  = new Conexao();
		
		$this->d       = new DebugController();
		
		$this->path = Core::$system_path;
	}
	
	function conteudo()
	{
		if (@$_GET['s'] != '')
		{
			$content = '';
			
			
			//tabelas comuns
			$content .= $this->montaPainelResposta('produtos','id,nome','nome',"nome LIKE '%".$_GET['s']."%'",'Produtos','produtos');
			$content .= $this->montaPainelResposta('ambientes','id,nome','nome',"nome LIKE '%".$_GET['s']."%'",'Ambientes','ambientes');
			$content .= $this->montaPainelResposta('servidores','id,nome','nome',"nome LIKE '%".$_GET['s']."%'",'Servidores','servidores');
			$content .= $this->montaPainelResposta('componentes','id,nome','nome',"nome LIKE '%".$_GET['s']."%'",'Componentes','componentes');
			$content .= $this->montaPainelResposta('consultas','id,nome','nome',"nome LIKE '%".$_GET['s']."%'",'Consultas','consultas');
			$content .= $this->montaPainelResposta('wiki','id,title','title',"title LIKE '%".$_GET['s']."%'",'Wikis','wiki');
			$content .= $this->montaPainelResposta('files','id,filename','filename',"filename LIKE '%".$_GET['s']."%'",'Arquivos','files');
			$content .= $this->montaPainelResposta('gatilhos_logs','id,texto','texto',"texto LIKE '%".$_GET['s']."%'",'Gatilhos','gatilhos');
			
			
			
			
			//fazer via ajax
			//$content .= $this->montaPainelLogs($_GET['s']);
			$content .= "<div class='col-md-3' id='divSearchLogs'></div>";
			
			echo "<div class='row' style='height: 10px;'></div>";
			echo "<div class='row'>";
				echo $content;
			echo "</div>";
		
		}
		else
		{
			echo "Pesquise por uma palavra.";
		}
		
	}
	
	function getNomeProdutosFromTblConfig($id)
	{
		$sql = "SELECT p.nome as nome FROM produtos p 
		INNER JOIN logs_config lc ON lc.idProdutos = p.id
		WHERE 
		lc.idConfigTbl = ".$id.";";
		$r = $this->bdconn->select($sql);
		return $r[0]['nome'];
	}
	
	function getSearchProdutos($s)
	{
		$sql = "SELECT id,nome FROM produtos WHERE nome LIKE '%".$s."%';";
		return $this->bdconn->select($sql);
	}
	
	function montaPainelLogs($msg){
	
		$sql = "SELECT id,nome FROM logs_config_tbl";
		$tbls = $this->bdconn->select($sql);
		$r="";
		$temlog = false;
		
		foreach($tbls as $tbl)
		{
			
			$sql_logs = "SELECT COUNT(id) FROM ".$tbl['nome']." log WHERE dtInsert > '".date("Y-m-d ")." 00:00:00' AND mensagem LIKE '%".$msg."%' ORDER BY dtlogDateTime DESC LIMIT 0,100";
			$n_logs = $this->bdconn->select($sql_logs);
			$logs[] = array('produto'=>$this->getNomeProdutosFromTblConfig($tbl['id']),'qtd'=>$n_logs[0]['COUNT(id)'],'tblNome'=>$tbl['nome']);
			
		}
			//$r .= "<div class='col-md-3'>";
				$r .= "<div class='panel panel-default'>";
					$r .= "<div class='panel-heading'>";
						$r .= "<div class='row'>";
							$r .= "<div class='col-xs-9'>";
								$r .= "<div>Logs hoje</div>";
							$r .= "</div>";
						$r .= "</div>";
					$r .= "</div>";
					$r .= "<div class='panel-footer'>";
						
						$dthj = urlencode(date("d/m/Y"));
						
						foreach($logs as $ll)
						{
							if ($ll['qtd'] > 0)
							{
								$r .= "<a href='".$this->path."/logs/".$ll['produto']."/?logmsg=".$msg."&dt=".$dthj."' target='_blank'>";
									$r .= "<span class='pull-left' style='word-wrap: break-word;'>".$ll['produto']." (".$ll['qtd'].")</span>";
									$r .= "<span class='pull-right'><i class='fa fa-arrow-circle-right'></i></span>";
									$r .= "<div class='clearfix'></div>";
								$r .= "</a>";
								$temlog = true;
							}
						}
						
					$r .= "</div>";
				$r .= "</div>";
			//$r .= "</div>";
		
		if (!$temlog)
			$r = "";
		
		return $r;
	}
	
	function montaPainelResposta($table,$col='id,nome',$label='nome',$where="nome LIKE '%%'",$titulo,$class)
	{
		$sql = "SELECT ".$col." FROM ".$table." WHERE ".$where.";";
		$resultados = $this->bdconn->select($sql);
		$r="";
		if ($resultados)
		{
			$r .= "<div class='col-md-3'>";
				$r .= "<div class='panel panel-default'>";
					$r .= "<div class='panel-heading'>";
						$r .= "<div class='row'>";
							$r .= "<div class='col-xs-9'>";
								$r .= "<div>".count($resultados)." ".$titulo."</div>";
							$r .= "</div>";
						$r .= "</div>";
					$r .= "</div>";
					$r .= "<div class='panel-footer'>";
						
						foreach($resultados as $res)
						{
							$r .= "<a href='".$this->path."/".$class."/".$res['id']."/' target='_blank'>";
								$r .= "<span class='pull-left' style='word-wrap: break-word;'>".$res[$label]."</span>";
								$r .= "<span class='pull-right'><i class='fa fa-arrow-circle-right'></i></span>";
								$r .= "<div class='clearfix'></div>";
							$r .= "</a>";
						}
						
					$r .= "</div>";
				$r .= "</div>";
			$r .= "</div>";
		}
		
		return $r;
	}
	
	
}

?>