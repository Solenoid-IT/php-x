<?php



namespace Solenoid\X\HTTP\Server;



use Solenoid\X\Stream\ReadableStream;



class Request
{
    public readonly string         $method;
    public readonly string         $path;
    public readonly string         $protocol;
    public readonly array          $headers;
    public readonly ReadableStream $body;



    public function __construct (string $method, string $path, string $protocol = 'HTTP/1.1', array $headers = [], ReadableStream $body)
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
        // (Setting the value)
        $headers = [];

        foreach ( getallheaders() as $name => $value )
        {// Processing each entry
            // (Appending the value)
            $headers[] = "$name: $value";
        }



        // Returning the value
        return new self( $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $_SERVER['SERVER_PROTOCOL'], $headers, new ReadableStream( 'php://input' ) );
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
        return implode( "\r\n", [ "$this->method $this->path $this->protocol", implode( "\r\n", $this->headers ), "\r\n", (string) $this->body ] );        
    }
}



?>