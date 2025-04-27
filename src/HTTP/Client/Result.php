<?php



namespace Solenoid\X\HTTP\Client;



use Solenoid\X\HTTP\Response;



class Result
{
    private $curl;



    public readonly array    $hops;
    public readonly Response $response;



    public function __construct ($curl)
    {
        // (Getting the value)
        $this->curl = $curl;



        // (Setting the value)
        $this->hops = [];
    }



    public function get_error () : Error
    {
        // Returning the value
        return new Error( curl_errno( $this->curl ), curl_error( $this->curl ) );
    }

    public function get_info () : array
    {
        // Returning the value
        return curl_getinfo( $this->curl );
    }



    public function add_hop (Hop $hop) : self
    {
        // (Appending the value)
        $this->hops[] = $hop;



        // Returning the value
        return $this;
    }

    public function set_response (Response $response) : self
    {
        // (Getting the value)
        $this->response = $response;



        // Returning the value
        return $this;
    }
}



?>