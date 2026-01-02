<?php



namespace Solenoid\X\Input;



class StringValue extends Value
{
    const TYPE = 'string';



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



        // (Getting the value)
        $this->value = (string) $value;



        // Returning the value
        return true;
    }
}



?>