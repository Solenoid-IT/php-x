<?php



namespace Solenoid\X\Input;



class ArrayList
{
    private Value|DTO $reference;

    protected bool  $is_valid;
    protected array $instances;



    public function __construct (Value|DTO $reference)
    {
        // (Getting the value)
        $this->reference = $reference;
    }



    public function validate (mixed $data) : bool
    {
        // (Setting the value)
        $this->is_valid = true;



        if ( !is_array( $data ) )
        {// (Value is not an array)
            // (Setting the value)
            $this->is_valid = false;

            // Returning the value
            return false;
        }



        // (Setting the value)
        $this->instances = [];

        foreach ( $data as $item_data )
        {// Processing each entry
            // (Getting the value)
            $copy = clone $this->reference;
            
            if ( !$copy->validate( $item_data ) )
            {// Match failed
                // (Setting the value)
                $this->is_valid = false;
            }



            // (Appending the value)
            $this->instances[] = $copy;
        }



        // Returning the value
        return $this->is_valid;
    }



    public function get_error () : array|null
    {
        // (Setting the value)
        $has_error = false;



        // (Setting the value)
        $errors = [];

        foreach ( $this->instances as $i => $instance )
        {// Processing each entry
            // (Getting the value)
            $error = $instance->get_error();

            if ( $error !== null )
            {// (Error found)
                // (Setting the value)
                $has_error = true;
            }



            // (Getting the value)
            $errors[ $i ] = $error;
        }



        // Returning the value
        return $has_error ? $errors : null;
    }

    public function get_value () : array
    {
        // Returning the value
        return array_map( function ($instance) { return $instance->get_value(); }, $this->instances );
    }
}



?>