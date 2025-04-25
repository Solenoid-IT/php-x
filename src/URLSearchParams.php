<?php



namespace Solenoid\X;



class URLSearchParams
{
    public readonly string $query;



    public function __construct (string $query)
    {
        // (Getting the value)
        $this->query = $query;
    }



    public function fetch (bool $associative = false) : \stdClass|array
    {
        if ( $associative )
        {// Value is true
            // (Setting the value)
            $params = [];
        }
        else
        {// Value is false
            // (Setting the value)
            $params = new \stdClass();
        }



        foreach ( explode( '&', preg_replace( '/^\?/', '', $this->query ) ) as $param )
        {
            // (Getting the values)
            [ $key, $value ] = explode( '=', $param, 2 );



            // (Getting the value)
            $key = urldecode( $key );



            // (Getting the value)
            $value = isset( $value ) ? urldecode( $value ) : null;



            if ( $associative )
            {// Value is true
                // (Getting the value)
                $params[ $key ] = $value;
            }
            else
            {// Value is false
                // (Getting the value)
                $params->{ $key } = $value;
            }
        }



        // Returning the value
        return $params;
    }



    public function __toString () : string
    {
        // (Getting the value)
        return $this->query;
    }
}



?>