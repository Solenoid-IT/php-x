<?php



namespace Solenoid\X\Data\Generators;



use \Solenoid\X\Data\Types\EnumValue;



class EnumGenerator extends Generator
{
    public function __construct (EnumValue $value)
    {
        // (Calling the function)
        parent::__construct( $value );
    }



    public function generate () : mixed
    {
        if ( !( $this->value instanceof EnumValue ) ) throw new \Exception( 'EnumGenerator requires a EnumValue' );



        if ( !$this->value->required && rand( 0, 3 ) === 0 )
        {// (25% chance to return null if not required)
            // Returning the value
            return null;
        }



        // Returning the value
        return $this->value->values[ array_rand( $this->value->values ) ];
    }
}



?>