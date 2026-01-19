<?php



namespace Solenoid\X\Input;



class AnyValue extends Value
{
    const TYPE = 'mixed';



    public function __construct (string $name, bool $required = true, string $description = '')
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
            // (Getting the value)
            $this->value = $value;
        }



        // Returning the value
        return true;
    }

    public function get_value () : mixed
    {
        // Returning the value
        return $this->value;
    }
}



?>