<?php



namespace Solenoid\X\Data\Generators;



use \Solenoid\X\Data\Types\StringValue;



class StringGenerator extends Generator
{
    private static function generate_by_pattern (string $regex) : string
    {
        // (Getting the value)
        $pattern = trim( $regex, '/^$' );



        // (Setting the value)
        $map =
        [
            '[a-zA-Z0-9]' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
            '[a-z]'       => 'abcdefghijklmnopqrstuvwxyz',
            '[A-Z]'       => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            '[0-9]'       => '0123456789',
            '\d'          => '0123456789',
            '\w'          => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_'
        ]
        ;



        // (Getting the value)
        $result = $pattern;

        foreach ( $map as $search => $chars )
        {// Processing each entry
            while ( str_contains( $result, $search ) )
            {// Processing each entry
                // (Setting the value)
                $random_content = '';

                for ( $i = 0; $i < 8; $i++ )
                {// Iterating each index
                    // (Appending the value)
                    $random_content .= $chars[ rand( 0, strlen( $chars ) - 1 ) ];
                }



                // (Getting the value)
                $result = substr_replace( $result, $random_content, strpos( $result, $search ), strlen( $search ) );
            }
        }



        // // Returning the value
        return substr( preg_replace( '/[+*?{}()\[\]\\\]/', '', $result ), 0, 20 );
    }



    public function __construct (StringValue $value)
    {
        // (Calling the function)
        parent::__construct( $value );
    }



    public function generate () : string|null
    {
        if ( !$this->value->required && rand( 0, 3 ) === 0 )
        {// ('25% chance of empty for optional fields')
            // Returning the value
            return null;
        }



        if ( !( $this->value instanceof StringValue ) ) throw new \Exception( 'StringGenerator requires a StringValue' );



        if ( $this->value->regex === null )
        {// Value not found
            // Returning the value
            return substr( str_shuffle( 'abcdefghijklmnopqrstuvwxyz0123456789' ), 0, 8 );
        }



        // Returning the value
        return self::generate_by_pattern( $this->value->regex );
    }

}



?>