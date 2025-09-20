<?php



namespace Solenoid\X;



class TempFile
{
    public readonly string $path;



    public function __construct (?string $folder_path = null, string $prefix = 'tmp_')
    {
        // (Getting the value)
        $path = tempnam( $folder_path ?? sys_get_temp_dir(), $prefix );

        if ( !$path )
        {// (Unable to create the temp file)
            // Throwing an exception
            throw new \Exception( 'Unable to create the temp file' );
        }



        // (Getting the value)
        $this->path = $path;
    }



    public function put (string $src_path = 'php://input') : bool
    {
        // (Opening the input stream)
        $input_stream = fopen( $src_path, 'rb' );

        if ( !$input_stream )
        {// (Unable to open the input stream)
            // Throwing an exception
            throw new \Exception( 'Unable to open the input stream' );
        }



        // (Opening the output stream)
        $output_stream = fopen( $this->path, 'wb' );

        if ( !$output_stream )
        {// (Unable to open the output stream)
            // (Closing the input stream)
            fclose( $input_stream );

            // Throwing an exception
            throw new \Exception( 'Unable to open the output stream' );
        }



        if ( !stream_copy_to_stream( $input_stream, $output_stream ) )
        {// (Unable to copy the content)
            // (Closing the input stream)
            fclose( $input_stream );

            // (Closing the output stream)
            fclose( $output_stream );

            // Throwing an exception
            throw new \Exception( 'Unable to copy the content' );
        }



        if ( !fclose( $input_stream ) )
        {// (Unable to close the input stream)
            // (Closing the output stream)
            fclose( $output_stream );

            // Throwing an exception
            throw new \Exception( 'Unable to close the input stream' );
        }



        if ( !fclose( $output_stream ) )
        {// (Unable to close the output stream)
            // Throwing an exception
            throw new \Exception( 'Unable to close the output stream' );
        }



        // Returning the value
        return true;
    }



    public function __destruct ()
    {
        // (Removing the file)
        unlink( $this->path );
    }



    public function __toString () : string
    {
        // Returning the value
        return $this->path;
    }
}



?>