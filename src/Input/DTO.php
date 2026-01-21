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



        if ( !is_array( $data ) )
        {// (Value is not an array)
            // (Setting the value)
            $this->is_valid = false;

            // Returning the value
            return false;
        }



        // (Getting the value)
        $this->property_tree = new \stdClass();

        foreach ( ( new \ReflectionClass( $this ) )->getProperties( \ReflectionProperty::IS_PUBLIC ) as $property )
        {// Processing each entry
            if ( !$property->isInitialized( $this ) ) continue;



            // (Getting the value)
            $instance = $property->getValue( $this );



            // (Getting the value)
            $name = $property->getName();



            // (Getting the value)
            $raw_value = $data[ $name ] ?? null;



            if ( $instance instanceof Value || $instance instanceof DTO || $instance instanceof ArrayList )
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



                /* ahcid to implementt

                if ( $instance instanceof DTO )
                {// Match OK
                    // (Getting the value)
                    $node->children = isset( $instance->property_tree ) ? $instance->property_tree : new \stdClass();
                }
                else
                if ( $instance instanceof ArrayList )
                {// Match OK
                    // (Getting the value)  
                    $node->children = array_map( function($inst) { return ( $inst instanceof DTO ) ? $inst->property_tree : null; }, $instance->instances );
                }

                */



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
            return 'object is required';
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
            $error = $node->instance->get_error();

            if ( $error === null ) continue;



            // (Getting the value)
            $error_tree->$name = $error;



            // (Setting the value)
            $has_error = true;
        }



        // Returning the value
        return $has_error ? $error_tree : null;
    }

    public function get_value () : self
    {
        // (Getting the value)
        $object = new ( get_class( $this ) )();

        foreach ( $this->property_tree as $name => $node )
        {// Processing each entry
            // (Getting the value)
            $object->$name = $node->instance->get_value();
        }



        // Returning the value
        return $object;
    }



    public function get (string $key, $default = null) : mixed
    {
        // (Getting the value)
        $parts = explode( '.', $key );



        // (Getting the value)
        $current = $this;



        foreach ( $parts as $part )
        {// Processing each entry
            if ( !isset( $current->property_tree->$part ) )
            {// Value not found
                // Returning the value
                return $default;
            }



            // (Getting the value)
            $instance = $current->property_tree->$part->instance;

            if ( count( $parts ) > 1 )
            {// Match OK
                // (Shifting the array)
                array_shift( $parts );



                // (Getting the value)
                $next_key = implode( '.', $parts );



                if ( $instance instanceof self )
                {// Match OK
                    // Returning the value
                    return $instance->get( $next_key );
                }



                // Returning the value
                return $default;
            }



            // Returning the value
            return $instance->get_value();
        }



        // Returning the value
        return $default;
    }

    public function list (string $prefix = '') : array
    {
        // (Getting the value)
        $list = [];

        if ( !isset( $this->property_tree ) )
        {// Value not found
            // Returning the value
            return $list;
        }



        foreach ( $this->property_tree as $name => $node )
        {// Processing each entry
            // (Getting the value)
            $instance = $node->instance;



            // (Getting the value)
            $key = $prefix === '' ? $name : "$prefix.$name";



            if ( $instance instanceof self )
            {// (Instance is a DTO)
                // (Merging values)
                $list = array_merge( $list, $instance->list( $key ) );
            }
            else
            if ( $instance instanceof ArrayList )
            {// (Instance is an ArrayList)
                /* ahcid to implementt

                foreach ( $instance->instances as $index => $item )
                {// Processing each list item
                    // (Getting the value)
                    $item_key = $key . '.' . $index;

                    if ( $item instanceof self )
                    {// (Item is a DTO)
                        // (Merging values)
                        $list = array_merge( $list, $item->list( $item_key ) );
                    }
                    else
                    {// (Item is a Value)
                        // (Getting the value)
                        $list[ $item_key ] = $item->get_value();
                    }
                }

                */
            }
            else
            {// (Instance is a Value)
                // (Getting the value)
                $list[ $key ] = $instance->get_value();
            }
        }



        // Returning the value
        return $list;
    }
}



?>