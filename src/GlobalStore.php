<?php



namespace Solenoid\X;



class GlobalStore
{
    private static array $items = [];



    public static function get (string $key) : mixed
    {
        // (Getting the value)
        return self::$items[ $key ] ?? null;
    }

    public static function set (string $key, mixed $value) : void
    {
        // (Getting the value)
        self::$items[ $key ] = $value;
    }
}



?>