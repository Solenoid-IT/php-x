<?php



namespace Solenoid\X\Security;



use \Predis\Client;



class RateLimiter
{
    public function __construct (private Client $client, private int $max_rate = 50, private int $time_limit = 60, private string $key_prefix = 'rate_limit') {}



    public function pass (string $subject) : bool
    {
        // (Getting the value)
        $time = microtime( true );



        // (Getting the value)
        $key = "{$this->key_prefix}:$subject";



        // (Getting the value)
        $pipe = $this->client->multi();



        // (Getting the value)
        $pipe->zRemRangeByScore( $key, 0, $time - $this->time_limit );

        // (Adding the value)
        $pipe->zAdd( $key, $time, $time );

        // (Count requests)
        $pipe->zCard($key);

        // (Setting the TTL)
        $pipe->expire( $key, $this->time_limit + 1 );



        // (Executing the command)
        $responses = $pipe->exec();



        // (Getting the value)
        $rate = $responses[2];



        // Returning the value
        return $rate <= $this->max_rate;
    }

    public function reset (string $subject) : bool
    {
        $key = "{$this->key_prefix}:$subject";



        // Returning the value
        return (bool) $this->client->del( [ $key ] );
    }
}



?>