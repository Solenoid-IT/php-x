<?php



namespace Solenoid\X\Input;



use \Attribute;



#[ Attribute( Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE ) ]
class Key
{
    public function __construct (public string $name, public array $fields) {}
}



?>