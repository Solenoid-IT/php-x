<?php



namespace Solenoid\X\Testing;



use \Solenoid\X\Data\ClassScanner;



class FluidTester
{
    private array $endpoints;



    public function __construct (string $path, array $app_errors = [], string $root_namespace = 'App\\Endpoints')
    {
        // (Getting the value)
        $this->endpoints = ( new ClassScanner( $path, $app_errors, $root_namespace ) )->scan();
    }



    public function run () : void
    {
        foreach ( $this->endpoints as $endpoint )
        {// Processing each entry
            // ahcid
        }
    }
}



?>