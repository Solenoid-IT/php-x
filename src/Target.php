<?php



namespace Solenoid\X;



class Target
{
    public $function;

    public readonly string $class;
    public readonly string $fn;



    public static function define (callable $function) : self
    {
        // (Getting the value)
        $target = new self();



        // (Getting the value)
        $target->function = $function;



        // Returning the value
        return $target;
    }

    public static function link (string $class, string $fn) : self
    {
        // (Getting the value)
        $target = new self();



        // (Getting the values)
        $target->class = $class;
        $target->fn    = $fn;



        // Returning the value
        return $target;
    }
}



?>