<?php



namespace Solenoid\X\Data;



use \Attribute;

use \Solenoid\X\Data\Types\Value;



#[ Attribute( Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE ) ]
class Input
{
    private Value|string|ArrayList|ReadableStream $reference;



    public function __construct (Value|string|ArrayList|ReadableStream $reference)
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
        if ( is_string( $this->reference ) && is_subclass_of( $this->reference, DTO::class ) )
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



    public function get_reference () : Value|string|ArrayList|ReadableStream
    {
        // Returning the value
        return $this->reference;
    }



    public static function read (string $class, string $method) : self|null
    {
        foreach ( ( new \ReflectionMethod( $class, $method ) )->getAttributes( self::class ) as $attribute )
        {// Processing each entry
            // Returning the value
            return $attribute->newInstance();
        }



        // Returning the value
        return null;
    }
}



?>