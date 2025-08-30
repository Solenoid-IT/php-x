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

    public function make (string $abstract)
    {
        if ( isset( $this->instances[$abstract] ) )
        {// Value found
            // Returning the value
            return $this->instances[$abstract];
        }



        if ( isset( $this->bindings[$abstract] ) )
        {// Value found
            // (Getting the values)
            $binding  = $this->bindings[$abstract];
            $concrete = $binding['concrete'];



            // (Getting the value)
            $object = is_callable( $concrete ) ? $concrete($this) : $this->build( $concrete );

            if ( $binding['singleton'] )
            {// Value found
                // (Getting the value)
                $this->instances[$abstract] = $object;
            }



            // Returning the value
            return $object;
        }



        // Returning the value
        return $this->build( $abstract );
    }



    protected function build (string $class)
    {
        // (Getting the value)
        $reflection = new \ReflectionClass( $class );

        if ( !$reflection->isInstantiable() )
        {// (Class is not instantiable)
            // Throwing an exception
            throw new \Exception( "Classe non instanziabile: $class" );
        }



        // (Getting the value)
        $constructor = $reflection->getConstructor();

        if ( !$constructor )
        {// Value not found
            // Returning the value
            return new $class();
        }



        // (Setting the value)
        $dependencies = [];

        foreach ( $constructor->getParameters() as $param )
        {// Processing each entry
            // (Getting the value)
            $type = $param->getType();

            if ( $type && !$type->isBuiltin() )
            {// Match OK
                // (Appending the value)
                $dependencies[] = $this->make( $type->getName() );
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
                throw new \Exception( "Parametro non risolvibile: {$param->getName()}" );
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

            if ( $type->isBuiltin() )
            {// (Param is a primitive type)
                // (Getting the value)
                $param = $params[ $params ? ( $params[0] ? $i : $param->getName() ) : null ] ?? null;
            }
            else
            {// (Param is an instance of a class)
                // (Getting the value)
                $param = $this->make( $type->getName() );
            }



            // (Appending the value)
            $args[] = $param;
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

            if ( $type->isBuiltin() )
            {// (Param is a primitive type)
                // (Getting the value)
                $param = $params[ $params ? ( $params[0] ? $i : $param->getName() ) : null ] ?? null;
            }
            else
            {// (Param is an instance of a class)
                // (Getting the value)
                $param = $this->make( $type->getName() );
            }



            // (Appending the value)
            $args[] = $param;
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

            if ( $type->isBuiltin() )
            {// (Param is a primitive type)
                // (Getting the value)
                $param = $params[ $params ? ( $params[0] ? $i : $param->getName() ) : null ] ?? null;
            }
            else
            {// (Param is an instance of a class)
                // (Getting the value)
                $param = $this->make( $type->getName() );
            }



            // (Appending the value)
            $args[] = $param;
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