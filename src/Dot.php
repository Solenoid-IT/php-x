<?php



namespace Solenoid\X;



class Dot
{
    public function get (string $key, $default = null) : mixed
    {
        if ( !str_contains( $key, '.' ) )
        {// Match failed
            // Returning the value
            return $this->$key ?? $default;
        }



        // (Getting the value)
        $parts = explode( '.', $key );



        // (Getting the value)
        $current = $this;



        foreach ( $parts as $part )
        {// Processing each entry
            if ( is_object( $current ) && isset( $current->$part ) )
            {// Match OK
                // (Getting the value)
                $current = $current->$part;
            }
            else
            {// Match failed
                // Returning the value
                return $default;
            }
        }



        // Returning the value
        return $current;
    }

    public function set (string $key, mixed $value) : self
    {
        // (Getting the value)
        $parts = explode( '.', $key );

        if ( count( $parts ) > 1 )
        {// (Parts are multiple)
            // (Getting the values)
            $current_key   = array_shift( $parts );
            $remaining_key = implode( '.', $parts );

            if ( isset( $this->$current_key ) && $this->$current_key instanceof self )
            {// Match OK
                // (Setting the value)
                $this->$current_key->set( $remaining_key, $value );
            }



            // Returning the value
            return $this;
        }



        // (Getting the value)
        $name = $parts[0];



        // (Getting the value)
        $this->$name = $value;



        // Returning the value
        return $this;
    }

    public function unset (string $key) : self
    {
        // (Getting the value)
        $parts = explode( '.', $key );

        if ( count( $parts ) > 1 )
        {// (Parts are multiple)
            // (Getting the values)
            $current_key   = array_shift( $parts );
            $remaining_key = implode( '.', $parts );

            if ( isset( $this->$current_key ) && $this->$current_key instanceof self )
            {// Match OK
                // (Unsetting the value)
                $this->$current_key->unset( $remaining_key );
            }



            // Returning the value
            return $this;
        }



        // (Getting the value)
        $name = $parts[0];



        // (Unsetting the value)
        unset( $this->$name );



        // Returning the value
        return $this;
    }



    public function compress (mixed $target = null, string $prefix = '') : array
    {
        // (Getting the value)
        $list = [];



        // (Getting the value)
        $current = $target ?? $this->object;

        foreach ( $current as $key => $value ) 
        {// Processing each entry
            // (Getting the value)
            $full_key = $prefix === '' ? (string) $key : "$prefix.$key";

            if ( is_array( $value ) || is_object( $value ) ) 
            {// (Value is a node)
                // (Getting the value)
                $list = array_merge( $list, $this->compress( $value, $full_key ) );
            } 
            else 
            {// (Value is a leaf)
                // (Getting the value)
                $list[ $full_key ] = $value;
            }
        }



        // Returning the value
        return $list;
    }



    public function iterate (mixed $target = null, string $prefix = '') : \Generator
    {
        // (Getting the value)
        $current = $target ?? $this;



        // (Getting the value)
        $keys = array_keys( is_object( $current ) ? get_object_vars( $current ) : (array) $current );



        foreach ( $keys as $key ) 
        {// Processing each entry
            // (Getting the value)
            $full_key = $prefix === '' ? (string) $key : "$prefix.$key";
            


            // (Getting the value)
            $value = is_object( $current ) ? ( $current->$key ?? null ) : ( $current[ $key ] ?? null );

            if ( $value instanceof self || is_object( $value ) || is_array( $value ) ) 
            {// (Node found)
                if ( $value instanceof self ) 
                {// (Target is another Dot instance)
                    // (Yielding the value)
                    yield from $value->iterate( null, $full_key );
                }
                else
                {// (Target is a standard object or array)
                    // (Yielding the value)
                    yield from $this->iterate( $value, $full_key );
                }



                if ( is_object( $current ) )
                {// Value is true
                    // (Removing the element)
                    unset( $current->$key );
                }
                else
                {// Value is false
                    // (Removing the element)
                    unset( $current[ $key ] );
                }
            } 
            else 
            {// (Leaf found)
                // (Yielding the value)
                yield [ $full_key, $value ];


    
                if ( is_object( $current ) )
                {// Value is true
                    // (Removing the element)
                    unset( $current->$key );
                }
                else
                {// Value is false
                    // (Removing the element)
                    unset( $current[ $key ] );
                }
            }
        }
    }



    public function __get (string $key)
    {
        // Returning the value
        return $this->get( $key );
    }

    public function __set (string $key, mixed $value)
    {
        // (Setting the value)
        $this->set( $key, $value );
    }

    public function __unset (string $key)
    {
        if ( isset( $this->$key ) )
        {// Value found
            // (Unsetting the value)
            unset( $this->$key );
        }
    }
}



?>