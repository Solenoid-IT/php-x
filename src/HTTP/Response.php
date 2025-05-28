<?php



namespace Solenoid\X\HTTP;



class Response
{
    private int   $code;
    private array $headers;
    private       $body;



    public function __construct (int $code = 200, array $headers = [], string|callable $body = '')
    {
        // (Getting the values)
        $this->code    = $code;
        $this->headers = $headers;
        $this->body    = is_string( $body ) ? $body : function () use (&$body) { echo $body; };        
    }



    public function stream (int $code = 200, array $headers = [], callable $function) : self
    {
        // (Getting the value)
        $this->code = $code;



        foreach ( $headers as $header )
        {// Processing each entry
            // (Appending the value)
            $this->headers[] = $header;
        }



        // (Getting the value)
        $this->body = $function;



        // Returning the value
        return $this;
    }



    public function text (int $code = 200, string $content) : self
    {
        // (Getting the value)
        $this->code = $code;



        // (Appending the values)
        $this->headers[] = 'Content-Type: text/plain';
        $this->headers[] = 'Content-Length: ' . strlen( $content );



        // (Getting the value)
        $this->body = function () use (&$content) { echo $content; };



        // Returning the value
        return $this;
    }

    public function json (int $code = 200, mixed $content) : self
    {
        // (Getting the value)
        $this->code = $code;



        // (Getting the value)
        $content = json_encode( $content );



        // (Appending the values)
        $this->headers[] = 'Content-Type: application/json';
        $this->headers[] = 'Content-Length: ' . strlen( $content );



        // (Getting the value)
        $this->body = function () use (&$content) { echo $content; };



        // Returning the value
        return $this;
    }

    public function csv (int $code = 200, mixed $content) : self
    {
        // (Getting the value)
        $this->code = $code;



        if ( !is_string( $content ) )
        {// Match failed
            // (Getting the value)
            $stream = fopen( 'php://temp', 'r+' );



            // (Setting the values)
            $column_separator = ';';
            $enclosure        = '"';
            $escape           = "\\";
            $eol              = "\n";



            switch ( gettype( array_keys( $content[0] )[0] ) )
            {
                case 'string':
                    // (Appending the content)
                    fputcsv( $stream, array_keys( $content[0] ), $column_separator, $enclosure, $escape, $eol );

                    foreach ( $content as $record )
                    {// Processing each entry
                        // (Appending the value)
                        fputcsv( $stream, array_values( $record ), $column_separator, $enclosure, $escape, $eol );
                    }
                break;

                case 'integer':
                    foreach ( $content as $row )
                    {// Processing each entry
                        // (Appending the value)
                        fputcsv( $stream, array_values( $row ), $column_separator, $enclosure, $escape, $eol );
                    }
                break;
            }



            // (Rewinding the stream)
            rewind( $stream );



            // (Getting the value)
            $content = stream_get_contents( $stream );



            // (Closing the stream)
            fclose( $stream );
        }



        // (Appending the value)
        $this->headers[] = 'Content-Type: text/csv';
        $this->headers[] = 'Content-Length: ' . strlen( $content );



        // (Getting the value)
        $this->body = function () use (&$content) { echo $content; };



        // Returning the value
        return $this;
    }



    public function set_code (int $code) : self
    {
        // (Getting the value)
        $this->code = $code;



        // Returning the value
        return $this;
    }

    public function add_header (string $value) : self
    {
        // (Appending the value)
        $this->headers[] = $value;



        // Returning the value
        return $this;
    }



    public function get_headers () : array
    {
        // Returning the value
        return $this->headers;
    }



    public function redirect (int $code = 303, string $location) : self
    {
        // (Getting the value)
        $this->code = $code;



        // (Appending the value)
        $this->headers[] = "Location: $location";



        // Returning the value
        return $this;
    }



    public function send () : self
    {
        // (Setting the code)
        http_response_code( $this->code );



        foreach ( $this->headers as $header )
        {// Processing each entry
            // (Setting the header)
            header( $header );
        }



        if ( is_string( $this->body ) )
        {// Match OK
            // (Printing the value)
            echo $this->body;
        }
        else
        {// Match failed
            // (Calling the function)
            ( $this->body )();
        }



        // Returning the value
        return $this;
    }



    public function get_code () : int
    {
        // Returning the value
        return $this->code;
    }



    public function get_header (string $name) : string|null
    {
        foreach ( $this->headers as $header )
        {// Processing each entry
            // (Getting the values)
            [ $n, $v ] = explode( ': ', $header, 2 );

            if ( strtolower( $n ) === strtolower( $name ) )
            {// Match OK
                // Returning the value
                return $v;
            }
        }



        // Returning the value
        return null;
    }

    public function list_header (string $name) : array
    {
        // (Setting the value)
        $results = [];

        foreach ( $this->headers as $header )
        {// Processing each entry
            // (Getting the values)
            [ $n, $v ] = explode( ': ', $header, 2 );

            if ( strtolower( $n ) === strtolower( $name ) )
            {// Match OK
                // (Appending the value)
                $results[] = $v;
            }
        }



        // Returning the value
        return $results;
    }



    public function get_body () : string|callable
    {
        // Returning the value
        return $this->body;
    }
}



?>