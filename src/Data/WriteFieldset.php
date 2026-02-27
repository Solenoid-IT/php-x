<?php



namespace Solenoid\X\Data;



use \Attribute;



#[ Attribute( Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE ) ]
class WriteFieldset
{
    public function __construct (public array $fields = []) {}
}



?>