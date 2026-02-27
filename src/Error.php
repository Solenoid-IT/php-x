<?php



namespace Solenoid\X;



class Error extends \Exception
{
    public function __construct (int $code = 0, string $message = '', protected ?string $type = null, protected ?int $http_code = 500, protected bool $exposed = false)
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



    public function is_exposed () : bool
    {
        // Returning the value
        return $this->exposed;
    }



    public function get_info () : array
    {
        // Returning the value
        return
        [
            'code'      => $this->code,
            'type'      => $this->type,
            'message'   => $this->message,
            'http_code' => $this->http_code
        ]
        ;
    }



    public function get_line (string $fn = 'error') : string|null
    {
        // (Getting the value)
        $steps = parent::getTrace();

        foreach ( $steps as $index => $step )
        {// Processing each entry
            if ( isset( $step['function'] ) && $step['function'] === $fn )
            {// Match OK
                // Returning the value
                return ( isset( $step['file'] ) ? $step['file'] : null ) . ':' . ( isset( $step['line'] ) ? $step['line'] : null );
            }
        }
        


        // Returning the value
        return null;
    }



    public function __toString () : string
    {
        // Returning the value
        return parent::__toString() . ' :: Error :: ' . implode( ' - ', [ $this->code, $this->type, $this->message, $this->http_code ? 'HTTP ' . $this->http_code : null ] );
    }
}



?>