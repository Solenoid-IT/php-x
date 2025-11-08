<?php



namespace Solenoid\X;



use \Solenoid\X\Stream\ReadableStream;



class JBIN
{
    const SEPARATOR   = "\n\n";
    const BUFFER_SIZE = 1024;



    public mixed          $json;
    public ReadableStream $binary;



    public function __construct (mixed $json = [], ReadableStream $binary)
    {
        // (Getting the values)
        $this->json   = $json;
        $this->binary = $binary;
    }



    public static function get (string $src = 'php://input') : self|false
    {
        // (Setting the value)
        $separator_length = strlen( self::SEPARATOR );



        // (Opening the stream)
        $input_stream = fopen( $src, 'rb' );

        if ( $input_stream === false )
        {// (Unable to open the stream)
            // Returning the value
            return false;
        }



        // (Setting the values)
        $jsonString = '';
        $window     = '';

        while ( !feof( $input_stream ) )
        {// Processing each clock
            // (Getting the value)
            $chunk = fread( $input_stream, self::BUFFER_SIZE );

            if ( $chunk === false || $chunk === '' )
            {// Match failed
                // Breaking the iteration
                break;
            }



            // (Appending the value)
            $window .= $chunk;



            // (Getting the value)
            $pos = strpos( $window, self::SEPARATOR );

            if ( $pos !== false )
            {// Value found
                // (Appending the value)
                $jsonString .= substr( $window, 0, $pos );



                // (Getting the value)
                $seekAmount = -( strlen( $window ) - $pos - $separator_length );

                // (Seeking the stream)
                fseek( $input_stream, $seekAmount, SEEK_CUR );


            
                // (Getting the value)
                $json = json_decode( $jsonString, true );



                // Returning the value
                return new self( $json, new ReadableStream()->set_stream( $input_stream ) );
            }
            else
            {// (Position not found)
                // (Appending the value)
                $jsonString .= substr( $window, 0, -$separator_length );



                // (Getting the value)
                $window = substr( $window, -$separator_length );
            }
        }



        // (Closing the stream)
        fclose( $input_stream );



        // Returning the value
        return false;
    }
}



?>