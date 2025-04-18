<?php



namespace Solenoid\X;



class App
{
    public readonly string $mode;
    public readonly string $basedir;



    public function __construct (string $basedir)
    {
        // (Getting the values)
        $this->mode    = isset( $_SERVER['REQUEST_METHOD'] ) ? 'http' : 'cli';
        $this->basedir = $basedir;
    }
}



?>