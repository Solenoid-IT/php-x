<?php



namespace Solenoid\X\HTTP\Client;



class Target
{
    public readonly string $host;
    public readonly ?int   $port;
    public readonly bool   $secure;



    public function __construct (string $host, ?int $port = null, bool $secure = true)
    {
        // (Getting the values)
        $this->host   = $host;
        $this->port   = $port;
        $this->secure = $secure;
    }



    public static function parse (string $target) : self
    {
        // (Getting the value)
        $parts = parse_url( $target );



        // (Getting the values)
        $host   = $parts['host'] ?? '';
        $port   = $parts['port'] ?? null;
        $secure = isset( $parts['scheme'] ) && strtolower( $parts['scheme'] ) === 'https';



        // Returning the value
        return new self( $host, $port, $secure );
    }



    public function __toString () : string
    {
        // (Returning the value)
        return ( $this->secure ? 'https' : 'http' ) . '://' . $this->host . ( $this->port ? ':' . $this->port : '' );
    }
}



?>