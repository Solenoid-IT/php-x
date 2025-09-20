<?php



namespace Solenoid\X;



class TempFile
{
    public readonly string $path;



    public function __construct (?string $folder_path = null, string $prefix = 'tmp_')
    {
        // (Getting the value)
        $this->path = $folder_path ?? sys_get_temp_dir() . '/' . $prefix . bin2hex( random_bytes( 32 / 2 ) );
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



        if ( !stream_copy_to_stream( $input_stream, $output_stream ) )
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

    public function delete () : self|false
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
        // (Deleting the file)
        $this->delete();
    }



    public function __toString () : string
    {
        // Returning the value
        return $this->path;
    }
}



?>