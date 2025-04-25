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



    public function __construct (string $method, string $path, string $protocol = 'HTTP/1.1', array $headers = [], string|ReadableStream $body)
    {
        // (Getting the values)
        $this->method   = $method;
        $this->path     = $path;
        $this->protocol = $protocol;
        $this->headers  = $headers;
        $this->body     = is_string( $body ) ? ( new ReadableStream( ReadableStream::TYPE_STRING ) )->set_content( $body ) : $body;
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
        return new self( $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $_SERVER['SERVER_PROTOCOL'], $headers, ( new ReadableStream( ReadableStream::TYPE_FILE ) )->set_file( 'php://input' ) );
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



    public function get_cookies () : array|false
    {
        // (Getting the value)
        $value = $this->get_header( 'Cookie' );

        if ( $value === false )
        {// (Header not found)
            // Returning the value
            return false;
        }



        // (Setting the value)
        $cookies = [];

        foreach ( explode( ';', $value ) as $cookie )
        {// Processing each entry
            // (Getting the values)
            [ $name, $value ] = explode( '=', $cookie, 2 );

            // (Getting the value)
            $cookies[ urldecode( $name ) ] = urldecode( $value );
        }



        // Returning the value
        return $cookies;
    }



    public function json (bool $associative = false) : mixed
    {
        // Returning the value
        return json_decode( $this->body, $associative );
    }



    public function __toString () : string
    {
        // Returning the value
        return "$this->method $this->path $this->protocol" . "\r\n" . implode( "\r\n", $this->headers ) . "\r\n\r\n" . $this->body;
    }
}



?>