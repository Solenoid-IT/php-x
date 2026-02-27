<?php



namespace Solenoid\X\Data;



use \Attribute;

use \Solenoid\X\Data\Types\Value;



#[ Attribute( Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE ) ]
class Input
{
    private Value|DTO|ArrayList|ReadableStream $reference;



    public function __construct (Value|DTO|ArrayList|ReadableStream $reference)
    {
        // (Getting the value)
        $this->reference = $reference;
    }



    public function get_type () : string
    {
        // (Setting the value)
        $type = null;

        if ( $this->reference instanceof Value )
        {// Match OK
            // (Setting the value)
            $type = 'Value';
        }
        else
        if ( $this->reference instanceof DTO )
        {// Match OK
            // (Setting the value)
            $type = 'DTO';
        }
        else
        if ( $this->reference instanceof ArrayList )
        {// Match OK
            // (Setting the value)
            $type = 'ArrayList';
        }
        else
        if ( $this->reference instanceof ReadableStream )
        {// Match OK
            // (Setting the value)
            $type = 'ReadableStream';
        }



        // Returning the value
        return $type;
    }



    public function validate (mixed $value) : bool
    {
        // Returning the value
        return $this->reference->validate( $value );
    }



    public function get_error () : mixed
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