<?php



namespace Solenoid\X;



class Error
{
    public readonly int    $code;
    public readonly string $message;
    public readonly string $description;



    public function __construct (int $code, string $message = '', string $description = '')
    {
        // (Getting the values)
        $this->code        = $code;
        $this->message     = $message;
        $this->description = $description;
    }



    public function __toString () : string
    {
        // Returning the value
        return "Error {$this->code} :: {$this->message} :: {$this->description}";
    }
}



?>