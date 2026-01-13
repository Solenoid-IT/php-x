<?php



namespace Solenoid\X;



class Container
{
    protected array $bindings  = [];
    protected array $instances = [];



    public function __construct () {}



    public function bind (string $abstract, callable|string $concrete, bool $singleton = false)
    {
        // (Getting the value)
        $this->bindings[ $abstract ] = compact( 'concrete', 'singleton' );
    }

    public function singleton (string $abstract, callable|string $concrete)
    {
        // (Binding the abstract)
        $this->bind( $abstract, $concrete, true );
    }

    public function make (string $abstract, array $params = [])
    {
        if ( isset( $params[ $abstract ] ) )
        {// Value found
            // Returning the value
            return $params[ $abstract ];
        }



        if ( isset( $this->instances[ $abstract ] ) )
        {// Value found
            // Returning the value
            return $this->instances[ $abstract ];
        }



        if ( isset( $this->bindings[ $abstract ] ) )
        {// Value found
            // (Getting the values)
            $binding  = $this->bindings[ $abstract ];
            $concrete = $binding['concrete'];



            // (Getting the value)
            $object = is_callable( $concrete ) ? $concrete( $this, $params ) : $this->build( $concrete, $params );

            if ( $binding['singleton'] )
            {// Value found
                // (Getting the value)
                $this->instances[ $abstract ] = $object;
            }



            // Returning the value
            return $object;
        }



        // Returning the value
        return $this->build( $abstract, $params );
    }



    protected function build (string $class, array $params = [])
    {
        // (Getting the value)
        $reflection = new \ReflectionClass( $class );

        if ( !$reflection->isInstantiable() )
        {// (Class is not instantiable)
            // Throwing an exception
            throw new \Exception( "Class $class is not instantiable" );
        }



        // (Getting the value)
        $constructor = $reflection->getConstructor();

        if ( !$constructor )
        {// Value not found
            // Returning the value
            return new $class();
        }



        // (Setting the value)
        $i = -1;



        // (Setting the value)
        $dependencies = [];

        foreach ( $constructor->getParameters() as $param )
        {// Processing each entry
            // (Incrementing the value)
            $i += 1;



            // (Getting the value)
            $type = $param->getType();

            if ( $type && !$type->isBuiltin() )
            {// Match OK
                // (Appending the value)
                $dependencies[] = $this->make( $type->getName(), $params );
            }
            else
            if ( isset( $params[ $param->getName() ] ) )
            {// (Param is provided by name)
                // (Appending the value)
                $dependencies[] = $params[ $param->getName() ];
            }
            else
            if ( isset( $params[ $i ] ) )
            {// (Param is provided by index)
                // (Appending the value)
                $dependencies[] = $params[ $i ];
            }
            else
            if ( $param->isDefaultValueAvailable() )
            {// (Default value is available)
                // (Appending the value)
                $dependencies[] = $param->getDefaultValue();
            }
            else
            {// (Default value is not available)
                // Throwing an exception
                throw new \Exception( "Unable to resolve parameter {$param->getName()}" );
            }
        }



        // Returning the value
        return $reflection->newInstanceArgs( $dependencies );
    }



    protected function resolve_params_by_function (callable $function, array $params = []) : array
    {
        // (Setting the value)
        $args = [];



        // (Setting the value)
        $i = -1;

        foreach ( ( new \ReflectionFunction( $function ) )->getParameters() as $param )
        {// Processing each entry
            // (Incrementing the value)
            $i += 1;



            // (Getting the value)
            $type = $param->getType();

            if ( $type === null )
            {// Value not found
                // (Getting the value)
                $param_name = $param->getName();

                if ( isset( $params[ $param_name ] ) )
                {// (Param is provided by name)
                    // (Getting the value)
                    $param_value = $params[ $param_name ];
                }
                else
                {// (Param not found)
                    // Continuing the iteration
                    continue;
                }
            }
            else
            {// Value found
                if ( $type->isBuiltin() )
                {// (Param is a primitive type)
                    // (Getting the value)
                    $param_value = $params[ $params ? ( isset( $params[0] ) ? $i : $param->getName() ) : null ] ?? null;
                }
                else
                {// (Param is an instance of a class)
                    // (Getting the value)
                    $param_value = $this->make( $type->getName(), $params );
                }
            }



            // (Appending the value)
            $args[] = $param_value;
        }



        // Returning the value
        return $args;
    }

