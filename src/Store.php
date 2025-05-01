<?php



namespace Solenoid\X;



class Store
{
    private static array $items = [];



    public readonly string $name;



    public function __construct (string $name)
    {
        // (Getting the value)
        $this->name = $name;
    }



    public function get (string $key) : mixed
    {
        // (Getting the value)
        return self::$items[ $this->name ][ $key ] ?? null;
    }

    public function set (string $key, mixed $value) : self
    {
        // (Getting the value)
        self::$items[ $this->name ][ $key ] = $value;



        // Returning the value
        return $this;
    }
}



?>