<?php



namespace Solenoid\X;



use \Predis\Client;



class RateLimiter
{
    public function __construct (private Client $client, private int $max_qty = 50, private int $time_window = 60, private string $key_prefix = 'rate_limit') {}



    public function pass (string $subject) : bool
    {
        // (Getting the value)
        $time = microtime( true );



        // (Getting the value)
        $key = "{$this->key_prefix}:$subject";



        // (Getting the value)
        $pipe = $this->client->multi();



        // (Getting the value)
        $pipe->zRemRangeByScore( $key, 0, $time - $this->time_window );

        // (Adding the value)
        $pipe->zAdd( $key, $time, $time );

        // (Count requests)
        $pipe->zCard($key);

        // (Setting the TTL)
        $pipe->expire( $key, $this->time_window + 1 );



        // (Executing the command)
        $responses = $pipe->exec();



        // (Getting the value)
        $qty = $responses[2];



        // Returning the value
        return $qty <= $this->max_qty;
    }
}



?>