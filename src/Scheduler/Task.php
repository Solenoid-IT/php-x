<?php



namespace Solenoid\X\Scheduler;



class Task
{
    public readonly string $class;
    public readonly array  $args;

    public bool  $enabled = false;
    public array $rules   = [];

    public int   $max_num_concurrencies = 1;



    public function __construct (string $class, array $args = [])
    {
        // (Getting the values)
        $this->class = $class;
        $this->args  = $args;
    }



    public function add_rule (string $rule) : self
    {
        // (Appending the value)
        $this->rules[] = $rule;



        // Returning the value
        return $this;
    }



    public function __toString ()
    {
        // Returning the value
        return $this->class . ( $this->args ? ' ' . implode( ' ', $this->args ) : '' );
    }
}



?>