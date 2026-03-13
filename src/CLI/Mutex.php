<?php



namespace Solenoid\X\CLI;



use \Attribute;



#[ Attribute( Attribute::TARGET_METHOD ) ]
class Mutex
{
    public static function find (string $class, string $method) : self|null
    {
        foreach ( new \ReflectionMethod( $class, $method ) as $attribute )
        {// Processing each entry
            // Returning the value
            return $attribute->newInstance();
        }



        // Returning the value
        return null;
    }
}



?>