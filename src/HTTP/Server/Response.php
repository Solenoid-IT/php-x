<?php



namespace Solenoid\X\HTTP\Server;



class Response
{
    private int    $code;
    private array  $headers;
    private        $body;



    public function __construct (int $code = 200, array $headers = [], string $body = '')
    {
        // (Getting the values)
        $this->code    = $code;
        $this->headers = $headers;
        $this->body    = function () use (&$body) { echo $body; };        
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



    public function add_header (string $value) : self
    {
        // (Appending the value)
        $this->headers[] = $value;



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



        // (Calling the function)
        ( $this->body )();



        // Returning the value
        return $this;
    }
}



?>