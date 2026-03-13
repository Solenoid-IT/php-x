<?php



include_once ( __DIR__ . '/../vendor/autoload.php' );



use \Solenoid\X\Schedule;


$time = time();



$datetime = date( 'c', $time );
$schedule = new Schedule( 'EVERY 5 SECOND' );

echo "Testing time '$datetime' with schedule '{$schedule->interval} -> " . ( $schedule->check( $time ) ? 'true' : 'false' ) . "\n";



?>