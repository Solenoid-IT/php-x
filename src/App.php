<?php



namespace Solenoid\X;



use \Solenoid\X\Error;
use \Solenoid\X\Input\Validator;



class App
{
    private Container $container;

    private array $errors        = [];
    private array $conn_profiles = [];

    private ?Error $error = null;



    public readonly string $mode;
    public readonly string $basedir;



    public function __construct (string $basedir)
    {
        // (Getting the values)
        $this->mode    = isset( $_SERVER['REQUEST_METHOD'] ) ? 'http' : 'cli';
        $this->basedir = $basedir;
    }



    public function get_container () : Container
    {
        // Returning the value
        return $this->container;
    }

    public function set_container (Container $container) : self
    {
        // (Getting the value)
        $this->container = $container;



        // Returning the value
        return $this;
    }



    public function ip (string $fqdn) : string|null
    {
        // (Getting the value)
        $ip = dns_get_record( $fqdn, DNS_A );

        if ( !$ip )
        {// (Unable to resolve the FQDN)
            // Returning the value
            return '';
        }



        // Returning the value
        return $ip[0]['ip'];
    }



    public function register_errors (array $errors) : self
    {
        // (Setting the value)
        $this->errors = [];

        foreach ( $errors as $error )
        {// Processing each entry
            // (Getting the value)
            $this->errors[ $error['code'] ] = $error;
        }



        // Returning the value
        return $this;
    }

    public function get_errors () : array
    {
        // Returning the value
        return $this->errors;
    }



    public function spawn_error (int $code, string $message = '') : Error
    {
        // (Getting the value)
        $r = $this->errors[ $code ];

        if ( !$r )
        {// Value not found
            // Throwing an exception
            throw new \Exception( "Error code {$code} not found" );
        }



        // (Getting the value)
        $error = new Error( $code, $message, $r['type'], $r['http_code'], $r['exposed'] === '1' );



        if ( $this->error === null )
        {// Value not found
            // (Getting the value)
            $this->error = $error;
        }



        // Returning the value
        return $error;
    }

    public function get_error () : Error|null
    {
        // Returning the value
        return $this->error;
    }



    public function get_conn_profile (string $type, string $id) : array|null
    {
        // Returning the value
        return $this->conn_profiles[ $type ][ $id ];
    }

    public function set_conn_profiles (string $type, array $profiles) : self
    {
        // (Getting the value)
        $this->conn_profiles[ $type ] = $profiles;



        // Returning the value
        return $this;
    }



    public function run (string $class, string $method) : mixed
    {
        // (Setting the value)
        $middlewares = [];

        foreach ( ( new \ReflectionMethod( $class, $method ) )->getAttributes( Middleware::class ) as $attribute )
        {// Processing each entry
            // (Getting the value)
            $middlewares = ( $attribute->newInstance() )->pipes;

            // Breaking the iteration
            break;
        }



        // (Getting the value)
        $input_flow = function () use ($class, $method)
        {
            // (Getting the value)
            $instance = $this->container->make_instance( $class );



            // (Setting the value)
            $params = [];



            // (Getting the value)
            $validator = new Validator( $class, $method );

            if ( $validator->available() )
            {// Value is true
                // (Getting the value)
                $request = $this->container->make( 'request' );



                // (Getting the value)
                $input_type = $validator->get_input_type();



                // (Getting the value)
                $input = in_array( $input_type, [ 'DTO', 'ArrayList' ] ) ? $request->json( true ) : $request->buffer();



                // (Getting the value)
                $error = $validator->check( $input );

                if ( $error !== null )
                {// (Check failed)
                    // (Getting the value)
                    $response = $this->container->make( 'response' );



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
            return $this->container->run_instance_method( $instance, $method, $params );
        }
        ;



        // Returning the value
        return ( new Dispatcher( $this->container ) )->dispatch( $this->container->make( 'request' ), $middlewares, $input_flow );
    }
}



?>