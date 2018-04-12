<?php

//include_once('class.bd.php');
include_once('class.mysqli_v2.php');

Class Conexao
{
	public $tipo;
	public $conn;
	public $config;

	public function Conexao($type = '',$config = '')
	{
		//se inputar os valores, é sinal que é custom, NAO baseado na config geral do phpfun.
		
		if ($type == '')
		{
			$this->config   = new Config();
		
			$this->dbhost   = $this->config->config_db['dbHost'];
			$this->db       = $this->config->config_db['dbDatabase'];
			$this->user     = $this->config->config_db['dbUser'];
			$this->password = $this->config->config_db['dbPass'];
			
	
			if ($this->config->config_db['dbType'] == 'sql'){
				$this->tipo = 'sql';
			}else if ($this->config->config_db['dbType'] == 'mysql'){
				$this->tipo = 'mysql';
			}else{
				die("Problema na conexão com o BD. Verificar o tipo de conexão configurada.");
			}
			$this->connect();
		}
		else
		{
			
			$this->dbhost   = $config['dbHost'];
			$this->db       = $config['dbDatabase'];
			$this->user     = $config['dbUser'];
			$this->password = $config['dbPass'];
			
			if ($type == 'sql'){
				$this->tipo = 'sql';
			}else if ($type == 'mysql'){
				$this->tipo = 'mysql';
			}else{
				die("Problema na conexão com o BD. Verificar o tipo de conexão configurada.");
			}
			$this->connect();
		
		}
	}
	
	public function connect()
	{
		
		if ($this->tipo == 'sql')
		{
			$conninfo = array("Database" => $this->db, "UID" => $this->user, "PWD" => $this->password);
			$this->conn = sqlsrv_connect($this->dbhost, $conninfo);
		}else if ($this->tipo == 'mysql')
		{
			//$this->conn = new MysqliDb($this->dbhost,$this->user,$this->password,$this->db);
			$this->conn = new MysqliDb_v2($this->dbhost,$this->user,$this->password,$this->db);
			
		}
		
	}
	
	public function select($query,$bool=false)
	{
		$sql = $query;
		//echo $sql;
		
		if ($this->tipo == 'sql')
		{
			$params = array();
			$options =array("Scrollable" => SQLSRV_CURSOR_KEYSET);
			$consulta = sqlsrv_query($this->conn, $sql, $params, $options);
			$res = array();
			
			if (!$bool)
			{
				while ($result = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC))
				{
					$res[] = $result;
				}
				return $res;
			}
			else
			{
				$num = sqlsrv_num_rows($consulta);
		
				if ($num > 0)
					return true;
				else
					return false;
			}
		
		
		}
		else if ($this->tipo == 'mysql')
		{
			//erros aqui: $this->conn->getLastError
			//count aqui: affected_rows
			//if (!$bool)
			//{
				if ($this->conn->query($query))
					return $this->conn->fetch();
				else
					die ("ERRO MYSQL: {".$query."}");
			//}	
			
		
		}
		
	
	}
	
	public function executa($query)
	{
	//para INSERT e UPDATE, sem retorno
		$sql = $query;
		
		if ($this->tipo == 'sql')
		{
			$sql = $query;

			//echo $sql;
			$params = array();
			$options =array("Scrollable" => SQLSRV_CURSOR_KEYSET);
			$consulta = sqlsrv_query($this->conn, $sql, $params, $options);
		
		
		}
		else if ($this->tipo == 'mysql')
		{
			//echo $query;
			return $this->conn->query($query);
		
		}
		
	
	}
	
	public function insert($query)
	{
		if ($this->tipo == 'mysql')
		{
			//echo $query;
			//retorna o lastid
			return $this->conn->insert($query);
		
		}
		
	
	}
}
?>