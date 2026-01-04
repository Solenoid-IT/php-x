<?php



namespace Solenoid\X\Input;



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



        if ( !is_bool( $value ) && !in_array( $value, [ 0, 1, '0', '1', 'false', 'true' ] ) )
        {// (Validation failed)
            // (Getting the value)
            $this->error = "$error_prefix Must be a boolean";

            // Returning the value
            return false;
        }



        // (Getting the value)
        $this->value = (bool) $value;



        // Returning the value
        return true;
    }

    public function get_value () : bool
    {
        // Returning the value
        return $this->value;
    }
}



?>