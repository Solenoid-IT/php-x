<?php



namespace Solenoid\X\Testing;



use \Solenoid\X\Data\EndpointReader;



class FluidTester
{
    private array $endpoints;
    private array $results      = [];
    private int   $total_tests  = 0;
    private int   $passed_tests = 0;
    private int   $failed_tests = 0;



    public function __construct (public readonly string $path, private string $token, array $app_errors = [], string $root_namespace = 'App\\Endpoints')
    {
        // (Getting the value)
        $this->endpoints = ( new EndpointReader( $path, $root_namespace ) )->read();
    }



    public function run () : void
    {
        // Printing the value
        echo "=== FLUID API TESTING ===\n\n";



        foreach ( $this->endpoints as $endpoint )
        {// Processing each entry
            echo json_encode($endpoint) . "\n";
        }
    }
}



?>