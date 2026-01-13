<?php



namespace Solenoid\X\Input;



class StringValue extends Value
{
    const TYPE = 'string';



    public function __construct (string $name, bool $required = true, string $description = '', public readonly ?string $regex = null)
    {
        // (Calling the function)
        parent::__construct( $name, $required, $description );
    }



    public function validate (mixed $value) : bool
    {
        // (Getting the value)
        $error_prefix = self::TYPE . ' ' . $this->name . ' ' . '::';



        if ( $value === null || $value === '' )
        {// (Value is not set)
            // (Setting the value)
            $this->value = null;



            if ( $this->required )
            {// (Value is required)
                // (Getting the value)
                $this->error = "$error_prefix Value is required";

                // Returning the value
                return false;
            }
        }
        else
        {// (Value is set)
            if ( !is_string( $value ) )
            {// (Type is not a string)
                // (Getting the value)
                $this->error = "$error_prefix Value must be a string";

                // Returning the value
                return false;
            }



            if ( $this->regex !== null )
            {// Value found
                if ( !preg_match( $this->regex, $value ) )
                {// (Match failed)
                    // (Getting the value)
                    $this->error = "$error_prefix Value does not match the required format";

                    // Returning the value
                    return false;
                }
            }
                



            // (Getting the value)
            $this->value = $value;
        }



        // Returning the value
        return true;
    }

    public function get_value () : string|null
    {
        // Returning the value
        return $this->value;
    }
}



?>