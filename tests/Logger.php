<?php



include_once( __DIR__ . '/../vendor/autoload.php' );


use \Solenoid\X\Logger;



$logger = new Logger( __DIR__ . '/../log/test.log', pid: true );
$logger->push( "Test\n1\n2\n3", 'D' );


?>