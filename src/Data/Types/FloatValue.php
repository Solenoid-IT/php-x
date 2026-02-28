<?php



namespace Solenoid\X\Data\Types;



use \Attribute;

use \Solenoid\X\Data\Types\Value;



#[ Attribute( Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER ) ]
class FloatValue extends Value
{
    const TYPE = 'float';



    public function __construct (string $name, bool $required = true, string $description = '', public readonly ?float $min = null, public readonly ?float $max = null)
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
            // (Setting the value)
            $this->value = null;



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
            if ( !filter_var( $value, FILTER_VALIDATE_FLOAT ) )
            {// (Validation failed)
                // (Getting the value)
                $this->error = "{$error_prefix}Must be a float";

                // Returning the value
                return false;
            }



            // (Getting the value)
            $this->value = (float) $value;

            if ( $this->min !== null && $this->value < $this->min )
            {// (Validation failed)
                // (Getting the value)
                $this->error = "{$error_prefix}Must be a number >= " . $this->min . ( $this->max === null ? '' : ' and <= ' . $this->max );

                // Returning the value
                return false;
            }

            if ( $this->max !== null && $this->value > $this->max )
            {// (Validation failed)
                // (Getting the value)
                $this->error = "{$error_prefix}Must be a number " . ( $this->min === null ? '' : '>= ' . $this->min . ' and ' ) . '<= ' . $this->max;
                // Returning the value
                return false;
            }
        }



        // Returning the value
        return true;
    }

    public function get_value () : float|null
    {
        // Returning the value
        return $this->value;
    }
}



?>