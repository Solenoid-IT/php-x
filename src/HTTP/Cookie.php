<?php



namespace Solenoid\X\HTTP;



class Cookie
{
    public readonly string       $name;

    public readonly string     $domain;
    public readonly string       $path;

    public readonly bool       $secure;
    public readonly bool    $http_only;

    public readonly string  $same_site;



    public function __construct (string $name, string $domain = '', string $path = '/', bool $secure = false, bool $http_only = false, string $same_site = 'Lax')
    {
        // (Getting the values)
        $this->name      = $name;

        $this->domain    = $domain;
        $this->path      = $path;

        $this->secure    = $secure;
        $this->http_only = $http_only;

        $this->same_site = $same_site;
    }



    public function set (string $value, ?int $expiration_timestamp = null) : bool
    {
        // (Getting the value)
        $components =
        [
            'Expires'  => $expiration_timestamp === null ? '' : 'Expires=' . gmdate( DATE_COOKIE, $expiration_timestamp ) . ';',
            'Domain'   => $this->domain ? 'Domain=' . $this->domain . ';' : '',
            'Path'     => $this->path ? 'Path=' . $this->path . ';' : '',
            'Secure'   => $this->secure ? 'Secure;' : '',
            'HttpOnly' => $this->http_only ? 'HttpOnly;' : '',
            'SameSite' => 'SameSite=' . $this->same_site . ';'
        ]
        ;



        // (Getting the value)
        $components = trim( implode( ' ', array_values( array_filter( $components, function ($component) { return $component !== ''; } ) ) ) );



        // (Getting the value)
        $header = "Set-Cookie: {$this->name}=$value; $components";



        // (Setting the header)
        header( $header, false );

        if ( !in_array( $header, headers_list() ) )
        {// (Unable to send the header)
            // Returning the value
            return false;
        }



        // Returning the value
        return true;
    }

    public function unset () : bool
    {
        // Returning the value
        return $this->set( '', -1 );
    }



    public static function fetch (string $name) : string|null
    {
        // Returning the value
        return $_COOKIE[ $name ] ?? null;
    }

    public static function delete (string $name, string $domain = '', string $path = '/') : bool
    {
        // Returning the value
        return ( new self( $name, $domain, $path ) )->set( '', -1 );
    }
}



?>