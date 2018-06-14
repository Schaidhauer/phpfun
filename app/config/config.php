<?php
/*
|--------------------------------------------------------------------------
| CLASSE PRINCIPAL DO SISTEMA CASO NENHUMA FOR CHAMADA
|--------------------------------------------------------------------------
|
| utilizado normalmente para aspaginas Home do site, se nao tiver nada do lado o /, cai nessa classe.
|
*/
$config_g['defaultClass'] = 'dashboard';


/*
|--------------------------------------------------------------------------
| PASTA DO SISTEMA SITE/PASTA
|--------------------------------------------------------------------------
|
| Caso o sistema rode em uma pasta do site principal, exemplo: site.com.br/pasta
| Para deixar no padrão é necessário deixar em /
|
*/
$config_g['systemFolder'] = '/phpfun';

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
| AUDIT
|--------------------------------------------------------------------------
|
| Auditar objetos do BD, com alterações e inserções
|
| true ou false
|
*/
$config_g['audit']   = true;

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
$config_g['allowAllClasses']   = false;

/*
|--------------------------------------------------------------------------
| PAGINACAO
|--------------------------------------------------------------------------
|
| Define quantas linhas vao ser apresentadas antes de começar a paginar
|
|
*/
$config_g['paginarMax'] = 10;

/*
|--------------------------------------------------------------------------
| SESSION TIMEOUT
|--------------------------------------------------------------------------
|
| Tempo para expirar a sessao do browser
|
|
*/
$config_g['session_timeout'] = 9600;

/*
|--------------------------------------------------------------------------
| TIPO DE LOGIN
|--------------------------------------------------------------------------
|
| ldap ou bd
|
*/
$config_g['login_tipo']          = 'ldap';

/*LOGIN via LDAP*/
$config_g['login_ldap_host']     = '';
$config_g['login_ldap_port']     = '';
$config_g['login_ldap_domain']   = '';
$config_g['login_ldap_dn']       = '';
$config_g['login_ldap_group']    = '';

/*LOGIN via BD ou salvar infos do LDAP para aplicar permissoes*/
$config_g['login_bd_table']      = 'lf_usuarios';
$config_g['login_bd_permissoes'] = 'lf_usuarios_permissoes';
$config_g['login_bd_rel_grupo']  = 'lf_usuarios_grupos';
$config_g['login_bd_usuario']    = 'usuario';
$config_g['login_bd_senha']      = 'senha';

?>