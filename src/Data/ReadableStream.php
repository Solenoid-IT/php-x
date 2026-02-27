<?php



namespace Solenoid\X\Data;



class ReadableStream
{
    const TYPE = 'stream';



    public function __construct (public string $name) {}



    public function validate (mixed $value) : bool
    {
        // Returning the value
        return true;
    }

    public function get_value () : string
    {
        // Returning the value
        return '';
    }
}



?>