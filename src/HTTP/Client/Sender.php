<?php



namespace Solenoid\X\HTTP\Client;



use \Solenoid\X\HTTP\Request;
use \Solenoid\X\HTTP\Response;
use \Solenoid\X\HTTP\Status;
use \Solenoid\X\HTTP\Client\Target;
use \Solenoid\X\HTTP\Client\Result;
use \Solenoid\X\HTTP\Client\Hop;



class Sender
{
    public readonly int $conn_timeout;
    public readonly int $exec_timeout;
    public readonly int $max_redirs;



    public function __construct (int $conn_timeout = 60, int $exec_timeout = 60, int $max_redirs = 10)
    {
        // (Getting the value)
        $this->conn_timeout = $conn_timeout;
        $this->exec_timeout = $exec_timeout;
        $this->max_redirs   = $max_redirs;
    }



    public function send (string|Request $request, string|Target $target) : Result|false
    {
        if ( is_string( $request ) )
        {// (Request is a string)
            // (Getting the value)
            $request = Request::parse( $request );
        }

        if ( is_string( $target ) )
        {// (Target is a string)
            // (Getting the value)
            $target = Target::parse( $target );
        }



        // (Initializing the curl)
        $curl = curl_init();

        if ( $curl === false )
        {// (Unable to initialize the cURL object)
            // Returning the value
            return false;
        }



        // (Getting the value)
        $options =
        [
            CURLOPT_URL            => $target . $request->path,
            CURLOPT_CUSTOMREQUEST  => $request->method,
            CURLOPT_HTTPHEADER     => $request->headers,
            CURLOPT_POSTFIELDS     => $request->body,

            CURLOPT_HEADER         => 1,

            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,

            CURLOPT_CONNECTTIMEOUT => $this->conn_timeout,
            CURLOPT_TIMEOUT        => $this->exec_timeout,

            CURLOPT_MAXREDIRS      => $this->max_redirs,

            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => false,

            #CURLOPT_VERBOSE        => 1
        ]
        ;

        if ( !curl_setopt_array( $curl, $options ) )
        {// (Unable to set the options)
            // Returning the value
            return false;
        }



        // (Executing the curl)
        $content = curl_exec( $curl );

        if ( $content === false )
        {// (Unable to executing the cURL)
            // Returning the value
            return false;
        }



        // (Closing the cURL)
        curl_close( $curl );



        // (Creating a Result)
        $result = new Result( $curl );



        // (Getting the value)
        $parts = explode( "\r\n\r\n", $content );

        for ( $i = 0; $i < count($parts) - 1; $i++ )
        {// Iterating each index
            // (Getting the value)
            $head_parts = explode( "\r\n", $parts[$i] );



            // (Getting the value)
            $first_parts = explode( ' ', $head_parts[0], 3 );



            // (Adding the hop)
            $result->add_hop( new Hop( $first_parts[0], new Status( $first_parts[1], $first_parts[2] ), array_splice( $head_parts, 1 ) ) );
        }



        // (Getting the value)
        $last_hop = $result->hops[ count( $result->hops ) - 1 ];



        // (Getting the value)
        $body = $parts[ count($parts) - 1 ];



        // (Setting the response)
        $result->set_response( new Response( $last_hop->status->code, $last_hop->headers, $body ) );



        // Returning the value
        return $result;
    }
}



?>