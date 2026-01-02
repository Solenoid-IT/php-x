<?php



namespace Solenoid\X\Input;



abstract class Value
{
    protected string $error = '';



    public function __construct (public readonly string $name, public readonly bool $required = true, public readonly string $description = '') {}



    abstract public function validate (mixed $value) : bool;



    public function get_error () : string
    {
        // Returning the value
        return $this->error;
    }
}



?>