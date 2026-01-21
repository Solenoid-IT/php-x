<?php



namespace Solenoid\X\Input;



use \Attribute;



#[ Attribute( Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE ) ]
class Record
{
    public function __construct (public array $fields = []) {}
}



?>