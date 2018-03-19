<?php

require_once("class.config.php");

Class LDAP{

	public $config;
	
	public $ldap_host;
	public $ldap_port;
	public $ldap_domain;
	public $ldap_dn;
	public $ldap_group;
	
	public $bind;
	public $ds;
	public $usuario;
	
	public function LDAP($usuario,$senha){
	
		$this->usuario = $usuario;
	
		$this->config      = new Config();
		$this->ldap_host   = $this->config->config['login_ldap_host'];
		$this->ldap_port   = $this->config->config['login_ldap_port'];
		$this->ldap_domain = $this->config->config['login_ldap_domain'];
		$this->ldap_dn     = $this->config->config['login_ldap_dn'];
		$this->ldap_group  = $this->config->config['login_ldap_group'];
		
		$this->ds = ldap_connect($this->ldap_host, $this->ldap_port);
		ldap_set_option($this->ds, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->ds, LDAP_OPT_REFERRALS, 0);
		
		$this->bind = @ldap_bind($this->ds, $this->usuario .'@'.$this->ldap_domain, $senha);
	
	}
	
	public function login()
	{
		if ($this->bind) {

			$userdn = $this->getDN($this->ds, $this->usuario, $this->ldap_dn);
			if (!$this->checkGroupEx($this->ds, $userdn, $this->getDN($this->ds, $this->ldap_group, $this->ldap_dn))) {
				//echo "<script>alert('Você não pertence ao grupo ".$group."')</script>";
				//unset ($_SESSION['login']);
				
				return false;
			}
			else{
				//$_SESSION['login'] = $login;
				//header('location: '.$this->config->config['defaultClass'].'/');
				return true;
			}
		} else {
			//echo "<script>alert('Autenticação falhou!')</script>";
			//unset ($_SESSION['login']);
			return false;
		}
	}
	
	public function getDN($ad, $samaccountname, $basedn)
	{
		$attributes = array('dn');
		$result = @ldap_search($ad, $basedn,
			"(samaccountname={$samaccountname})", $attributes);
		if ($result === FALSE) { return ''; }
		$entries = ldap_get_entries($ad, $result);
		if ($entries['count']>0) { return $entries[0]['dn']; }
		else { return ''; };
	}
	
	public function checkGroupEx($ad, $userdn, $groupdn)
	{
		$attributes = array('memberof');
		$result = ldap_read($ad, $userdn, '(objectclass=*)', $attributes);
		if ($result === FALSE) { return FALSE; };
		$entries = ldap_get_entries($ad, $result);
		if ($entries['count'] <= 0) { return FALSE; };
		if (empty($entries[0]['memberof'])) { return FALSE; } else {
			for ($i = 0; $i < $entries[0]['memberof']['count']; $i++) {
				if ($entries[0]['memberof'][$i] == $groupdn) { return TRUE; }
				elseif ($this->checkGroupEx($ad, $entries[0]['memberof'][$i], $groupdn)) { return TRUE; };
			};
		};
		return FALSE;
	}
}
?>