<?php



namespace Solenoid\X;



use \Solenoid\X\Error;



class RuntimeException extends \RuntimeException
{
    public function __construct (public Error $error) {}
}



?>