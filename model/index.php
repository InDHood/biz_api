<?php  

$host = 'localhost';
$user = 'li';
$pass = 'one';
$schema = 'api';
// $port, $socket;

$mysqli = new mysqli( $host, $user, $pass, $schema );
// $mysqli->query("DROP TABLE IF EXISTS test");
$mysqli->query("CREATE TABLE test(id INT)");
// $mysqli->query("INSERT INTO test(id) VALUES (1), (2)");

?>