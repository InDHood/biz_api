<?php  

error_reporting(E_ALL);
ini_set('display_errors', 1);

class Model {

	public static $mysqli;

	public function __construct() {

		$host 	= 'localhost';
		$user 	= 'li';
		$pass 	= 'one';
		$schema = 'api';
		// $port, $socket;

		Model::$mysqli = new mysqli( $host, $user, $pass, $schema );
		
	}


	public static function query( $tbl, $where="", $order="", $limit="100", $cols="" ){
		
		$arr = "";
		
		if( $where != '' ) { $where = 'WHERE '.$where; }
		if( $order != '' ) { $order = 'ORDER BY '.$order.' '; }
		if( $cols != '' ) { $fields = implode(',', $cols); } else { $fields = '*'; }
		if( empty($limit) ) { $limit = 100; }

		$sql = "SELECT ".$fields." FROM " . $tbl . " " . $where . " ". $order . " LIMIT ".$limit;
		$res = Model::$mysqli->query( $sql );
		while ( $rows = $res->fetch_assoc() ) { $arr[] = $rows; }
		$count = $res->num_rows;
		$res->free();
		Model::$mysqli->close();

		return ( $count == 1 ) ? $arr[0] : $arr;

	}

	public static function insert( $tbl, $arr ){
		
		$sql = "INSERT INTO ".$tbl." (".implode(',', array_keys($arr)).") VALUES ('".implode("','", array_values($arr))."')";
		$res = Model::$mysqli->query( $sql );
		$id  = Model::$mysqli->insert_id;
		Model::$mysqli->close();

		return $id;

	}

	public static function update($tbl, $arr, $where="", $limit="1", $e="" ){

		if( $where != '' ) { $where = 'WHERE '.$where; }
		else { $where = 'WHERE id = '.$arr['id']; }
		foreach ( $arr as $k => $v ) { if(isset($v) || $e!='') { $ar[] = $k."= '".$v."'"; } }
		$sql = "UPDATE " . $tbl . " SET " . implode(',', $ar) . " " . $where . " LIMIT " . $limit;
		
		$res = Model::$mysqli->query( $sql );
		Model::$mysqli->close();

		return $res;

	}

	public static function delete($tbl, $arr, $where="", $limit="1" ){
		
		if( $where != '' ) { 
			$where = 'WHERE ' . $where; 
		} else { 
			$where = "WHERE id = '" . $arr['id'] . "'"; 
		}
		$sql = "DELETE FROM " . $tbl . " " . $where . " LIMIT " . $limit;
		$res = Model::$mysqli->query( $sql );
		$count = intval( Model::$mysqli->affected_rows );
		Model::$mysqli->close();

		return ( $count > 0 ) ? 1 : 0;

	}

	public static $db = array(
		'users' => array (
			'cols' => array ('user', 'email', 'firstname', 'lastname', 'phone', 'gender'),
			'fields' => array ('user', 'pass', 'email', 'firstname', 'lastname', 'phone', 'gender')
			),
		'ar' => array(
			'cols' => array ('id', 'user_agent', 'api_key', 'token'),
			'fields' => array ('id', 'user_agent', 'api_key', 'token')
			),
		'arl' => array(
			'cols' => array('id', 'user_agent_id', 'date'),
			'fields' => array('id', 'user_agent_id')
			),
		'biz_plan' => array(
			'cols' => array('id', 'user_id', 'detais', 'date'),
			'fields' => array('id', 'user_id', 'detais', 'date')
			)
		); 


}

new Model();

// if( array_key_exists('ar', Model::$tbl) ) {
// 	e( 'Yea' );
// }

p( Model::insert( 'users', $d ) );


?>