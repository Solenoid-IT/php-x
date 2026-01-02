<?php



namespace Solenoid\X\Input;



class IntValue extends Value
{
    const TYPE = 'int';



    public function __construct (string $name, bool $required = true, string $description = '', public readonly ?int $min = null, public readonly ?int $max = null)
    {
        // (Calling the function)
        parent::__construct( $name, $required, $description );
    }



    public function validate (mixed $value) : bool
    {
        // (Getting the value)
        $error_prefix = self::TYPE . ' ' . $this->name . ' ' . '::';



        if ( $this->required )
        {// Value not found
            if ( $value === null || $value === '' )
            {// Value not found
                // (Getting the value)
                $this->error = "$error_prefix Value is required";

                // Returning the value
                return false;
            }
        }



        if ( !filter_var( $value, FILTER_VALIDATE_INT ) )
        {// (Validation failed)
            // (Getting the value)
            $this->error = "$error_prefix Must be an integer";

            // Returning the value
            return false;
        }



        // (Getting the value)
        $int_value = (int) $value;

        if ( $this->min !== null && $int_value < $this->min )
        {// (Validation failed)
            // (Getting the value)
            $this->error = "$error_prefix Must be a number >= " . $this->min . ( $this->max === null ? '' : ' and <= ' . $this->max );

            // Returning the value
            return false;
        }

        if ( $this->max !== null && $int_value > $this->max )
        {// (Validation failed)
            // (Getting the value)
            $this->error = "$error_prefix Must be a number " . ( $this->min === null ? '' : '>= ' . $this->min . ' and ' ) . '<= ' . $this->max;
            // Returning the value
            return false;
        }



        // Returning the value
        return true;
    }
}



?>