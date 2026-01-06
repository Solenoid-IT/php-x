<?php



namespace Solenoid\X\Input;



abstract class DTO
{
    protected bool      $is_valid;
    protected \stdClass $property_tree;



    public function validate (mixed $data) : bool
    {
        // (Setting the value)
        $this->is_valid = true;



        // (Getting the value)
        $this->property_tree = new \stdClass();



        if ( !is_array( $data ) )
        {// (Value is not an array)
            // (Setting the value)
            $this->is_valid = false;

            // Returning the value
            return false;
        }



        foreach ( ( new \ReflectionClass( $this ) )->getProperties( \ReflectionProperty::IS_PUBLIC ) as $property )
        {// Processing each entry
            if ( !$property->isInitialized( $this ) ) continue;



            // (Getting the value)
            $instance = $property->getValue( $this );



            // (Getting the value)
            $name = $property->getName();



            // (Getting the value)
            $raw_value = $data[ $name ] ?? null;



            if ( $instance instanceof Value || $instance instanceof DTO )
            {// Match OK
                if ( !$instance->validate( $raw_value ) )
                {// (Validation failed)
                    // (Setting the value)
                    $this->is_valid = false;
                }



                // (Getting the value)
                $node = new \stdClass();

                // (Getting the value)
                $node->instance = $instance;

                if ( $instance instanceof DTO )
                {// Match OK
                    // (Getting the value)
                    $node->children = $instance->get_property_tree();
                }



                // (Getting the value)
                $this->property_tree->$name = $node;
            }
        }



        // Returning the value
        return $this->is_valid;
    }



    public function get_error () : string|\stdClass|null
    {
        if ( !isset( $this->property_tree ) )
        {// Value not found
            // Returning the value
            return 'DTO object is empty';
        }



        // (Getting the value)
        $error_tree = new \stdClass();



        // (Setting the value)
        $has_error = false;



        /* ahcid to deleted

        foreach ( ( new \ReflectionClass( $this ) )->getProperties( \ReflectionProperty::IS_PUBLIC ) as $property )
        {// Processing each entry
            // (Getting the value)
            $name = $property->getName();



            // (Getting the value)
            $instance = $property->getValue( $this );

            if ( $instance instanceof Value )
            {// Match OK
                // (Getting the value)
                $err = $instance->get_error();

                if ( $err )
                {// (Error found)
                    // (Getting the value)
                    $error_tree->$name = $err;

                    // (Setting the value)
                    $has_error = true;
                }
            }
            else
            if ( $instance instanceof DTO )
            {// Match OK
                // (Getting the value)
                $child_errors = $instance->get_error();

                if ( $child_errors !== null )
                {// (Error found)
                    // (Getting the value)
                    $error_tree->$name = $child_errors;

                    // (Setting the value)
                    $has_error = true;
                }
            }
        }

        */



        foreach ( $this->property_tree as $name => $node )
        {// Processing each entry
            // (Getting the value)
            $error_tree->$name = $node->instance->get_error();

            if ( !$has_error )
            {// (Error not found yet)
                // (Getting the value)
                $has_error = $error_tree->$name !== null;
            }
        }



        // Returning the value
        return $has_error ? $error_tree : null;
    }

    public function get_value () : self
    {
        foreach ( $this->property_tree as $name => $node )
        {// Processing each entry
            // (Getting the value)
            $this->$name = $node->instance->get_value();
        }



        // Returning the value
        return $this;
    }



    public function get_property_tree () : \stdClass
    {
        // Returning the value
        return $this->property_tree;
    }
}



?>