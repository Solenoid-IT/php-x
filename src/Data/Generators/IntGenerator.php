<?php



namespace Solenoid\X\Data\Generators;



use \Solenoid\X\Data\Types\IntValue;



class IntGenerator extends Generator
{
    public function __construct (IntValue $value)
    {
        // (Calling the function)
        parent::__construct( $value );
    }



    public function generate () : int|null
    {
        if ( !( $this->value instanceof IntValue ) ) throw new \Exception( 'IntGenerator requires an IntValue' );



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
                $min !== null && $max !== null => rand( $min, $max ),
                $min !== null                  => rand( $min, $min + 1000 ),
                $max !== null                  => rand( $max > 0 ? 0 : $max - 1000, $max ),
                default                        => rand( 1, 100 )
            }
        ;
    }
}



?>