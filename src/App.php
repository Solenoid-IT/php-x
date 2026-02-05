<?php



namespace Solenoid\X;



use \Solenoid\X\Error;
use \Solenoid\X\RuntimeException;



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

        foreach ( $errors as $record )
        {// Processing each entry
            // (Getting the value)
            $error = new Error( $record['code'] );

            if ( isset( $record['name'] ) )
            {// Value found
                // (Setting the name)
                $error->set_name( $record['name'] );
            }

            if ( isset( $record['http_code'] ) )
            {// Value found
                // (Setting the HTTP code)
                $error->set_http_code( $record['http_code'] );
            }



            // (Getting the value)
            $this->errors[ $error->code ] = $error;
        }



        // Returning the value
        return $this;
    }



    public function spawn_error (int $code, string $description = '') : Error
    {
        // (Getting the value)
        $error = $this->errors[ $code ] ?? null;

        if ( !$error )
        {// Value not found
            // Throwing an exception
            throw new \Exception( "Error code {$code} not found" );
        }



        // (Getting the value)
        $new_error = new Error( $code, $description );

        if ( isset( $error->name ) )
        {// Value found
            // (Setting the name)
            $new_error->set_name( $error->name );
        }

        if ( isset( $error->http_code ) )
        {// Value found
            // (Setting the HTTP code)
            $new_error->set_http_code( $error->http_code );
        }



        if ( $this->error === null )
        {// Value not found
            // (Getting the value)
            $this->error = $new_error;
        }



        // Throwing the exception
        throw new RuntimeException( $new_error );



        // Returning the value
        return $new_error;
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