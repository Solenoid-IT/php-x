<?php



namespace Solenoid\X\Data\Generators;



use \Solenoid\X\Data\Types\FloatValue;



class FloatGenerator extends Generator
{
    public function __construct (FloatValue $value)
    {
        // (Calling the function)
        parent::__construct( $value );
    }



    public function generate () : float|null
    {
        if ( !( $this->value instanceof FloatValue ) ) throw new \Exception( 'FloatGenerator requires a FloatValue' );



        if ( !$this->value->required && rand( 0, 3 ) === 0 )
        {// (25% chance to return null if not required)
            // Returning the value
            return null;
        }



        // (Getting the values)
        $min = $this->value->min;
        $max = $this->value->max;



        // Returning the value
        return
            match ( true )
            {
                // (Values found)
                $min !== null && $max !== null => 
                    $min + ( mt_rand() / mt_getrandmax() ) * ( $max - $min ),
                
                // Value found
                $min !== null => 
                    $min + ( mt_rand() / mt_getrandmax() ) * 1000.0,

                // Value found
                $max !== null => 
                    ( $max > 0 ? 0.0 : $max - 1000.0 ) + 
                    ( mt_rand() / mt_getrandmax() ) * 
                    ( $max - ( $max > 0 ? 0.0 : $max - 1000.0 ) ),

                default => 
                    1.0 + ( mt_rand() / mt_getrandmax() ) * 99.0
            }
        ;
    }
}



?>