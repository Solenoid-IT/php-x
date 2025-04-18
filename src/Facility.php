<?php



namespace Solenoid\X;



class Facility
{
    private static array $values = [];



    public static function get (string $name) : mixed
    {
        if ( !isset( self::$values[ $name ] ) )
        {// Value not found
            // Returning the value
            return false;
        }



        // Returning the value
        return self::$values[ $name ];
    }

    public static function set (string $name, mixed $value) : void
    {
        if ( isset( self::$values[ $name ] ) ) return;

        // (Getting the value)
        self::$values[ $name ] = $value;
    }
}



?>