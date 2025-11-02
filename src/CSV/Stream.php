<?php



namespace Solenoid\X\CSV;



use \Solenoid\X\CSV\CSV;



class Stream
{
    private $output_stream;



    public int $length = 0;



    public readonly CSV    $csv;

    public readonly string $filename;
    public readonly string $charset;



    public function __construct (CSV $csv, string $filename, string $charset = 'utf-8')
    {
        // (Getting the value)
        $this->csv = $csv;



        // (Getting the values)
        $this->filename = $filename;
        $this->charset  = $charset;



        // (Getting the value)
        $this->output_stream = fopen( 'php://output', 'w' );
    }



    public function head () : self
    {
        // (Setting the headers)
        header( 'Content-Type: text/csv; charset=' . $this->charset );
        header( 'Content-Disposition: attachment; filename="' . $this->filename . '"' );



        // Returning the value
        return $this;
    }



    public function send (array $line = []) : self|false
    {
        // (Getting the value)
        $length = fputcsv( $this->output_stream, $line, $this->csv->separator, $this->csv->enclosure, $this->csv->escape, $this->csv->eol );

        if ( $length === false )
        {// (Unable to write to the stream)
            // Returning the value
            return false;
        }



        // (Incrementing the value)
        $this->length += $length;



        // Returning the value
        return $this;
    }



    public function close () : self
    {
        // (Closing the stream)
        fclose( $this->output_stream );



        // Returning the value
        return $this;
    }



    public function __destruct ()
    {
        // (Closing the stream)
        $this->close();
    }
}



?>