<?php



namespace Solenoid\X;



use \Solenoid\X\Error;



class App
{
    private Container $container;

    private array $errors        = [];
    private array $conn_profiles = [];

    private ?Error $error = null;



    public readonly string $mode;
    public readonly string $basedir;



    public function __construct (string $basedir)
    {
        // (Getting the values)
        $this->mode    = isset( $_SERVER['REQUEST_METHOD'] ) ? 'http' : 'cli';
        $this->basedir = $basedir;
    }



    public function get_container () : Container
    {
        // Returning the value
        return $this->container;
    }

    public function set_container (Container $container) : self
    {
        // (Getting the value)
        $this->container = $container;



        // Returning the value
        return $this;
    }



    public function ip (string $fqdn) : string|null
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



    public function register_errors (array $errors) : self
    {
        // (Setting the value)
        $this->errors = [];

        foreach ( $errors as $error )
        {// Processing each entry
            // (Getting the value)
            $this->errors[ $error['code'] ] = $error;
        }



        // Returning the value
        return $this;
    }

    public function get_errors () : array
    {
        // Returning the value
        return $this->errors;
    }



    public function spawn_error (int $code, string $message = '') : Error
    {
        // (Getting the value)
        $r = $this->errors[ $code ];

        if ( !$r )
        {// Value not found
            // Throwing an exception
            throw new \Exception( "Error code {$code} not found" );
        }



        // (Getting the value)
        $error = new Error( $code, $message, $r['type'], $r['http_code'], $r['exposed'] === '1' );



        if ( $this->error === null )
        {// Value not found
            // (Getting the value)
            $this->error = $error;
        }



        // Returning the value
        return $error;
    }

    public function get_error () : Error|null
    {
        // Returning the value
        return $this->error;
    }



    public function get_conn_profile (string $type, string $id) : array|null
    {
        // Returning the value
        return $this->conn_profiles[ $type ][ $id ];
    }

    public function set_conn_profiles (string $type, array $profiles) : self
    {
        // (Getting the value)
        $this->conn_profiles[ $type ] = $profiles;



        // Returning the value
        return $this;
    }
}



?>