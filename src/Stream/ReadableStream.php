<?php



namespace Solenoid\X\Stream;



class ReadableStream
{
    private $stream;



    public readonly string $file_path;



    public function __construct (string $file_path)
    {
        // (Getting the value)
        $this->file_path = $file_path;
    }



    public function open () : self|false
    {
        // (Opening the stream)
        $stream = fopen( $this->file_path, 'r' );

        if ( $stream === false )
        {// (Unable to open the stream)
            // Returning the value
            return false;
        }



        // (Getting the value)
        $this->stream = $stream;



        // Returning the value
        return $this;
    }

    public function close () : self|false
    {
        if ( !fclose( $this->stream ) )
        {// (Unable to close the stream)
            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }



    public function ended () : bool
    {
        // Returning the value
        return feof( $this->stream );
    }

    public function read (?int $length = null) : string|false
    {
        if ( $length === null )
        {// Value not found
            // (Getting the value)
            $length = filesize( $this->file_path );

            if ( $length === false )
            {// (Unable to get the file size)
                // Returning the value
                return false;
            }
        }



        // (Getting the value)
        $buffer = fread( $this->stream, $length );

        if ( $buffer === false )
        {// (Unable to read the stream)
            // Returning the value
            return false;
        }



        // Returning the value
        return $buffer;
    }



    public function __toString () : string
    {
        // (Opening the stream)
        $this->open();



        // (Getting the value)
        $buffer = $this->read();



        // (Closing the stream)
        $this->close();



        if ( $buffer === false )
        {// (Unable to read the stream)
            // Returning the value
            return '';
        }



        // Returning the value
        return $buffer;
    }
}



?>