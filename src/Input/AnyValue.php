<?php



namespace Solenoid\X\Input;



class AnyValue extends Value
{
    const TYPE = 'mixed';



    public function __construct (string $name, bool $required = true, string $description = '', public array $types = [])
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
            // (Setting the value)
            $type_match = false;

            foreach ( $this->types as $type )
            {// Processing each entry
                switch ( $type )
                {
                    case 'int':
                        if ( is_int( $value ) )
                        {// Match failed
                            // (Setting the value)
                            $type_match = true;

                            // Breaking the iteration
                            break 2;
                        }
                    break;

                    case 'float':
                        if ( is_float( $value ) )
                        {// Match failed
                            // (Setting the value)
                            $type_match = true;

                            // Breaking the iteration
                            break 2;
                        }
                    break;

                    case 'string':
                        if ( is_string( $value ) )
                        {// Match failed
                            // (Setting the value)
                            $type_match = true;

                            // Breaking the iteration
                            break 2;
                        }
                    break;

                    case 'bool':
                        if ( is_bool( $value ) )
                        {// Match failed
                            // (Setting the value)
                            $type_match = true;

                            // Breaking the iteration
                            break 2;
                        }
                    break;

                    case 'array':
                        if ( is_array( $value ) )
                        {// Match failed
                            // (Setting the value)
                            $type_match = true;

                            // Breaking the iteration
                            break 2;
                        }
                    break;

                    default:
                        if ( $value instanceof $type )
                        {// Match failed
                            // (Setting the value)
                            $type_match = true;

                            // Breaking the iteration
                            break 2;
                        }
                    break;
                }
            }



            if ( !$type_match )
            {// (Type does not match)
                // (Getting the value)
                $this->error = "$error_prefix Value type does not match the expected types";

                // Returning the value
                return false;
            }



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