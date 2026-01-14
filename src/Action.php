<?php



namespace Solenoid\X;



use \Solenoid\X\Container;
use \Solenoid\X\Middleware;

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
            // (Setting the value)
            $this->error = 'sRPC :: Action-Method is required';

            // Returning the value
            return;
        }



        // (Getting the value)
        $this->class = $class;



        // (Getting the value)
        $this->class_path = str_replace( '/', '\\', "$prefix/$class" );

        if ( !class_exists( $this->class_path ) )
        {// (Class not found)
            // (Setting the value)
            $this->error = 'sRPC :: Action-Class not found';

            // Returning the value
            return;
        }



        if ( !method_exists( $this->class_path, $method ) )
        {// (Method not found)
            // (Setting the value)
            $this->error = 'sRPC :: Action-Method not found';

            // Returning the value
            return;
        }



        // (Getting the value)
        $this->method = $method;
    }



    public function run (Container $container) : mixed
    {
        // (Setting the value)
        $middlewares = [];

        foreach ( ( new \ReflectionMethod( $this->class_path, $this->method ) )->getAttributes( Middleware::class ) as $attribute )
        {// Processing each entry
            // (Getting the value)
            $middlewares = ( $attribute->newInstance() )->pipes;

            // Breaking the iteration
            break;
        }



        // (Getting the value)
        $input_flow = function () use ($container)
        {
            // (Getting the value)
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
                $input_type = $validator->get_input_type();



                // (Getting the value)
                $input = in_array( $input_type, [ 'DTO', 'ArrayList' ] ) ? $request->json( true ) : $request->buffer();



                // (Getting the value)
                $error = $validator->check( $input );

                if ( $error !== null )
                {// (Check failed)
                    // (Getting the value)
                    $response = $container->make( 'response' );



                    if ( $error instanceof \stdClass || is_array( $error ) )
                    {// (Error of a DTO or ArrayList)
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
                    switch ( $input_type )
                    {// Processing each type
                        case 'Value':
                            // (Getting the value)
                            $params = [ $validator->get_value() ];
                        break;

                        case 'DTO':
                            // (Getting the value)
                            $dto = $validator->get_value();



                            // (Getting the value)
                            $params =
                            [
                                get_class( $dto ) => $dto,
                                'dto'             => $dto
                            ]
                            ;

                            // (Getting the value)
                            $params = array_merge( $params, (array) $dto );
                        break;

                        case 'ArrayList':
                            // (Getting the value)
                            $params = [ $validator->get_value() ];

                            # ahcid List<DTO> to implementt
                        break;
                    }
                }
            }



            // Returning the value
            return $this->stopped ? null : $container->run_instance_method( $instance, $this->method, $params );
        }
        ;



        // Returning the value
        return ( new Dispatcher( $container ) )->dispatch( $container->make( 'request' ), $middlewares, $input_flow );
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