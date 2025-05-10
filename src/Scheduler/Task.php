<?php



namespace Solenoid\X\Scheduler;



class Task
{
    public readonly string $id;
    public readonly string $fn;
    public readonly array  $args;

    public  bool $enabled = false;
    public array $rules   = [];



    public function __construct (string $id, string $fn = 'run', array $args = [])
    {
        // (Getting the values)
        $this->id   = $id;
        $this->fn   = $fn;
        $this->args = $args;
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
        return implode( ' ', [ $this->id . ':' . $this->fn, $this->args ] );
    }
}



?>