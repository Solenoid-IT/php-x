<?php



namespace Solenoid\X\CLI;



readonly class Action
{
    public function __construct (public string $class, public string $method = 'run') {}



    public function __toString () : string
    {
        // Returning the value
        return $this->class . '::' . $this->method . '()';
    }
}



?>