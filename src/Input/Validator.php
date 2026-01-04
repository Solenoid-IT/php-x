<?php



namespace Solenoid\X\Input;



class Validator
{
    private Input $input;



    public readonly string $class;
    public readonly string $method;



    public function __construct (string $class, string $method)
    {
        // (Getting the values)
        $this->class  = $class;
        $this->method = $method;



        foreach ( ( new \ReflectionMethod( $this->class, $this->method ) )->getAttributes( Input::class ) as $attribute )
        {// Processing each entry
            // (Getting the value)
            $this->input = $attribute->newInstance();

            // Breaking the iteration
            break;
        }
    }



    public function available () : bool
    {
        // Returning the value
        return isset( $this->input );
    }



    public function get_input_type () : string
    {
        // Returning the value
        return $this->input->get_type();
    }



    public function check (mixed $value) : mixed
    {
        if ( !$this->input->validate( $value ) )
        {// (Validation failed)
            // Returning the value
            return $this->input->get_error();
        }



        // Returning the value
        return null;
    }



    public function get_value () : mixed
    {
        // Returning the value
        return $this->input->get_value();
    }
}



?>