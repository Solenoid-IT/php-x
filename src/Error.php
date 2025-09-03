<?php



namespace Solenoid\X;



class Error
{
    public readonly int    $code;
    public readonly string $name;
    public readonly string $description;

    public readonly int    $http_code;



    public function __construct (int $code, string $name = '', string $description = '')
    {
        // (Getting the values)
        $this->code        = $code;
        $this->name        = $name;
        $this->description = $description;
    }



    public function set_http_code (int $code) : self
    {
        // (Getting the value)
        $this->http_code = $code;



        // Returning the value
        return $this;
    }



    public function __toString () : string
    {
        // Returning the value
        return "Error {$this->code} :: {$this->name} :: {$this->description}";
    }
}



?>