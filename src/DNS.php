<?php



namespace Solenoid\X;



class DNS
{
    public static function resolve (string $fqdn) : string
    {
        // (Getting the value)
        $ip = dns_get_record( $fqdn, DNS_A );

        if ( !$ip )
        {// (Unable to resolve the FQDN)
            // Returning the value
            return '';
        }



        // Returning the value
        return $ip[0]['ip'];
    }

    public static function query (string $fqdn, ?string $server = null) : array|false
    {
        // (Setting the value)
        $records = [];



        // (Getting the value)
        $result = shell_exec( 'dig' . ( $server ? ' @' . escapeshellarg( $server ) : '' ) . ' ' . escapeshellarg( $fqdn ) . ' +noall +answer' );

        if ( !$result )
        {// (There are no records)
            // Returning the value
            return false;
        }



        foreach ( explode( "\n", $result ) as $record )
        {// Processing each entry
            // (Getting the value)
            $parts = preg_split( '/\s+/', trim( $record ) );



            // (Appending the value)
            $records[] =
            [
                'name'  => $parts[0],
                'ttl'   => (int) $parts[1],
                'class' => $parts[2],
                'type'  => $parts[3],
                'value' => implode( ' ', array_slice( $parts, 4 ) )
            ]
            ;
        }



        // Returning the value
        return $records;
    }
}



?>