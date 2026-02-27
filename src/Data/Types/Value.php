<?php



namespace Solenoid\X\Data\Types;



abstract class Value
{
    protected string $error = '';
    protected mixed  $value = null;



    public function __construct (public readonly string $name, public readonly bool $required = true, public readonly string $description = '') {}



    abstract public function validate (mixed $value) : bool;



    public function get_error () : string|null
    {
        // Returning the value
        return $this->error === '' ? null : $this->error;
    }

    public function get_value () : mixed
    {
        // Returning the value
        return $this->value;
    }
}



?>