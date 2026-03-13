<?php



namespace Solenoid\X;



readonly class Action
{
    public function __construct (public string $class, public string $method) {}



    public function __toString () : string
    {
        // Returning the value
        return str_replace( '\\', '/', $this->class ) . '.' . $this->method;
    }
}



?>