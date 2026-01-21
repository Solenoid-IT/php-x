<?php



namespace Solenoid\X;



class Collection
{
    private object $object;



    public function __construct ($object)
    {
        // (Getting the value)
        $this->object = $object;
    }



    public function get (string $key, $default = null) : mixed
    {
        if ( !str_contains( $key, '.' ) )
        {// Match failed
            // Returning the value
            return $this->object->{ $key } ?? $default;
        }



        // (Getting the value)
        $parts = explode( '.', $key );



        // (Getting the value)
        $current = $this->object;



        foreach ( $parts as $part )
        {// Processing each entry
            if ( is_object( $current ) && isset( $current->{ $part } ) )
            {// Match OK
                // (Getting the value)
                $current = $current->{ $part };
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
}



?>