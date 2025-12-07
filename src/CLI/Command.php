<?php



namespace Solenoid\X\CLI;



use \Solenoid\X\Container;
use \Solenoid\X\Stream\ReadableStream;



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



    public function run (Container $container) : mixed
    {
        // (Calling the function)
        return $container->run_class_fn( $this->class, $this->method, $this->args );
    }



    public function stdin () : ReadableStream
    {
        // Returning the value
        return ( new ReadableStream( ReadableStream::TYPE_FILE ) )->set_file( 'php://stdin' );
    }



    public function buffer () : string|false
    {
        // (Getting the value)
        $stream = $this->stdin();

        if ( $stream->open() === false )
        {// (Unable to open the stream)
            // Returning the value
            return false;
        }



        // (Getting the value)
        $buffer = $stream->read();

        if ( $buffer === false )
        {// (Unable to read the stream)
            // Returning the value
            return false;
        }



        if ( $stream->close() === false )
        {// (Unable to close the stream)
            // Returning the value
            return false;
        }



        // Returning the value
        return $buffer;
    }

    public function json (bool $associative = false) : array|false
    {
        // Returning the value
        return json_decode( $this->buffer(), $associative );
    }



    public function __toString () : string
    {
        // Returning the value
        return "{$this->class}::{$this->method}()";
    }
}



?>