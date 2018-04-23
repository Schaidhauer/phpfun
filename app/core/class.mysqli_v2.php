<?php
class MysqliDb_v2
{

    /**
     * Database credentials
     *
     * @var string
     */
    protected $host;
    protected $username;
    protected $password;
    protected $db;
    protected $port;
	
	public $connection;


    public function __construct($host = NULL, $username = NULL, $password = NULL, $db = NULL, $port = NULL)
    {
        $this->connection = new mysqli($host, $username, $password, $db);
		if(mysqli_connect_error())
		{
			return "can not connect to database ".mysqli_connect_error();
		}
		$this->connection->set_charset ('utf8');
    }
		
	public function __destruct(){
		mysqli_close($this->connection);

	}

	/**
	 * Run select, insert, update or delete query
	 * A successful query will return $result as true
	 * For select query after query() run fetch() to fetch result rows
	 * @return result object returned, contains details like $result->num_rows
	 * @param $query a query to be run
	 * example usage
	 * $query = "select * from table"; // or insert, update, delete query
	 * $result = $db->query($query);
	 */
	public function insert($query)
	{
		$this->result = $this->connection->query($query);
		//$this->result = $this->connection->query($this->connection->real_escape_string($query));
		return mysqli_insert_id($this->connection);
	}

	/**
	 * Run select, insert, update or delete query
	 * A successful query will return $result as true
	 * For select query after query() run fetch() to fetch result rows
	 * @return result object returned, contains details like $result->num_rows
	 * @param $query a query to be run
	 * example usage
	 * $query = "select * from table"; // or insert, update, delete query
	 * $result = $db->query($query);
	 */
	public function query($query){
		$this->result = $this->connection->query($query);
		return $this->result;

	}
	/**
	 * For select queries run query($query) and then fetch() to get result rows
	 * @return associative array of all selected rows
	 * example usage
	 * $rows = $db->fetch();
	 */
	public function fetch(){
		if(!$this->result){
			return "no results";
		}
		while($row = $this->result->fetch_assoc()){
			$rows[] = $row;
		}
		if (sizeof(@$rows)>0)
			return $rows;
		else
			return false;
	}

	
	
} // END class

?>