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



    public function ip (string $fqdn)
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

    public function error (int $code, string $description = '') : Error|null
    {
        // (Getting the value)
        $error = $this->errors[ $code ] ?? null;

        if ( $error )
        {// Value found
            // (Getting the value)
            $new_error = new Error( $error->code, $error->name, $description );

            if ( $error->http_code )
            {// Value found
                // (Getting the value)
                $new_error->set_http_code( $error->http_code );
            }
        }



        // Returning the value
        return $new_error;
    }
}



?>