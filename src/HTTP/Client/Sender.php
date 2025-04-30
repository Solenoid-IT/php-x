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
    private array $callbacks = [];



    public readonly int $conn_timeout;
    public readonly int $exec_timeout;
    public readonly int $max_redirs;



    private function trigger_event (string $event_type, mixed $data) : self
    {
        foreach ( $this->callbacks[ $event_type ] as $callback )
        {// (Iterating each entry)
            // (Calling the function)
            $callback( $data );
        }



        // Returning the value
        return $this;
    }



    public function __construct (int $conn_timeout = 60, int $exec_timeout = 60, int $max_redirs = 10)
    {
        // (Getting the value)
        $this->conn_timeout = $conn_timeout;
        $this->exec_timeout = $exec_timeout;
        $this->max_redirs   = $max_redirs;
    }



    public function send (string|Request $request, string|Target $target, bool $stream_response = false) : Result|false
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

            CURLOPT_RETURNTRANSFER => !$stream_response,
            CURLOPT_FOLLOWLOCATION => true,

            CURLOPT_CONNECTTIMEOUT => $this->conn_timeout,
            CURLOPT_TIMEOUT        => $this->exec_timeout,

            CURLOPT_MAXREDIRS      => $this->max_redirs,

            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => false,

            #CURLOPT_VERBOSE        => 1
        ]
        ;

        if ( $stream_response )
        {// Value is true
            // (Setting the value)
            $hops = [];

            // (Getting the value)
            $options[ CURLOPT_HEADERFUNCTION ] = function ($curl, $header) use (&$hops)
            {
                // (Getting the value)
                $h = trim( $header );

                if ( $h !== '' )
                {// Match OK
                    if ( strpos( $h, 'HTTP/' ) === 0 )
                    {// Match OK
                        // (Getting the values)
                        [ $protocol, $code, $message ] = explode( ' ', $h, 3 );

                        // (Appending the value)
                        $hops[] = new Hop( $protocol, new Status( $code, $message ?? '' ), [] );
                    }
                    else
                    {// Match failed
                        // (Appending the value)
                        $hops[ count($hops) - 1 ]->headers[] = $h;
                    }
                }



                // Returning the value
                return strlen( $header );
            }
            ;



            // (Setting the value)
            $header_size = 0;

            // (Getting the value)
            $options[ CURLOPT_WRITEFUNCTION ] = function ($curl, $data) use (&$header_size)
            {
                if ( $header_size === curl_getinfo( $curl, CURLINFO_HEADER_SIZE ) )
                {// Match OK
                    // (Triggering the event)
                    $this->trigger_event( 'data', $data );
                }



                // (Incrementing the value)
                $header_size += strlen( $data );



                // (Returning the value)
                return strlen( $data );
            }
            ;
        }



        if ( !curl_setopt_array( $curl, $options ) )
        {// (Unable to set the options)
            // Returning the value
            return false;
        }



        if ( $stream_response )
        {// Value is true
            // (Executing the curl)
            curl_exec( $curl );
        }
        else
        {// Value is false
            // (Executing the curl)
            $content = curl_exec( $curl );

            if ( $content === false )
            {// (Unable to executing the cURL)
                // Returning the value
                return false;
            }
        }



        // (Closing the cURL)
        curl_close( $curl );



        // (Creating a Result)
        $result = new Result( $curl );



        if ( $stream_response )
        {// Value is true
            foreach ( $hops as $hop )
            {// Processing each entry
                // (Adding the hop)
                $result->add_hop( $hop );
            }



            // (Getting the value)
            $last_hop = $result->hops[ count( $result->hops ) - 1 ];



            // (Setting the response)
            $result->set_response( new Response( $last_hop->status->code, $last_hop->headers, '' ) );
        }
        else
        {// Value is false
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
        }



        // Returning the value
        return $result;
    }



    public function on (string $event_type, callable $callback) : self
    {
        // (Appending the value)
        $this->callbacks[ $event_type ][] = $callback;



        // Returning the value
        return $this;
    }
}



?>