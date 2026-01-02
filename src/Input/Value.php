<?php



namespace Solenoid\X\Input;



class Value
{
    private string $error = '';



    public readonly string $name;
    public readonly string $type;
    public readonly bool   $required;
    public readonly string $description;
    public readonly ?int   $min;
    public readonly ?int   $max;



    public function __construct (string $name, string $type, bool $required = true, string $description = '', ?int $min = null, ?int $max = null)
    {
        // (Getting the values)
        $this->name        = $name;
        $this->type        = $type;
        $this->required    = $required;
        $this->description = $description;
        $this->min         = $min;
        $this->max         = $max;
    }



    public function validate (mixed $value) : bool
    {
        // (Getting the value)
        $error_prefix = "$this->type $this->name ::";



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



        if ( $value !== null )
        {// Value is not null
            switch ( $this->type )
            {
                case 'int':
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
                break;

                case 'bool':
                    if ( !is_bool( $value ) && !in_array( $value, [ 0, 1, '0', '1', 'false', 'true' ] ) )
                    {// (Validation failed)
                        // (Getting the value)
                        $this->error = "$error_prefix Must be a boolean";

                        // Returning the value
                        return false;
                    }
                break;
            }
        }



        // Returning the value
        return true;
    }



    public function get_error () : string
    {
        // Returning the value
        return $this->error;
    }
}



?>