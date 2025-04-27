<?php



namespace Solenoid\X;



class Process
{
    private $resource;



    public readonly string $cmd;
    public readonly string $cwd;
    public readonly string $input;

    public readonly int $pid;

    public readonly string $output;
    public readonly string $error;

    public readonly int $exitcode;



    public function __construct (string $cmd)
    {
        // (Getting the value)
        $this->cmd = $cmd;
    }



    public function set_cwd (string $cwd) : self
    {
        // (Getting the value)
        $this->cwd = $cwd;



        // Returning the value)
        return $this;
    }

    public function set_input (string $input) : self
    {
        // (Getting the value)
        $this->input = $input;



        // Returning the value)
        return $this;
    }



    public function run () : self|false
    {
        // (Getting the value)
        $descriptor =
        [
            0 => [ 'pipe', 'r' ],# 'child STDIN'
            1 => [ 'pipe', 'w' ],# 'child STDOUT'
            2 => [ 'pipe', 'w' ],# 'child STDERR'
        ]
        ;



        // (Opening the process)
        $this->resource = proc_open( $this->cmd, $descriptor, $pipes, $this->cwd );

        if ( !$this->resource )
        {// (Unable to open the process)
            // Returning the value
            return false;
        }



        if ( fwrite( $pipes[0], $this->input ) === false )
        {// (Unable to write to the stream for child STDIN)
            // Returning the value
            return false;
        }

        if ( !fclose( $pipes[0] ) )
        {// (Unable to close the stream for child STDIN)
            // Returning the value
            return false;
        }



        /*

        // (Setting the stream as non blocking)
        stream_set_blocking( $pipes[1], false );

        */



        // (Getting the value)
        $this->output = stream_get_contents( $pipes[1] );

        if ( !fclose( $pipes[1] ) )
        {// (Unable to close the stream for child STDOUT)
            // Returning the value
            return false;
        }



        // (Getting the value)
        $this->error = stream_get_contents( $pipes[2] );

        if ( !fclose( $pipes[2] ) )
        {// (Unable to close the stream for child STDERR)
            // Returning the value
            return false;
        }



        // (Closing the process)
        $this->exitcode = proc_close( $this->resource );



        // Returning the value
        return $this;
    }

    public function start () : self|false
    {
        if ( isset( $this->cwd ) )
        {// Value found
            // (Getting the value)
            $cwd = getcwd();

            if ( !chdir( $this->cwd ) )
            {// (Unable to set the directory)
                // Returning the value
                return false;
            }
        }



        // (Setting the value)
        $input = '';

        if ( $this->input )
        {// Value found
            // (Getting the value)
            $tmp_file_path = tempnam( '/tmp', 'async_proc_' );

            if ( file_put_contents( $tmp_file_path, $this->input ) === false )
            {// (Unable to write to the file)
                // Returning the value
                return false;
            }



            // (Getting the value)
            $input = " < $tmp_file_path";
        }



        // (Getting the value)
        $this->pid = (int) trim( shell_exec( "nohup $this->cmd{$input} >/dev/null 2>&1 & echo $!" ) );



        if ( isset( $this->cwd ) )
        {// Value found
            if ( !chdir( $cwd ) )
            {// (Unable to set the directory)
                // Returning the value
                return false;
            }
        }



        if ( $this->input )
        {// Value found
            if ( !unlink( $tmp_file_path ) )
            {// (Unable to remove the file)
                // Returning the value
                return false;
            }
        }



        // Returning the value
        return $this;
    }



    public static function spawn (string $cmd, ?string $cwd = null, ?string $input = null) : self|false
    {
        // Returning the value
        return ( new self( $cmd ) )->set_cwd( $cwd )->set_input( $input )->start();
    }



    public function __toString () : string
    {
        // Returning the value
        return $this->cmd;
    }
}



?>