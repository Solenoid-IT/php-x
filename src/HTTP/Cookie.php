<?php



namespace Solenoid\X\HTTP;



class Cookie
{
    private string $header = '';



    public function __construct
    (
        public readonly string $name,
        public readonly string $domain = '',
        public readonly string $path = '/',
        public readonly bool $secure = false,
        public readonly bool $http_only = false,
        public readonly string $same_site = 'Lax'
    )
    {}



    public function set (string $value, ?int $expiration_timestamp = null) : self
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
        $this->header = "Set-Cookie: {$this->name}=$value; $components";



        // Returning the value
        return $this;
    }

    public function unset () : self
    {
        // Returning the value
        return $this->set( '', -1 );
    }



    public function send () : bool
    {
        // (Sending the header)
        header( $this->header, false );

        if ( !in_array( $this->header, headers_list() ) )
        {// (Unable to send the header)
            // Returning the value
            return false;
        }



        // Returning the value
        return true;
    }



    public function __toString () : string
    {
        // Returning the value
        return $this->header;
    }
}



?>