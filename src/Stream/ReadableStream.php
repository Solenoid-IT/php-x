<?php



namespace Solenoid\X\Stream;



class ReadableStream
{
    const TYPE_FILE   = 'file';
    const TYPE_STRING = 'string';



    private $stream;



    public readonly string $src_type;

    public readonly string $file_path;
    public readonly string $content;



    public function __construct (string $src_type = self::TYPE_FILE)
    {
        // (Getting the value)
        $this->src_type = $src_type;
    }



    public function set_file (string $file_path) : self|false
    {
        if ( $this->src_type !== self::TYPE_FILE ) return false;



        // (Getting the value)
        $this->file_path = $file_path;



        // Returning the value
        return $this;
    }

    public function set_content (string &$content) : self|false
    {
        if ( $this->src_type !== self::TYPE_STRING ) return false;



        // (Getting the value)
        $this->content = $content;



        // Returning the value
        return $this;
    }



    public function set_stream ($stream) : self
    {
        // (Getting the value)
        $this->stream = $stream;



        // Returning the value
        return $this;
    }



    public function open () : self|false
    {
        switch ( $this->src_type )
        {
            case self::TYPE_FILE:
                // (Opening the stream)
                $stream = fopen( $this->file_path, 'r' );
            break;

            case self::TYPE_STRING:
                // (Opening the stream)
                $stream = fopen( 'php://temp', 'r+' );
            break;
        }



        if ( $stream === false )
        {// (Unable to open the stream)
            // Returning the value
            return false;
        }



        switch ( $this->src_type )
        {
            case self::TYPE_FILE:
                // (Doing nothing)
            break;

            case self::TYPE_STRING:
                if ( fwrite( $stream, $this->content ) === false )
                {// (Unable to write to the stream)
                    // Returning the value
                    return false;
                }

                if ( !rewind( $stream ) )
                {// (Unable to rewind the stream)
                    // Returning the value
                    return false;
                }
            break;
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
            $buffer = stream_get_contents( $this->stream );
        }
        else
        {// Value found
            // (Getting the value)
            $buffer = fread( $this->stream, $length );
        }



        if ( $buffer === false )
        {// (Unable to read the stream)
            // Returning the value
            return false;
        }



        // Returning the value
        return $buffer;
    }



    public function get_resource ()
    {
        // Returning the value
        return $this->stream;
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