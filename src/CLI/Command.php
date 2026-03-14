<?php



namespace Solenoid\X\CLI;



use \Solenoid\X\Container;
use \Solenoid\X\Stream\ReadableStream;



class Command
{
    private array $handlers = [];



    public readonly string $file_path;
    public readonly array  $args;

    public Task $task;



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
        $this->task = new Task( $prefix . '\\' . $class, $method ?? 'run' );
    }



    public function run (Container $container) : mixed
    {
        // (Setting the value)
        $mutex_locked = false;



        // (Getting the value)
        $mutex = $this->task->mutex();

        if ( $mutex )
        {// Value found
            if ( $this->handlers['mutex-pid']() ) return 'mutex:locked';



            // (Calling the function)
            $this->handlers['mutex-lock']();



            // (Setting the value)
            $mutex_locked = true;
        }
        


        try
        {
            // Returning the value
            return $this->task->run( $container, $this->args );
        }
        catch (\Throwable $e)
        {
            // Throwing the exception
            throw $e;
        }
        finally
        {
            if ( $mutex && $mutex_locked ) $this->handlers['mutex-unlock']();
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



    public function set_handlers (array $handlers) : self
    {
        // (Getting the value)
        $this->handlers = $handlers;



        // Returning the value
        return $this;
    }



    public function __toString () : string
    {
        // Returning the value
        return preg_replace( '/^\\/', '', (string) $this->task );
    }
}



?>