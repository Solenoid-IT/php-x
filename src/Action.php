<?php



namespace Solenoid\X;



class Action
{
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



    public function run () : mixed
    {
        // Returning the value
        return call_user_func_array( [ new $this->class_path(), $this->method ], [] );
    }



    public function __toString () : string
    {
        // Returning the value
        return "{$this->class}.{$this->method}";
    }
}



?>