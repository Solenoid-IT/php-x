<?php



include_once ( __DIR__ . '/../vendor/autoload.php' );



use \Solenoid\X\Security\RateLimiter;

use \Predis\Client;



$redis_client = new Client();
$redis_client->connect( '127.0.0.1', 6379 );



$rate_limiter = new RateLimiter( $redis_client );

if ( !$rate_limiter->pass( $_SERVER['REMOTE_ADDR'] ) )
{
    http_response_code( 429 );
    echo 'RateLimiter :: Too many requests';
    exit;
}



$redis_client->disconnect();



?>