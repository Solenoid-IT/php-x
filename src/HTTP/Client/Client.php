<?php



namespace Solenoid\X\HTTP\Client;



class Client
{
    /*public static function send (string $request, string $host)
    {
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
            CURLOPT_URL            => $url,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => is_string($body) ? $body : json_encode($body),

            CURLOPT_HEADER         => 1,

            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,

            CURLOPT_CONNECTTIMEOUT => $conn_timeout,
            CURLOPT_TIMEOUT        => $exec_timeout,

            CURLOPT_MAXREDIRS      => $max_redirs,

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



        // (Setting the value)
        $heads = [];



        // (Getting the value)
        $parts = explode( "\r\n\r\n", $content );

        for ( $i = 0; $i < count($parts) - 1; $i++ )
        {// Iterating each index
            // (Getting the value)
            $head_parts = explode( "\r\n", $parts[$i] );



            // (Getting the value)
            $first_parts = explode( " ", $head_parts[0], 3 );



            // (Appending the value)
            $heads[] = new ResponseHead
            (
                $first_parts[0],
                new Status
                (
                    $first_parts[1],
                    $first_parts[2]
                ),
                array_splice( $head_parts, 1 )
            )
            ;
        }



        // (Getting the value)
        $body = $parts[ count($parts) - 1 ];
        $body = strpos( $heads[ count($heads) - 1 ]->get('Content-Type') ?? '', 'application/json' ) === 0 ? json_decode( $body, true ) : $body;



        // (Getting the value)
        $response = new Response
        (
            $heads,
            $body,

            new RequestError( curl_errno( $curl ), curl_error( $curl ) ),
            curl_getinfo( $curl )
        )
        ;



        // Returning the value
        return $response;
    }*/
}



?>