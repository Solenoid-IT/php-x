<?php



namespace Solenoid\X;



class Error extends \Exception
{
    public int    $code;
    public string $description;

    public string $name;
    public int    $http_code;



    public function __construct (int $code, string $description = '')
    {
        // (Getting the values)
        $this->code        = $code;
        $this->description = $description;
    }



    public function set_name (string $value) : self
    {
        // (Getting the value)
        $this->name = $value;



        // Returning the value
        return $this;
    }

    public function set_http_code (int $value) : self
    {
        // (Getting the value)
        $this->http_code = $value;



        // Returning the value
        return $this;
    }



    public function __toString () : string
    {
        // (Getting the value)
        $location = "\nThrown in " . $this->getFile() . " on line " . $this->getLine();



        // (Getting the value)
        $stack_trace = "\nStack trace:\n" . $this->getTraceAsString();



        // Returning the value
        return 'Error ' . implode( ' :: ', [ $this->code, isset( $this->name ) ? $this->name : '', $this->description ] ) . $location . $stack_trace;
    }
}



?>