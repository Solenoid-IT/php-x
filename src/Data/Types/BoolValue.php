<?php



namespace Solenoid\X\Data\Types;



use \Attribute;

use \Solenoid\X\Data\Types\Value;



#[ Attribute( Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER ) ]
class BoolValue extends Value
{
    const TYPE = 'bool';



    public function __construct (string $name, bool $required = true, string $description = '')
    {
        // (Calling the function)
        parent::__construct( $name, $required, $description );
    }

    

    public function validate (mixed $value) : bool
    {
        // (Getting the value)
        $error_prefix = $this->name ? ( self::TYPE . ' ' . $this->name . ' ' ) . ':: ' : '';



        if ( $value === null || $value === '' )
        {// (Value is not set)
            if ( $this->required )
            {// (Value is required)
                // (Getting the value)
                $this->error = "{$error_prefix}Value is required";

                // Returning the value
                return false;
            }
        }
        else
        {// (Value is set)
            if ( !is_bool( $value ) && !in_array( $value, [ 0, 1, '0', '1', 'false', 'true' ] ) )
            {// (Validation failed)
                // (Getting the value)
                $this->error = "{$error_prefix}Must be a boolean";

                // Returning the value
                return false;
            }



            // (Getting the value)
            $this->value = (bool) $value;
        }



        // Returning the value
        return true;
    }

    public function get_value () : bool|null
    {
        // Returning the value
        return $this->value;
    }
}



?>