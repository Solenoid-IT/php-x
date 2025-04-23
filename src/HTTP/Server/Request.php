<?php



namespace Solenoid\X\HTTP\Server;



class Request
{
    public readonly string $method;
    public readonly string $path;
    public readonly string $protocol;
    public readonly array  $headers;
    public readonly string $body;



    public function __construct (string $method, string $path, string $protocol = 'HTTP/1.1', array $headers = [], string $body = '')
    {
        // (Getting the values)
        $this->method   = $method;
        $this->path     = $path;
        $this->protocol = $protocol;
        $this->headers  = $headers;
        $this->body     = $body;
    }



    public static function fetch () : self
    {
        // Returning the value
        return new self( $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $_SERVER['SERVER_PROTOCOL'], getallheaders(), file_get_contents( 'php://input' ) );
    }



    public function get_header (string $name) : string|false
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
        return false;
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



    public function __toString () : string
    {
        // Returning the value
        return implode( "\r\n", [ "$this->method $this->path $this->protocol", implode( "\r\n", $this->headers ), "\r\n", $this->body ] );        
    }
}



?>