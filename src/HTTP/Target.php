<?php



namespace Solenoid\X\HTTP;



class Target
{
    private array $middlewares = [];



    public $function;

    public string $class;
    public string $fn;



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



    public function add_middleware (string $class) : self
    {
        // (Adding the value)
        $this->middlewares[] = $class;

        // Returning the value
        return $this;
    }
}



?>