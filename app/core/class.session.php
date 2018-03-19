<?php
ini_set("display_errors" , "1");
require_once("class.bd.php");
require_once("class.help.php");
require_once("class.config.php");

/*CREATE TABLE `sessions` (
  `id` CHAR(128) NOT NULL,
  `set_time` CHAR(10) NOT NULL,
  `data` text NOT NULL,
  `session_key` CHAR(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
*/

Class Session{
	
	var $bd;
	var $config;
	
	function __construct() {
		$this->config = new Config();
		//$this->open();
	   // set our custom session functions.
	   session_set_save_handler(array($this, 'open'), array($this, 'close'), array($this, 'read'), array($this, 'write'), array($this, 'destroy'), array($this, 'gc'));

	   // This line prevents unexpected effects when using objects as save handlers.
	   register_shutdown_function('session_write_close');
	}
	
	function open() {
		
	   $this->bd = new Mysqlidb (
		   	$this->config->config_db['dbHost'], 
			$this->config->config_db['dbUser'], 
			$this->config->config_db['dbPass'], 
			$this->config->config_db['dbDatabase'] );
	   return true;
	}
	
	function close() {
	   //$this->db->close();
	   return true;
	}
	
	function start_session($session_name, $secure) {
		
		
	   // Make sure the session cookie is not accessible via javascript.
	   $httponly = true;

	   // Hash algorithm to use for the session. (use hash_algos() to get a list of available hashes.)
	   $session_hash = 'sha512';

	   // Check if hash is available
	   if (in_array($session_hash, hash_algos())) {
		  // Set the has function.
		  ini_set('session.hash_function', $session_hash);
	   }
	   // How many bits per character of the hash.
	   // The possible values are '4' (0-9, a-f), '5' (0-9, a-v), and '6' (0-9, a-z, A-Z, "-", ",").
	   ini_set('session.hash_bits_per_character', 5);

	   // Force the session to only use cookies, not URL variables.
	   ini_set('session.use_only_cookies', 1);

	   // Get session cookie parameters 
	   $cookieParams = session_get_cookie_params(); 
	   // Set the parameters
	   session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); 
	   // Change the session name 
	   session_name($session_name);
	   // Now we cat start the session
	   session_start();
	   // This line regenerates the session and delete the old one. 
	   // It also generates a new encryption key in the database. 
	   session_regenerate_id(true); 
	
	}
	
	
	
	function read($id) {
		
		//$params = Array($id);
		//$q = "SELECT data FROM sessions WHERE id = ? LIMIT 1";
		//$resutls = $this->bd->rawQuery ($q, $params);
		
		$this->bd->where ("id", $id);
		$session = $this->bd->getOne ("sessions");
		//echo $session['id'];

		//return $resutls;
	
	   	$key = $this->getkey($id);
	   	$data = $this->decrypt($session['data'], $key);
	   	return $data;
	}
	
	function write($id, $data) {
		$debug = $data;
	   // Get unique key
	   $key = $this->getkey($id);
	   // Encrypt the data
	   $data = $this->encrypt($data, $key);

	   $time = time();
		
		$params = Array($id, $time, $data, $key, $debug);
		$q = "REPLACE INTO sessions (id, set_time, data, session_key, debug) VALUES (?, ?, ?, ?, ?)";
		$resutls = $this->bd->rawQuery ($q, $params);
		
	   return true;
	}
	
	function destroy($id) {
		$this->bd->where('id', $id);
		if($this->bd->delete('sessions')) return true;
		
	   /*if(!isset($this->delete_stmt)) {
		  $this->delete_stmt = $this->db->prepare("DELETE FROM sessions WHERE id = ?");
	   }
	   $this->delete_stmt->bind_param('s', $id);
	   $this->delete_stmt->execute();
	   return true;*/
	}
	
	
	function gc($max) {
		
		$old = time() - $max;
		$params = Array($old);
		$q = "DELETE FROM sessions WHERE set_time < ?";
		$resutls = $this->bd->rawQuery ($q, $params);
		
	   return true;
	}
	
	private function getkey($id) {
		$this->bd->where ("id", $id);
		$session = $this->bd->getOne ("sessions");
		
		if ($session['session_key']){
			return $session['session_key'];
		}else{
			$random_key = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
		  	return $random_key;
		}
		
	}
	
	private function encrypt($data, $key) {
	   $salt = 'cH!swe!retReGu7W6bEDRup7usuDUh9THeD2CHeGE*ewr4n39=E@rAsp7c-Ph@pH';
	   $key = substr(hash('sha256', $salt.$key.$salt), 0, 32);
	   $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
	   $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	   $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_ECB, $iv));
	   return $encrypted;
	}
	private function decrypt($data, $key) {
	   $salt = 'cH!swe!retReGu7W6bEDRup7usuDUh9THeD2CHeGE*ewr4n39=E@rAsp7c-Ph@pH';
	   $key = substr(hash('sha256', $salt.$key.$salt), 0, 32);
	   $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
	   $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	   $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($data), MCRYPT_MODE_ECB, $iv);
	   return $decrypted;
	}
	
}


?>