<?php



namespace Solenoid\X\HTTP;



use \Solenoid\X\Target;
use \Solenoid\X\Container;

use \Solenoid\X\Validation\Validator;



class Route
{
    private static array $routes = [];
    private static $fallback_route;



    private array $middlewares = [];



    public readonly string $method;
    public readonly string $path;

    public readonly Target $target;

    public readonly array $params;

    public readonly mixed $stop;



    public function __construct (string $method, string $path, Target $target)
    {
        // (Getting the values)
        $this->method = $method;
        $this->path   = $path;
        $this->target = $target;
    }



    public static function bind (string $route, callable|array $target) : self
    {
        // (Getting the values)
        [ $method, $path ] = explode( ' ', $route, 2 );



        // (Getting the value)
        $route = new self( $method, $path, is_array( $target ) ? Target::link( $target[0], $target[1] ) : Target::define( $target ) );



        // (Getting the value)
        self::$routes[ $route->method ][ $route->path ] = $route;



        // Returning the value
        return $route;
    }

    public static function fallback (callable|array $target) : void
    {
        // (Getting the value)
        self::$fallback_route = new self( '', '', is_array( $target ) ? Target::link( $target[0], $target[1] ) : Target::define( $target ) );
    }



    public function via (array $middlewares) : self
    {
        foreach ( $middlewares as $middleware )
        {// Processing each entry
            // (Appending the value)
            $this->middlewares[] = $middleware;
        }



        // Returning the value
        return $this;
    }



    public static function match (string $method, string $path) : self|false
    {
        // (Setting the value)
        $params = [];



        // (Setting the value)
        $route = false;

        if ( isset( self::$routes[ $method ][ $path ] ) )
        {// Value found
            // (Getting the value)
            $route = self::$routes[ $method ][ $path ];
        }
        else
        {// Value not found
            if ( self::$routes[ $method ] )
            {// Value found
                foreach ( self::$routes[ $method ] as $defined_path => $defined_route )
                {// Processing each entry
                    if ( strlen( $defined_path ) >= 2 && $defined_path[0] === '/' && $defined_path[ strlen( $defined_path ) - 1 ] === '/' )
                    {// (Path is a regex)
                        if ( preg_match( $defined_path, $path, $matches ) === 1 )
                        {// Match OK
                            // (Getting the value)
                            $route = $defined_route;

                            // (Getting the value)
                            $route->params = $matches;

                            // Breaking the iteration
                            break;
                        }
                    }
                    else
                    {// (Path is not a regex)
                        // (Getting the values)
                        $path_parts         = explode( '/', $path );
                        $defined_path_parts = explode( '/', $defined_path );

                        if ( count( $path_parts ) !== count( $defined_path_parts ) ) continue;



                        // (Getting the value)
                        $diff = array_diff( $path_parts, $defined_path_parts );

                        if ( !$diff )
                        {// (Parts are equals)
                            // (Getting the value)
                            $route = $defined_route;

                            // Breaking the iteration
                            break;
                        }



                        foreach ( $path_parts as $k => $v )
                        {// Processing each entry
                            if ( $path_parts[$k] !== $defined_path_parts[$k] )
                            {// (Values are different)
                                if ( preg_match( '/\{\s*([^\s]+)\s*\}/', $defined_path_parts[$k], $matches ) === 1 )
                                {// Match OK
                                    // (Getting the value)
                                    $params[ $matches[1] ] = $path_parts[$k];
                                }
                                else
                                {// Match failed
                                    // Breaking the iteration
                                    break;
                                }
                            }
                        }



                        if ( $params )
                        {// Value found
                            // (Getting the value)
                            $route = $defined_route;

                            // (Getting the value)
                            $route->params = $params;

                            // Breaking the iteration
                            break;
                        }
                    }
                }
            }
        }



        if ( !$route )
        {// Value not found
            if ( self::$fallback_route )
            {// Value found
                // (Getting the value)
                $route = self::$fallback_route;
            }
        }



        if ( $route )
        {// Value found
            if ( !isset( $route->params ) )
            {// Value not found
                // (Setting the value)
                $route->params = [];
            }
        }



        // Returning the value
        return $route;
    }



    public function run (Container $container) : mixed
    {
        foreach ( $this->middlewares as $middleware )
        {// Processing each entry
            // (Getting the value)
            $middleware = $container->make_instance( $middleware );

            if ( $container->run_instance_method( $middleware, 'run' ) === false )
            {// (Middleware has not been passed)
                // (Getting the value)
                $this->stop = $middleware;

                // Returning the value
                return false;
            }
        }



        if ( $this->target )
        {// Value found
            if ( isset( $this->target->function ) )
            {// (Target is a function)
                // (Getting the value)
                #$result = call_user_func_array( $this->target->function, $this->params );
                $result = $container->run_function( $this->target->function, $this->params );
            }
            else
            if ( isset( $this->target->class ) && isset( $this->target->fn ) )
            {// (Target is a class method)
                /* ahcid to implementt

                // (Getting the value)
                $validator = new Validator( $this->target->class, $this->target->fn );



                // (Getting the value)
                $error = $validator->check_input( 'ahcid' );

                if ( $error )
                {// (Validation failed)
                    // ahcid
                }



                // (Getting the value)
                $errors = $validator->check_input_params( [ 'a' => 0, 'b' => false ] );

                if ( $errors )
                {// (Validation failed)
                    // ahcid
                }

                */



                // (Getting the value)
                #$result = call_user_func_array( [ new $this->target->class(), $this->target->fn ], $this->params );
                $result = $container->run_class_fn( $this->target->class, $this->target->fn, $this->params );
            }
        }



        // Returning the value
        return $result;
    }



    public function __toString () : string
    {
        // Returning the value
        return $this->method === '' && $this->path === '' ? '@fallback' : "{$this->method} {$this->path}";
    }
}



?>