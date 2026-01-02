<?php



namespace Solenoid\X\Input;



use \Attribute;



#[ Attribute( Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE ) ]
class Input
{
    private Value|DTO $reference;



    public function __construct (Value|DTO $reference)
    {
        // (Getting the value)
        $this->reference = $reference;
    }



    public function validate (mixed $value) : bool
    {
        // Returning the value
        return $this->reference->validate( $value );
    }



    public function get_error () : string
    {
        // Returning the value
        return $this->reference->get_error();
    }

    public function get_value () : mixed
    {
        // Returning the value
        return $this->reference->get_value();
    }
}



?>