<?php
require_once("app/core/class.crud.php");
require_once("app/core/class.config.php");

Class UsuariosController
{

	public $crud;
	public $objVar;
	public $formconfig;
	
	public function __construct($tipo=1)
	{
		$this->objVar = 'objVarvalue';
		$this->config        = new Config();
		
		$avaliable_permissions = $this->getAvaliablePermissions();
		
		if ($tipo == 2)
		{
			$this->tipoLabel = 'Grupo';
			$this->tipoClass = 'grupos';
			$this->tipoTitulo = 'Grupos';
			$array_rel = array(
					'label'=>'Usuarios',//Usuarios
					'name'=>'relUsuarios',//relUsuarios
					'type'=>'selectRel',
					'selectLabel'=>'usuario',
					'where'=>'tipo=1',
					'tableRel'=>'lf_usuarios_grupos',//lf_usuarios_grupos
					'idPai'=>'idGrupo',//idGrupo
					'options'=>'lf_usuarios',//usuarios
					'idFilhos'=>'idUsuario',//idUsuario
					'size'=>250
					);
		}
		else
		{
			$this->tipoLabel = 'Usuário';
			$this->tipoClass = 'usuarios';
			$this->tipoTitulo = 'Usuários';
			$array_rel = array(
					'label'=>'Grupos',//Grupos
					'name'=>'relGrupos',//relGrupos
					'type'=>'selectRel',
					'selectLabel'=>'usuario',
					'where'=>'tipo=2',
					'tableRel'=>'lf_usuarios_grupos',//lf_usuarios_grupos
					'idPai'=>'idUsuario',//idUsuario
					'options'=>'lf_usuarios',//grupos
					'idFilhos'=>'idGrupo',//idGrupo
					'size'=>250
					);
		}
		
		$this->formconfig = array(
			'form_action'=>'',
			'form_dbtable'=>'lf_usuarios',
			'form_class'=>$this->tipoClass,
			'form_title'=>$this->tipoTitulo,
			'campos'=>array(
				array(
					'label'=>$this->tipoLabel,
					'name'=>'usuario',
					'type'=>'text',
					'size'=>250,
					'required'=>true
					),
				array(
					'label'=>'Senha',
					'name'=>'senha',
					'type'=>'password',
					'size'=>250
					),
				array(
					'label'=>'Email',
					'name'=>'email',
					'type'=>'text',
					'size'=>250
					),
				array(
					'label'=>'Tipo',
					'name'=>'tipo',
					'type'=>'select',
					'options'=>array($tipo=>$this->tipoLabel),
					'size'=>250,
					'required'=>true
					),
				$array_rel,
				array(
					'label'=>'Permissões do '.$this->tipoLabel,//Permissões
					'name'=>'relPermissoes',//relGrupos
					'type'=>'selectRel',
					'tableRel'=>'lf_usuarios_permissoes',//lf_usuarios_permissoes
					'idPai'=>'idUsuario',//idUsuario
					'options'=>$avaliable_permissions,
					'idFilhos'=>'permissao',//idGrupo
					'size'=>250
					)
			)
		);
		//$avaliable_permissions
		//array('ambientes'=>'ambientes','servidores'=>'servidores'),//grupos
		//'options'=>array('ambientes','servidores'),//grupos
		//'options'=>array(array('id'=>'ambientes','nome'=>'ambientes'),array('id'=>'servidores','nome'=>'servidores')),//grupos
		
		$this->crud = new CrudBootstrap($this->formconfig);
	}
	
	
	function getAvaliablePermissions()
	{
		$path = Core::$system_path;
		$views = array_slice(scandir("app/views/"),2);
		$remover = array($this->config->config['defaultClass'].".php","login.php","search.php");
		
		$final = array_diff($views,$remover);
		
		$ff['*']='*';
		
		foreach($final as $f)
		{
			$f = substr($f,0,-4);
		
			$ff[$f] = $f;
			
		}
		
		
		//print_r($ff);
		//echo "<hr/>";
		//print_r($final);
		return $ff;
	}
	
	function conteudo()
	{
		$path = Core::$system_path;
		
		if ($this->tipoClass == 'grupos')
		{
			$this->crud->setListWhere('tipo = 2');
			$this->crud->setOrderby('usuario','ASC');
			$btnExtra = "<span class='label label-default' style='cursor:pointer;' title='Ir para usuários'><a href='".$path."/usuarios/' style='color:#fff;'>Usuários</a></span>";
			$array_label = "Usuarios";
			$array_rel= array(
				'relTable'=>'lf_usuarios_grupos',
				'table'=>'lf_usuarios',
				'relURL'=>'usuarios',
				'return'=>'usuario',
				'selectLabel'=>'usuario',
				'field'=>'idUsuario',
				'fieldPai'=>'idGrupo'
				);
		}
		else
		{
			$this->crud->setListWhere('tipo = 1');
			$this->crud->setOrderby('usuario','ASC');
			$btnExtra = "<span class='label label-default' style='cursor:pointer;' title='Ir para grupos'><a href='".$path."/grupos/' style='color:#fff;'>Grupos</a></span>";
			$array_label = "Grupos";
			$array_rel= array(
				'relTable'=>'lf_usuarios_grupos',
				'table'=>'lf_usuarios',
				'relURL'=>'grupos',
				'return'=>'usuario',
				'selectLabel'=>'usuario',
				'field'=>'idGrupo',
				'fieldPai'=>'idUsuario'
				);
		}

		$this->crud->criaFormList(
			array(
			$this->tipoLabel=>'usuario',
			'Ativo'=>array(
				'field'=>'ativo',
				'options'=>array('0'=>'Não','1'=>'Sim')
				),
				$array_label=>$array_rel
			),
			$btnExtra
		);
	}
	
	function view($id)
	{
		$this->crud->criaView($id);
	}
	
}

?>