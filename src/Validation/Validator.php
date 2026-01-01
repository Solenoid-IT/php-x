<?php



namespace Solenoid\X\Validation;



class Validator
{
    private \ReflectionMethod $reflection;



    public readonly string $class;
    public readonly string $method;



    public function __construct (string $class, string $method)
    {
        // (Getting the values)
        $this->class  = $class;
        $this->method = $method;



        // (Getting the value)
        $this->reflection = new \ReflectionMethod( $this->class, $this->method );
    }



    public function check_input (mixed $input) : string|null
    {
        foreach ( $this->reflection->getAttributes( Input::class ) as $attribute )
        {// Processing each entry
            // (Getting the value)
            $param = $attribute->newInstance();

            if ( !$param->validate( $input ) )
            {// (Validation failed)
                // Returning the value
                return $param->get_error();
            }
        }



        // Returning the value
        return null;
    }

    public function check_input_params (mixed $input) : array
    {
        // (Setting the value)
        $errors = [];

        foreach ( $this->reflection->getAttributes( InputParam::class ) as $attribute )
        {// Processing each entry
            // (Getting the value)
            $param = $attribute->newInstance();



            // (Getting the value)
            $value = $input[ $param->name ] ?? null;



            if ( !$param->validate( $value ) )
            {// (Validation failed)
                // (Getting the value)
                $errors[ $param->name ] = $param->get_error();
            }
        }



        // Returning the value
        return $errors;
    }
}



?>