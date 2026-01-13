<?php



namespace Solenoid\X;



class Dispatcher
{
    protected Container $container;



    public function __construct (Container $container)
    {
        // (Getting the value)
        $this->container = $container;
    }



    public function dispatch ($input, array $middlewares, callable $destination) : mixed
    {
        // Returning the value
        return ( new Pipeline( $this->container ) )->send( $input )->through( $middlewares )->then( function ($input) use ($destination) { return $destination( $input ); } );
    }
}



?>