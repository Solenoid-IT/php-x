<?php



namespace Solenoid\X\HTTP;



use \Solenoid\X\JBIN;
use \Solenoid\X\Stream\ReadableStream;

use \Solenoid\X\HTTP\Client\Sender;
use \Solenoid\X\HTTP\Client\Result;



class Request
{
    public readonly string         $method;
    public readonly string         $path;
    public readonly string         $protocol;
    public readonly array          $headers;
    public readonly ReadableStream $body;



    public function __construct (string $method, string $path, string $protocol = 'HTTP/1.1', array $headers = [], string|ReadableStream $body = '')
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

    public static function parse (string $request) : self
    {
        // (Setting the value)
        $line_separator = "\r\n";



        // (Getting the values)
        [ $head, $body ] = explode( $line_separator . $line_separator, $request, 2 );

        // (Getting the value)
        $headers = explode( $line_separator, $head );

        // (Getting the values)
        [ $method, $path, $protocol ] = explode( ' ', $headers[0], 3 );



        // (Setting the value)
        $real_headers = [];

        for ( $i = 1; $i < count( $headers ); $i++ )
        {// Iterating each index
            // (Appending the value)
            $real_headers[] = $headers[$i];
        }



        // Returning the value
        return new self( $method, $path, $protocol, $real_headers, $body );
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



    public function buffer () : string
    {
        // (Opening the stream)
        $this->body->open();



        // (Getting the value)
        $buffer = $this->body->read();



        // (Closing the stream)
        $this->body->close();



        // Returning the value
        return $buffer;
    }

    public function text () : string
    {
        // Returning the value
        return $this->buffer();
    }

    public function json (bool $associative = false) : mixed
    {
        // Returning the value
        return json_decode( $this->body, $associative );
    }

    public function jbin () : JBIN
    {
        // Returning the value
        return JBIN::get();
    }

    public function multipart (?array $options = null) : array
    {
        /*

        // Returning the value
        return request_parse_body( $options );

        */



        // (Setting the value)
        $result = [];



        // (Getting the value)
        $result[0] = $_POST;



        // (Setting the value)
        $result[1] = [];

        foreach ( $_FILES as $id => $object )
        {// Processing each entry
            foreach ( $object['tmp_name'] as $i => $v )
            {// Processing each entry
                // (Getting the value)
                $r =
                [
                    'remote_path' => $object['full_path'][ $i ],
                    'tmp_path'    => $object['tmp_name'][ $i ],
                    'size'        => $object['size'][ $i ],
                    'type'        => $object['type'][ $i ],
                    'error'       => $object['error'][ $i ]
                ]
                ;

                // (Appending the value)
                $result[1][ $id ][] = $r;
            }
        }



        // Returning the value
        return $result;
    }



    public function send (string $url, int $conn_timeout = 60, int $exec_timeout = 60, int $max_redirs = 10) : Result|false
    {
        // Returning the value
        return ( new Sender( $conn_timeout, $exec_timeout, $max_redirs ) )->send( $this, $url );
    }



    public function __toString () : string
    {
        // Returning the value
        return "$this->method $this->path $this->protocol" . "\r\n" . implode( "\r\n", $this->headers ) . "\r\n\r\n" . $this->body;
    }
}



?>