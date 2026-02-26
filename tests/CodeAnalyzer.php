<?php



include_once( __DIR__ . '/../vendor/autoload.php' );



use \Solenoid\X\CodeAnalyzer;



function print_message (string $message) : void
{
    // (Printing the value)
    echo $message;
}



class Level1
{
    public function execute (string $message) : void
    {
        // (Printing the message)
        print_message( $message );
    }
}

class Level2
{
    public function execute (string $message) : void
    {
        // (Printing the message)
        ( new Level1() )->execute( $message );
    }
}

class Level3
{
    public function execute (string $message) : void
    {
        // (Printing the message)
        ( new Level2() )->execute( $message );
    }
}



class Test
{
    public function execute () : void
    {
        // (Printing the message)
        print_message( 'Hello from level 0' );

        // (Printing the value)
        ( new Level1() )->execute( 'Hello from level 1' );

        // (Printing the value)
        ( new Level2() )->execute( 'Hello from level 2' );

        // (Printing the value)
        ( new Level3() )->execute( 'Hello from level 3' );
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