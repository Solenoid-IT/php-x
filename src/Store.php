<?php



namespace Solenoid\X;



class Store
{
    private array $items = [];



    public function get (string $key) : mixed
    {
        // (Getting the value)
        return $this->items[ $key ] ?? null;
    }

    public function set (string $key, mixed $value) : self
    {
        // (Getting the value)
        $this->items[ $key ] = $value;



        // Returning the value
        return $this;
    }
}



?>