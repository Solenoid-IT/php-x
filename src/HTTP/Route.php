<?php



namespace Solenoid\X\HTTP;



class Route
{
    private static array $targets = [];
    private static $fallback_target;

    private array $middlewares = [];



    public readonly string $method;
    public readonly string $path;

    public readonly array $params;



    public function __construct (string $method, string $path, array $params = [])
    {
        // (Getting the values)
        $this->method = $method;
        $this->path   = $path;
        $this->params = $params;
    }



    public static function bind (string $route, callable|array $target) : self
    {
        // (Getting the values)
        [ $method, $path ] = explode( ' ', $route, 2 );



        // (Getting the value)
        $route = new self( $method, $path );



        // (Getting the value)
        self::$targets[ $route->method ][ $route->path ] = $target;



        // Returning the value
        return $route;
    }

    public static function fallback (callable|array $target) : void
    {
        // (Getting the value)
        self::$fallback_target = $target;
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

        if ( isset( self::$targets[ $method ][ $path ] ) )
        {// Value found
            // (Getting the value)
            $route = new self( $method, $path );
        }
        else
        {// Value not found
            if ( self::$targets[ $method ] )
            {// Value found
                foreach ( self::$targets[ $method ] as $defined_path => $defined_target )
                {// Processing each entry
                    if ( $defined_path[0] === '/' && $defined_path[ strlen( $defined_path ) - 1 ] === '/' )
                    {// (Path is a regex)
                        if ( preg_match( $defined_path, $path, $matches ) === 1 )
                        {// Match OK
                            // (Getting the value)
                            $route = new self( $method, $defined_path, $params );

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
                            $route = new self( $method, $defined_path );

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
                                    break 2;
                                }
                            }
                        }



                        if ( $params )
                        {// Value found
                            // (Getting the value)
                            $route = new self( $method, $defined_path, $params );

                            // Breaking the iteration
                            break;
                        }
                    }
                }
            }
        }



        // Returning the value
        return $route;
    }



    public function run () : self
    {
        foreach ( $this->middlewares as $middleware )
        {// Processing each entry
            // (Getting the value)
            $middleware = new $middleware();

            if ( $middleware->run() === false )
            {// (Middleware lock received)
                // Returning the value
                return $this;
            }
        }



        // (Getting the value)
        $target = self::$targets[ $this->method ][ $this->path ];

        if ( is_array( $target ) )
        {// (Target is an array)
            // (Getting the value)
            call_user_func_array( [ new $target[0], $target[1] ], $this->params );
        }
        else
        {// (Target is a function)
            // (Calling the function)
            $target();
        }



        // Returning the value
        return $this;
    }



    public function __toString () : string
    {
        // Returning the value
        return "{$this->method} {$this->path}";
    }
}



?>