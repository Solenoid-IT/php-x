<?php



namespace Solenoid\X\CLI;



use \Solenoid\X\Container;
use \Solenoid\X\Stream\ReadableStream;



class Command
{
    private array $callbacks = [];

    private $acquire_mutex = null;



    public readonly string $file_path;
    public readonly string $class;
    public readonly string $method;
    public readonly array  $args;



    public function __construct (array $argv, string $prefix = '\\App\\Tasks')
    {
        if ( count( $argv ) < 2 )
        {// (There are not enough arguments)
            // (Closing the process)
            die( "Usage: php {$argv[0]} {classpath.method} ...{args}\n" );
        }



        // (Getting the values)
        $this->file_path       = $argv[0];
        [ $class, $method ]    = explode( '.', $argv[1], 2 );
        $this->args            = array_slice( $argv, 2 );



        // (Getting the value)
        $class = str_replace( '/', '\\', $class );

        if ( str_starts_with( $class, $prefix ) )
        {// Match OK
            // (Getting the value)
            $class = substr( $class, strlen( $prefix ) );
        }



        // (Getting the value)
        $this->class = $prefix . '\\' . $class;



        // (Getting the value)
        $this->method = $method ?? 'run';
    }



    public function run (Container $container) : mixed
    {
        // (Getting the value)
        $mutex = Mutex::find( $this->class, $this->method );

        if ( $mutex ) $this->emit( 'mutex-lock' );



        try
        {
            if ( $mutex )
            {// Value found
                if ( $this->acquire_mutex )
                {// Value found
                    // (Getting the value)
                    $pid = ( $this->acquire_mutex )();

                    if ( $pid ) return 'mutex:locked';
                }
            }



            // (Calling the function)
            return $container->run_class_fn( $this->class, $this->method, $this->args );
        }
        catch (\Throwable $e)
        {
            // Throwing the exception
            throw $e;
        }
        finally
        {
            if ( $mutex ) $this->emit( 'mutex-unlock' );
        }
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



    public function emit (string $event, ...$args) : self
    {
        // (Getting the value)
        $callbacks = $this->callbacks[ $event ] ?? [];

        foreach ( $callbacks as $callback )
        {// Processing each entry
            // (Calling the function)
            $callback( ...$args );
        }



        // Returning the value
        return $this;
    }

    public function on (string $event, callable $callback) : self
    {
        // (Appending the value)
        $this->callbacks[ $event ][] = $callback;



        // Returning the value
        return $this;
    }



    public function set_mutex_reader (callable $function) : self
    {
        // (Getting the value)
        $this->acquire_mutex = $function;



        // Returning the value
        return $this;
    }



    public function __toString () : string
    {
        // Returning the value
        return "{$this->class}::{$this->method}()";
    }
}



?>