<?php
/*
|--------------------------------------------------------------------------
| CLASSE PRINCIPAL DO SISTEMA CASO NENHUMA FOR CHAMADA
|--------------------------------------------------------------------------
|
| utilizado normalmente para aspaginas Home do site, se nao tiver nada do lado o /, cai nessa classe.
|
*/
$config_g['defaultClass'] = 'hello';


/*
|--------------------------------------------------------------------------
| PASTA DO SISTEMA SITE/PASTA
|--------------------------------------------------------------------------
|
| Caso o sistema rode em uma pasta do site principal, exemplo: site.com.br/pasta
| Para deixar no padrão é necessário deixar em /
|
*/
$config_g['systemFolder'] = '/yourfolder';

/*
|--------------------------------------------------------------------------
| PROTOCOLO
|--------------------------------------------------------------------------
|
| http ou https
|
*/
$config_g['protocolo']   = 'http';

/*
|--------------------------------------------------------------------------
| PERMISSOES
|--------------------------------------------------------------------------
|
| Define se o acesso tem permissoes por modulos(classes) ou somente o login 
| libera tudo.
|
| true ou false
|
*/
$config_g['allowAllClasses']   = true;

/*
|--------------------------------------------------------------------------
| TIPO DE LOGIN
|--------------------------------------------------------------------------
|
| ldap ou bd
|
*/
$config_g['login_tipo']        = 'ldap';

/*LOGIN via LDAP*/
$config_g['login_ldap_host']   = '';
$config_g['login_ldap_port']   = '';
$config_g['login_ldap_domain'] = '';
$config_g['login_ldap_dn']     = '';
$config_g['login_ldap_group']  = '';

/*LOGIN via BD*/
$config_g['login_bd_table']    = '';
$config_g['login_bd_usuario']  = '';
$config_g['login_bd_senha']    = '';

?>