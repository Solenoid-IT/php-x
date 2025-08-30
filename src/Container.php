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
}



?>