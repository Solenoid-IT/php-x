<?php



include_once( __DIR__ . '/../vendor/autoload.php' );



use \Solenoid\X\CodeAnalyzer;



function print_message (string $message) : void
{
    // (Printing the value)
    echo $message;
}



class Test
{
    public function execute () : void
    {
        // (Printing the message)
        print_message( 'Hello World!' );

        // (Printing the message)
        print_message( 'Hello Again!' );
    }



    public static function run ()
    {
        // (Setting the value)
        $results = [];

        foreach ( ( new CodeAnalyzer( self::class, 'execute' ) )->find( 'print_message' ) as $result )
        {// Processing each entry
            // (Appending the value)
            $results[] = $result->args[0]->value->value;
        }



        // debug
        print_r( $results );
    }
}



Test::run();



?>