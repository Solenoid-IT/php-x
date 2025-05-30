<?php



namespace Solenoid\X\CLI;



class Command
{
    public readonly string $file_path;
    public readonly string $class;
    public readonly string $method;
    public readonly array  $args;



    public function __construct (array $argv, string $prefix = '/App/Tasks')
    {
        if ( count( $argv ) < 2 )
        {// (There are not enough arguments)
            // (Closing the process)
            die( "Usage: php {$argv[0]} {class} ...{args}\n" );
        }



        // (Getting the values)
        $this->file_path       = $argv[0];
        $class                 = $argv[1];
        $this->args            = array_slice( $argv, 2 );



        // (Getting the value)
        $this->class = str_replace( '/', '\\', "$prefix/$class" );



        // (Setting the value)
        $this->method = 'run';
    }



    public function run () : mixed
    {
        // (Calling the function)
        return call_user_func_array( [ new ($this->class)(), $this->method ], $this->args );
    }



    public function __toString () : string
    {
        // Returning the value
        return "{$this->class}::{$this->method}()";
    }
}



?>