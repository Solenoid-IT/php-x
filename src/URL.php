<?php



namespace Solenoid\X;



use \Solenoid\X\URLSearchParams;



class URL
{
    public readonly string  $scheme;

    public readonly ?string $username;
    public readonly ?string $password;

    public readonly string  $host;
    public readonly ?int    $port;

    public readonly string  $path;
    public readonly string  $query;
    public readonly string  $fragment;

    public readonly URLSearchParams $params;



    public function __construct
    (
        string  $scheme,

        ?string $username = null,
        ?string $password = null,

        string  $host,
        ?int    $port     = null,

        string  $path,
        string  $query    = '',
        string  $fragment = ''
    )
    {
        // (Getting the values)
        $this->scheme   = $scheme;

        $this->username = $username;
        $this->password = $password;

        $this->host     = $host;
        $this->port     = $port;

        $this->path     = $path;
        $this->query    = $query;
        $this->fragment = $fragment;



        // (Getting the value)
        $this->params = new URLSearchParams( $this->query );
    }



    public static function parse (string $value) : self
    {
        // (Getting the value)
        $parts = parse_url( $value );



        // Returning the value
        return new self
        (
            $parts['scheme'],

            $parts['user'],
            $parts['pass'],

            $parts['host'],
            $parts['port'],

            $parts['path'] ?? '/',
            $parts['query'] ? $parts['query'] : '',
            $parts['fragment'] ? $parts['fragment'] : ''
        )
        ;
    }



    public function fetch_base () : string
    {
        // Returning the value
        return
            $this->scheme
                .
            '://'
                .
            ( $this->username && $this->password ? ( $this->username . ':' . $this->password . '@' ) : '' )
                .
            $this->host
                .
            ( $this->port ? ( in_array( $this->port, [ 80, 443 ] ) ? '' : ( ':' . $this->port ) ) : '' )
        ;
    }



    public function get_fullpath () : string
    {
        // Returning the value
        return $this->path . ( $this->query ? ( '?' . $this->query ) : '' );
    }



    public function __toString () : string
    {
        // Returning the value
        return
            $this->fetch_base()
                .
            $this->path
                .
            ( $this->query ? ( '?' . $this->query ) : '' )
                .
            ( $this->fragment ? ( '#' . $this->fragment ) : '' )
        ;
    }
}



?>