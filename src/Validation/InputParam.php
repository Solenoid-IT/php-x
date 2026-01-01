<?php



namespace Solenoid\X\Validation;



use \Attribute;



#[ Attribute( Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE ) ]
class InputParam
{
    private string $error = '';



    public readonly string $name;
    public readonly string $type;
    public readonly bool   $required;
    public readonly string $description;



    public function __construct (string $name, string $type, bool $required = true, string $description = '')
    {
        // (Getting the values)
        $this->name        = $name;
        $this->type        = $type;
        $this->required    = $required;
        $this->description = $description;
    }



    public function validate (mixed $value) : bool
    {
        if ( $this->required )
        {// Value not found
            if ( $value === null || $value === '' )
            {// Value not found
                // (Setting the value)
                $this->error = 'Input is required';

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
                        // (Setting the value)
                        $this->error = 'Input must be an integer';

                        // Returning the value
                        return false;
                    }  
                break;

                case 'bool':
                    if ( !is_bool( $value ) /*&& !in_array( $value, [ 0, 1, '0', '1', 'true', 'false' ] )*/ )
                    {// (Validation failed)
                        // (Setting the value)
                        $this->error = 'Input must be a boolean';

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