<?php



namespace Solenoid\X\CSV;



class Reader
{
    protected $stream;
    protected array $header = [];



    public function __construct (protected string $file_path, protected ?CSV $csv = null) {}



    public function open () : static|false
    {
        // (Opening the input stream)
        $this->stream = fopen( $this->file_path, 'r' );

        if ( $this->stream === false )
        {// (Unable to open the input stream)
            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }



    public function head () : static|null
    {
        // (Getting the value)
        $line = $this->read();

        if ( !$line ) return null;



        // (Getting the value)
        $this->header = $line;



        // Returning the value
        return $this;
    }

    public function read (int $length = 0) : array|null
    {
        while ( ( $line = fgetcsv( $this->stream, $length, $this->csv?->separator ?? ',', $this->csv?->enclosure ?? '"', $this->csv?->escape ?? '\\' ) ) !== false )
        {// Processing each entry
            if ( implode( '', $line ) === '' ) continue;



            if ( $this->header )
            {// Value found
                // (Getting the value)
                $line = array_combine( $this->header, $line );
            }



            // Returning the value
            return $line;
        }



        // Returning the value
        return null;
    }

    public function read_all (int $length = 0) : array
    {
        // (Setting the value)
        $lines = [];

        while ( ( $line = $this->read( $length ) ) !== null )
        {// Processing each entry
            // (Adding the value)
            $lines[] = $line;
        }



        // Returning the value
        return $lines;
    }



    public function close () : static|false
    {
        if ( !fclose( $this->stream ) )
        {// (Unable to close the input stream)
            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }



    public function fetch (bool $head = false) : array|false
    {
        if ( $this->open() === false )
        {// (Unable to open the input stream)
            // Returning the value
            return false;
        }



        if ( $head )
        {// Value is true
            // (Getting the header)
            $this->head();
        }



        // (Getting the value)
        $lines = $this->read_all();



        if ( !$this->close() )
        {// (Unable to close the input stream)
            // Returning the value
            return false;
        }



        // Returning the value
        return $lines;
    }



    public function __destruct ()
    {
        if ( isset( $this->stream ) && is_resource( $this->stream ) )
        {// (Closing the input stream)
            // (Closing the stream)
            fclose( $this->stream );
        }
    }
}



?>