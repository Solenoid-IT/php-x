<?php



namespace Solenoid\X;



class Pipeline
{
    protected Container $container;

    protected $passable;
    protected $pipes = [];



    public function __construct (Container $container)
    {
        // (Getting the value)
        $this->container = $container;
    }



    public function send ($passable) : self
    {
        // (Getting the value)
        $this->passable = $passable;



        // Returning the value
        return $this;
    }



    public function through (array $pipes) : self
    {
        // (Getting the value)
        $this->pipes = $pipes;



        // Returning the value
        return $this;
    }



    public function then (callable $destination) : mixed
    {
        // (Getting the value)
        $pipeline = array_reduce( array_reverse( $this->pipes ), $this->carry(), $destination );



        // Returning the value
        return $pipeline( $this->passable );
    }



    protected function carry () : callable
    {
        // Returning the value
        return function ($stack, $pipe)
        {
            // Returning the value
            return function ($passable) use ($stack, $pipe)
            {
                // (Getting the value)
                $instance = is_string( $pipe ) ? $this->container->make( $pipe ) : $pipe;



                // (Setting the value)
                $params = [];



                if ( is_object( $passable ) )
                {// Match OK
                    // (Getting the value)
                    $params[ get_class( $passable ) ] = $passable;
                }



                // (Getting the values)
                $params['input'] = $passable;
                $params['next']  = $stack;



                // Returning the value
                return $this->container->run_instance_method( $instance, 'handle', $params );
            };
        }
        ;
    }
}



?>