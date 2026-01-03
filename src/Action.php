<?php



namespace Solenoid\X;



use \Solenoid\X\Container;
use \Solenoid\X\Input\Validator;



class Action
{
    private bool $stopped = false;



    public readonly string $error;

    public readonly string $class;
    public readonly string $method;

    public readonly string $class_path;



    public function __construct (string $prefix, string $id)
    {
        // (Getting the values)
        [ $class, $method ] = explode( '.', $id, 2 );

        if ( !$method )
        {// Value not found
            // (Getting the value)
            $this->error = 'RPC :: Action-Method is required';

            // Returning the value
            return;
        }



        // (Getting the value)
        $this->class = $class;



        // (Getting the value)
        $this->class_path = str_replace( '/', '\\', "$prefix/$class" );

        if ( !class_exists( $this->class_path ) )
        {// (Class not found)
            // (Getting the value)
            $this->error = 'RPC :: Action-Class not found';

            // Returning the value
            return;
        }



        if ( !method_exists( $this->class_path, $method ) )
        {// (Method not found)
            // (Getting the value)
            $this->error = 'RPC :: Action-Method not found';

            // Returning the value
            return;
        }



        // (Getting the value)
        $this->method = $method;
    }



    public function run (Container $container) : mixed
    {
        // (Getting the value)
        #$instance = new $this->class_path();
        $instance = $container->make_instance( $this->class_path );



        // (Setting the value)
        $params = [];



        // (Getting the value)
        $validator = new Validator( $this->class_path, $this->method );

        if ( $validator->available() )
        {// Value is true
            // (Getting the value)
            $request = $container->make( 'request' );



            // (Getting the value)
            $error = $validator->check( $request->buffer() );

            if ( $error !== null )
            {// (Check failed)
                // (Getting the value)
                $response = $container->make( 'response' );



                if ( $error instanceof \stdClass )
                {// (Error of a DTO)
                    // Returning the value
                    return $response->json( 400, $error );
                }
                else
                {// (Error of a Value)
                    // Returning the value
                    return $response->text( 400, $error );
                }
            }
            else
            {// (Check passed)
                // (Getting the value)
                $params = [ $validator->get_value() ];
            }
        }



        // Returning the value
        #return $this->stopped ? null : $instance->{ $this->method }();
        return $this->stopped ? null : $container->run_instance_method( $instance, $this->method, $params );
    }



    public function stop () : self
    {
        // (Setting the value)
        $this->stopped = true;



        // Returning the value
        return $this;
    }



    public function __toString () : string
    {
        // Returning the value
        return "{$this->class}.{$this->method}";
    }
}



?>