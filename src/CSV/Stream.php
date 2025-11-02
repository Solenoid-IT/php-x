<?php



namespace Solenoid\X\CSV;



use \Solenoid\X\CSV\CSV;



class Stream
{
    private $output_stream;



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
        if ( fputcsv( $this->output_stream, $line, $this->csv->separator, $this->csv->enclosure, $this->csv->escape, $this->csv->eol ) === false )
        {// (Unable to write to the stream)
            // Returning the value
            return false;
        }



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
        fclose( $this->output_stream );
    }
}



?>