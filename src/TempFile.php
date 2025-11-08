<?php



namespace Solenoid\X;



use Solenoid\X\Stream\ReadableStream;



class TempFile
{
    private bool $auto_remove;



    public readonly string $path;



    public function __construct (?string $folder_path = null, string $prefix = 'tmp_', bool $auto_remove = true)
    {
        // (Getting the values)
        $this->path        = ( $folder_path ?? sys_get_temp_dir() ) . '/' . $prefix . bin2hex( random_bytes( 32 / 2 ) );
        $this->auto_remove = $auto_remove;



        // (Getting the value)
        $dir = dirname( $this->path );

        if ( !is_dir( $dir ) )
        {// (Directory not found)
            if ( !mkdir( $dir, 0777, true ) )
            {// (Unable to create the folder)
                // Throwing an exception
                throw new \Exception( "Unable to create the folder '$dir'" );
            }
        }



        if ( !touch( $this->path ) )
        {// (Unable to create the file)
            // Throwing an exception
            throw new \Exception( "Unable to create the file '$this->path'" );
        }
    }



    public function put (string $src_path = 'php://input') : self|false
    {
        // (Opening the input stream)
        $input_stream = fopen( $src_path, 'rb' );

        if ( !$input_stream )
        {// (Unable to open the input stream)
            // Throwing an exception
            throw new \Exception( 'Unable to open the input stream' );

            // Returning the value
            return false;
        }



        // (Opening the output stream)
        $output_stream = fopen( $this->path, 'wb' );

        if ( !$output_stream )
        {// (Unable to open the output stream)
            // (Closing the input stream)
            fclose( $input_stream );

            // Throwing an exception
            throw new \Exception( 'Unable to open the output stream' );

            // Returning the value
            return false;
        }



        if ( stream_copy_to_stream( $input_stream, $output_stream ) === false )
        {// (Unable to copy the content)
            // (Closing the input stream)
            fclose( $input_stream );

            // (Closing the output stream)
            fclose( $output_stream );

            // Throwing an exception
            throw new \Exception( 'Unable to copy the content' );

            // Returning the value
            return false;
        }



        if ( !fclose( $input_stream ) )
        {// (Unable to close the input stream)
            // (Closing the output stream)
            fclose( $output_stream );

            // Throwing an exception
            throw new \Exception( 'Unable to close the input stream' );

            // Returning the value
            return false;
        }



        if ( !fclose( $output_stream ) )
        {// (Unable to close the output stream)
            // Throwing an exception
            throw new \Exception( 'Unable to close the output stream' );

            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }

    public function put_stream (ReadableStream $stream) : self|false
    {
        // Returning the value
        return stream_copy_to_stream( $stream->get_resource(), $this->path );
    }



    public function remove () : self|false
    {
        if ( !unlink( $this->path ) )
        {// (Unable to remove the file)
            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }



    public function __destruct ()
    {
        if ( $this->auto_remove )
        {// Value is true
            // (Removing the file)
            $this->remove();
        }
    }



    public function __toString () : string
    {
        // Returning the value
        return $this->path;
    }
}



?>