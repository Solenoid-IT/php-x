<?php



namespace Solenoid\X;



class Env
{
    private array $values = [];



    public function __construct (string $file_path)
    {
        // (Getting the value)
        $content = file_get_contents( $file_path );

        foreach ( explode( "\n", $content ) as $line )
        {// Processing each entry
            // (Getting the value)
            $line = trim( $line );

            if ( empty( $line ) || strpos( $line, '#' ) === 0 ) continue;



            // (Getting the value)
            [ $key, $value ] = explode( '=', $line, 2 );

            // (Getting the value)
            $key   = trim( $key );
            $value = trim( $value );



            // (Getting the value)
            $this->values[ $key ] = $value;
        }
    }



    public function get (string $key) : string|false
    {
        // (Getting the value)
        return $this->values[ $key ] ?? false;
    }
}



?>