    protected function resolve_params_by_class (string $class, array $params = []) : array
    {
        // (Setting the value)
        $args = [];



        // (Getting the value)
        $constructor = ( new \ReflectionClass( $class ) )->getConstructor();

        if ( !$constructor )
        {// Value not found
            // Returning the value
            return [];
        }



        // (Setting the value)
        $i = -1;

        foreach ( $constructor->getParameters() as $param )
        {// Processing each entry
            // (Incrementing the value)
            $i += 1;



            // (Getting the value)
            $type = $param->getType();

            if ( $type === null )
            {// Value not found
                // (Getting the value)
                $param_name = $param->getName();

                if ( isset( $params[ $param_name ] ) )
                {// (Param is provided by name)
                    // (Getting the value)
                    $param_value = $params[ $param_name ];
                }
                else
                {// (Param not found)
                    // Continuing the iteration
                    continue;
                }
            }
            else
            {// Value found
                if ( $type->isBuiltin() )
                {// (Param is a primitive type)
                    // (Getting the value)
                    $param_value = $params[ $params ? ( isset( $params[0] ) ? $i : $param->getName() ) : null ] ?? null;
                }
                else
                {// (Param is an instance of a class)
                    // (Getting the value)
                    $param_value = $this->make( $type->getName(), $params );
                }
            }



            // (Appending the value)
            $args[] = $param_value;
        }



        // Returning the value
        return $args;
    }

    protected function resolve_params_by_class_fn (string $class, string $fn, array $params = []) : array
    {
        // (Setting the value)
        $args = [];



        // (Getting the value)
        $method = ( new \ReflectionMethod( $class, $fn ) );

        if ( !$method )
        {// Value not found
            // Returning the value
            return [];
        }



        // (Setting the value)
        $i = -1;

        foreach ( $method->getParameters() as $param )
        {// Processing each entry
            // (Incrementing the value)
            $i += 1;



            // (Getting the value)
            $type = $param->getType();

            if ( $type === null )
            {// Value not found
                // (Getting the value)
                $param_name = $param->getName();

                if ( isset( $params[ $param_name ] ) )
                {// (Param is provided by name)
                    // (Getting the value)
                    $param_value = $params[ $param_name ];
                }
                else
                {// (Param not found)
                    // Continuing the iteration
                    continue;
                }
            }
            else
            {// Value found
                if ( $type->isBuiltin() )
                {// (Param is a primitive type)
                    // (Getting the value)
                    $param_value = $params[ $params ? ( isset( $params[0] ) ? $i : $param->getName() ) : null ] ?? null;

                    if ( $param_value === null && $param->isDefaultValueAvailable() )
                    {// (Default value is available)
                        // (Getting the value)
                        $param_value = $param->getDefaultValue();
                    }
                }
                else
                {// (Param is an instance of a class)
                    // (Getting the value)
                    $param_value = $this->make( $type->getName(), $params );
                }
            }



            // (Appending the value)
            $args[] = $param_value;
        }



        // Returning the value
        return $args;
    }



    public function run_function (callable $function, array $params = []) : mixed
    {
        // Returning the value
        return call_user_func_array( $function, $this->resolve_params_by_function( $function, $params ) );
    }

    public function run_class_fn (string $class, string $fn, array $params = []) : mixed
    {
        // (Getting the instance)
        $instance = ( new \ReflectionClass( $class ) )->newInstanceArgs( $this->resolve_params_by_class( $class, $params ) );



        // (Getting the value)
        return call_user_func_array( [ $instance, $fn ], $this->resolve_params_by_class_fn( $class, $fn, $params ) );
    }



    public function make_instance (string $class, array $params = []) : mixed
    {
        // (Getting the instance)
        return ( new \ReflectionClass( $class ) )->newInstanceArgs( $this->resolve_params_by_class( $class, $params ) );
    }

    public function run_instance_method (mixed $instance, string $method, array $params = []) : mixed
    {
        // (Getting the value)
        return call_user_func_array( [ $instance, $method ], $this->resolve_params_by_class_fn( get_class( $instance ), $method, $params ) );
    }
}



?>