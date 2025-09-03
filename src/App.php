<?php



namespace Solenoid\X;



use \Solenoid\X\Error;



class App
{
    private array $errors = [];



    public readonly string $mode;
    public readonly string $basedir;



    public function __construct (string $basedir)
    {
        // (Getting the values)
        $this->mode    = isset( $_SERVER['REQUEST_METHOD'] ) ? 'http' : 'cli';
        $this->basedir = $basedir;
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



    public function register_error (Error $error) : self
    {
        // (Getting the value)
        $this->errors[ $error->code ] = $error;



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
            throw new \Exception( "Error code $code is not registered" );
        }



        // (Getting the value)
        $new_error = new Error( $error->code, $error->name, $description );

        if ( $error->http_code )
        {// Value found
            // (Getting the value)
            $new_error->set_http_code( $error->http_code );
        }



        // Returning the value
        return $new_error;
    }
}



?>