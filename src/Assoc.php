<?php



namespace Solenoid\X;



class Assoc
{
    private array  $value;
    private string $separator;



    private static function _expand (array $assoc, string $separator) : array
    {
        // (Setting the value)
        $result = [];

        foreach ( $assoc as $k => $v )
        {// Processing each entry
            if ( is_array( $v ) )
            {// Match OK
                // (Expanding the array)
                $v = self::_expand( $v, $separator );
            }



            foreach ( array_reverse( explode( $separator, $k ) ) as $k )
            {// Processing each entry
                // (Getting the value)
                $v = [ $k => $v ];
            }

            // (Getting the value)
            $result = array_merge_recursive( $result, $v );
        }



        // Returning the value
        return $result;
    }



    public function __construct (array $value = [], string $separator = '.')
    {
        // (Getting the values)
        $this->value     = $value;
        $this->separator = $separator;
    }



    public function expand () : array
    {
        // Returning the value
        return self::_expand( $this->value, $this->separator );
    }

    public function compress () : array
    {
        // (Setting the value)
        $result = [];

        

        // (Getting the value)
        $rii = new \RecursiveIteratorIterator( new \RecursiveArrayIterator( $this->value ) );

        foreach ( $rii as $leaf_value )
        {// Processing each entry
            // (Setting the value)
            $keys = [];

            foreach ( range( 0, $rii->getDepth() ) as $depth )
            {// Processing each entry
                // (Appending the value)
                $keys[] = $rii->getSubIterator( $depth )->key();
            }



            // (Getting the value)
            $result[ implode( $this->separator, $keys ) ] = $leaf_value;
        }



        // Returning the value
        return $result;
    }
}



?>