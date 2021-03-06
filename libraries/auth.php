<?php 
require_once "connect.php";
require_once "guid.php";
require_once "basic.php";
require_once "errors.php";
$GUID = new GUID;

class auth{
	public function signin($table, $login, $password, $cookieLife = false){
		global $GUID;

		$login = htmlspecialchars(  filter_var( trim( $login ), FILTER_SANITIZE_STRING ) );
		$password = htmlspecialchars(  filter_var( trim( $password ), FILTER_SANITIZE_STRING ) );

		if ($login == '' || $password == '') return false;
		$check = $GUID->get($table, "`login` = '$login' AND `password` = '$password'");
		
		if ( $cookieLife ) setcookie( 'user-id', $check['id'], time() + $cookieLife, '/');

		if ( $check ) return true;
		else return false; 
	}

	public function signup( $table, $columns, $values, $login, $password, $perProtection, $cookieLife  = false ){
		global $GUID;
		global $errors;

		$login = htmlspecialchars(  filter_var( trim( $login ), FILTER_SANITIZE_STRING ) );
		$password = htmlspecialchars(  filter_var( trim( $password ), FILTER_SANITIZE_STRING ) );

		$checkLogin = $GUID->get( $table, "`login` = '$login'" );
		if ( $checkLogin ) return $errors->error(504);
		
		$easyPasswords = ['qwerty','qwertyu','qwerty123','123456789','1234567890','0123456789','password','mypassword','parol'];

		$valuesArr = explode( ',', $values );
		foreach ( $valuesArr as $key => $value ) $valuesArray[] = "'" . htmlspecialchars(  filter_var( trim($value), FILTER_SANITIZE_STRING ) ) . "'";
		$values = implode( ',', $valuesArray );

		foreach ( $easyPasswords as $key => $value ) if ( mb_strtolower( $password ) == $value ) return $errors->error(501);

		foreach ( $valuesArray as $key => $value ) if ( mb_strtolower( $password ) == mb_strtolower( trim( $value, "'" ) ) ) return $errors->error(501);

		if ( mb_strtolower( $password ) == mb_strtolower( $login ) ) return $errors->error(501);
		if ( mb_strlen( $password ) < 8 ) return $errors->error(502);

		$diffPassword = 0;
		if ( mb_strlen($password) > 12 ) $diffPassword++;
		if ( preg_match( "/([a-z]+)/", $password ) ) $diffPassword++; 
  		if ( preg_match( "/([A-Z]+)/", $password ) ) $diffPassword++;
  		if ( preg_match( "/([0-9]+)/", $password ) ) $diffPassword++;
  		if ( preg_match( "/(W+)/", $password ) ) $diffPassword++;

  		if ( $diffPassword <= $perProtection ) return $errors->error(503);
  		else {
  			$columns = explode(',', $columns);
  			$values = explode(',', $values);
  			$columns[] = "login";
  			$columns[] = "password";
  			$values[] = "'" . $login . "'";
  			$values[] = "'" . $password . "'";
  			$columns = implode( ',', $columns );
  			$values = implode( ',', $values );
  			
  			if ( $cookieLife ) setcookie( 'user-id', $check['id'], time() + $cookieLife, '/');

  			$check = $GUID->insert($table, $columns, $values);
  			return $check;
		}
	}
	public function check( $cookieName, $url, $backURL = false ){
		if ( !isset( $_COOKIE[$cookieName] ) ) redirect( $url, $backURL );
		else return true;
	}
}
//AUTH
// include auth.php
// $auth = new auth;
// $signin = $auth -> signin( 'table name', 'login', 'password', 'cookie life' );
// $signup = $auth -> signup( 'table name', 'columns', 'values', 'login', 'password', 'permitted protection', 'cookie life' );

$auth = new auth;
// $signup = $auth -> signup( 'users', 'name', 'Diyorbek', 'Diko@iProger', 'Parol@Password2001', 3);
// $signin = $auth -> signin( 'users', 'Diko@iProger', 'Parol@Password2001', 500 );

// var_dump($signin);
// $auth->check('id', '/', currentURL(false) );
echo currentURL(false);
?>