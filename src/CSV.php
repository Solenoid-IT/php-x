<?php



namespace Solenoid\X;



class CSV
{
    public readonly string $separator;
    public readonly string $enclosure;
    public readonly string $escape;
    public readonly string $eol;



    public function __construct (string $separator = ';', string $enclosure = '"', string $escape = "\\", string $eol = "\n")
    {
        // (Getting the values)
        $this->separator = $separator;
        $this->enclosure = $enclosure;
        $this->escape    = $escape;
        $this->eol       = $eol;
    }



    public function parse (string $content, bool $header = false) : array
    {
        // (Setting the value)
        $records = [];



        // (Setting the value)
        $columns = [];



        // (Getting the value)
        $lines = explode( $this->eol, $content );

        foreach ( $lines as $line )
        {// Processing each entry
            if ( $line === '' ) continue;



            if ( $header )
            {// Value is true
                if ( $columns )
                {// Value found
                    // (Setting the value)
                    $record = [];



                    // (Getting the value)
                    $rows = str_getcsv( $line, $this->separator, $this->enclosure, $this->escape );

                    foreach ( $rows as $i => $value )
                    {// Processing each entry
                        // (Getting the value)
                        $record[ $columns[ $i ] ] = $value;
                    }



                    // (Appending the value)
                    $records[] = $record;
                }
                else
                {// Value not found
                    // (Getting the value)
                    $columns = str_getcsv( $line, $this->separator, $this->enclosure, $this->escape );
                }
            }
            else
            {// Value is false
                // (Appending the value)
                $records[] = str_getcsv( $line, $this->separator, $this->enclosure, $this->escape );
            }
        }



        // Returning the value
        return $records;
    }

    public function build (array $records) : string
    {
        // (Getting the value)
        $stream = fopen( 'php://temp', 'r+' );



        switch ( gettype( array_keys( $records[0] )[0] ) )
        {
            case 'string':
                // (Appending the content)
                fputcsv( $stream, array_keys( $records[0] ), $this->separator, $this->enclosure, $this->escape, $this->eol );

                foreach ( $records as $record )
                {// Processing each entry
                    // (Appending the value)
                    fputcsv( $stream, array_values( $record ), $this->separator, $this->enclosure, $this->escape, $this->eol );
                }
            break;

            case 'integer':
                foreach ( $records as $row )
                {// Processing each entry
                    // (Appending the value)
                    fputcsv( $stream, array_values( $row ), $this->separator, $this->enclosure, $this->escape, $this->eol );
                }
            break;
        }



        // (Rewinding the stream)
        rewind( $stream );



        // (Getting the value)
        $content = stream_get_contents( $stream );



        // (Closing the stream)
        fclose( $stream );



        // Returning the value
        return $content;
    }
}



?>