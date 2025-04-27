<?php



namespace Solenoid\X\HTTP\Client;



class Error
{
    public readonly int    $code;
    public readonly string $message;
    


    public function __construct (int $code, string $message)
    {
        // (Getting the values)
        $this->code    = $code;
        $this->message = $message;
    }



    public function __toString () : string
    {
        // Returning the value
        return $this->code . ' ' . $this->message;
    }
}



?>