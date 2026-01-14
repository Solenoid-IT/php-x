<?php



namespace Solenoid\X;



use \Attribute;



#[ Attribute( Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE ) ]
class Middleware
{
    public string $name;



    public function __construct (string $name)
    {
        // (Getting the value)
        $this->name = $name;
    }
}



?>