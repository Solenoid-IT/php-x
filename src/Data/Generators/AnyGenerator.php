<?php



namespace Solenoid\X\Data\Generators;



use \Solenoid\X\Data\Types\AnyValue;



class AnyGenerator extends Generator
{
    public function __construct (AnyValue $value)
    {
        // (Calling the function)
        parent::__construct( $value );
    }



    public function generate () : mixed
    {
        if ( !( $this->value instanceof AnyValue ) ) throw new \Exception( 'AnyGenerator requires a AnyValue' );



        if ( !$this->value->required && rand( 0, 3 ) === 0 )
        {// (25% chance to return null if not required)
            // Returning the value
            return null;
        }



        // (Getting the value)
        $types = !empty( $this->value->types ) ? $this->value->types : [ 'bool', 'int', 'float', 'string', 'array' ];



        // (Getting the value)
        $random_type = $types[ array_rand( $types ) ];

        switch ( $random_type )
        {
            case 'bool':
                // Returning the value
                return (bool) rand( 0, 1 );
            break;

            case 'int':
                // Returning the value
                return rand( 1, 100 );
            break;

            case 'float':
                // Returning the value
                return (float) ( rand( 1, 100 ) / rand( 1, 10 ) );
            break;

            case 'string':
                // Returning the value
                return 'generated_string_' . rand( 1000, 9999 );
            break;

            case 'array':
                // Returning the value
                return [ 'item_1', 'item_2', rand( 1, 100 ) ];
            break;

            default:
                // Returning the value
                return "Instance of {$random_type}";
        }
    }
}



?>