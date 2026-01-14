<?php



namespace Solenoid\X;



use \Attribute;



#[ Attribute( Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE ) ]
class Middleware
{
    public array $pipes;



    public function __construct (string|array $pipes)
    {
        // (Getting the value)
        $this->pipes = is_array( $pipes ) ? $pipes : [ $pipes ];
    }
}



?>