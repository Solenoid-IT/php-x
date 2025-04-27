<?php



namespace Solenoid\X\HTTP\Client;



use Solenoid\X\HTTP\Status;



class Hop
{
    public readonly string $protocol;
    public readonly Status $status;
    public readonly array  $headers;



    public function __construct (string $protocol, Status $status, array $headers)
    {
        // (Getting the value)
        $this->protocol = $protocol;
        $this->status   = $status;
        $this->headers  = $headers;
    }



    public function get_header (string $key) : ?string
    {
        foreach ( $this->headers as $header )
        {// Processing each entry
            // (Getting the value)
            $parts = explode( ': ', $header, 2 );

            if ( strtolower( $parts[0] ) === strtolower( $key ) )
            {// Match OK
                // Returning the value
                return $parts[1];
            }
        }



        // Returning the value
        return null;
    }

    public function list_headers (string $key) : array
    {
        // (Setting the value)
        $values = [];

        foreach ( $this->headers as $header )
        {// Processing each entry
            // (Getting the value)
            $parts = explode( ': ', $header, 2 );

            if ( strtolower( $parts[0] ) === strtolower( $key ) )
            {// Match OK
                // (Appending the value)
                $values[] = $parts[1];
            }
        }



        // Returning the value
        return $values;
    }



    public function __toString () : string
    {
        // Returning the value
        return $this->protocol . ' ' . $this->status . "\r\n" . implode( "\r\n", $this->headers ) . "\r\n\r\n";
    }
}



?>