<?php



namespace Solenoid\X;



class Error extends \Exception
{
    public function __construct (int $code = 0, string $message = '', protected ?string $type = null, protected ?int $http_code = 500)
    {
        // (Calling the function)
        parent::__construct( $message, $code );
    }



    public function get_type () : string|null
    {
        // Returning the value
        return $this->type;
    }

    public function get_http_code () : int|null
    {
        // Returning the value
        return $this->http_code;
    }



    public function get_info () : array
    {
        // Returning the value
        return
        [
            'code'      => $this->code,
            'message'   => $this->message,
            'type'      => $this->type,
            'http_code' => $this->http_code
        ]
        ;
    }



    public function __toString () : string
    {
        // (Getting the value)
        $location = "\nThrown in " . $this->getFile() . " on line " . $this->getLine();



        // (Getting the value)
        $stack_trace = "\nStack trace:\n" . $this->getTraceAsString();



        // Returning the value
        return 'Error ' . implode( ' :: ', [ $this->code, $this->type, $this->message ] ) . $location . $stack_trace;
    }
}



?>