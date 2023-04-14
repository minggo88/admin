<?php


$DB_CONN = "";
$DB_HOST = &$db_host;
$DB_NAME = &$db_name;
$DB_USER = &$db_user;
$DB_PASS = &$db_pass;
$DB_ = &$db_pass;

$SESS_LIFE = get_cfg_var( "session.gc_maxlifetime" );

function sess_open( $save_path, $session_name )
{
	global $DB_CONN, $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;
	if (!( $DB_CONN = mysqli_connect( $DB_HOST, $DB_USER, $DB_PASS ))) {
		echo 'Query ErrorNo : '.mysqli_errno();
		echo 'Query Error Message : '.mysqli_error();
		exit;
	}

	if(!empty($this->db_charset)) { mysqli_query('SET NAMES '.$this->db_charset,$this->connect); }


	if (!(mysqli_select_db( $DB_NAME, $DB_CONN ))) {
		echo 'Query ErrorNo : '.mysqli_errno();
		echo 'Query Error Message : '.mysqli_error();
		exit;
	}
	// MySQL 8 에서 날짜 형식 오류 제외하기
	// SELECT VERSION();
	mysqli_query($this->connect,"SET @@SESSION.sql_mode = CONCAT_WS(',', @@SESSION.sql_mode, 'ALLOW_INVALID_DATES')");

	return true;
}

function sess_close()
{
	return true;
}

function sess_read( $key )
{
	global $DB_CONN, $SESS_LIFE;
	$sess_query = "SELECT value FROM session WHERE sesskey = '$key' AND expiry > " . time();
	$sess_result = mysqli_query( $sess_query, $DB_CONN );
	if( list( $value ) = mysqli_fetch_row( $sess_result ) ) {
		return $value;
	}
	return false;
}

function sess_write( $key, $val )
{
	global $DB_CONN, $SESS_LIFE;
	$expiry = time() + $SESS_LIFE;
	$value = addslashes($val);
	$sess_query = "INSERT INTO session ( sesskey, expiry, value ) VALUES ( '$key', $expiry, '$value' )";
	$sess_result = mysqli_query( $sess_query, $DB_CONN );
	if( !$sess_result ) {
		$sess_query = "UPDATE session SET expiry = $expiry, value = '$value' WHERE sesskey = '$key' AND expiry > " . time();
		$sess_result = mysqli_query( $sess_query, $DB_CONN );
	}
	return $sess_result;
}

function sess_destroy( $key )
{
	global $DB_CONN;
	$sess_query = "DELETE FROM session WHERE sesskey = '$key'";
	$sess_result = mysqli_query( $sess_query, $DB_CONN );
	return $sess_result;
}

function sess_gc( $maxlifetime ) {
	global $DB_CONN;
	$sess_query = "DELETE FROM session WHERE expiry < " . time();
	$sess_result = mysqli_query( $sess_query, $DB_CONN );
	return mysqli_affected_rows( $DB_CONN );
}

session_set_save_handler("sess_open", "sess_close", "sess_read", "sess_write", "sess_destroy", "sess_gc");
session_start();
?>