<?php



namespace Solenoid\X\Data\Generators;



use \Solenoid\X\Data\Types\BoolValue;



class BoolGenerator extends Generator
{
    public function __construct (BoolValue $value)
    {
        // (Calling the function)
        parent::__construct( $value );
    }



    public function generate () : bool|null
    {
        if ( !( $this->value instanceof BoolValue ) ) throw new \Exception( 'BoolGenerator requires a BoolValue' );



        if ( !$this->value->required && rand( 0, 3 ) === 0 )
        {// (25% chance to return null if not required)
            // Returning the value
            return null;
        }



        // Returning the value
        return (bool) rand( 0, 1 );
    }
}



?>