<?php



namespace Solenoid\X\Data\Generators;



use \Solenoid\X\Data\Types\Value;



abstract class Generator
{
    public function __construct (protected Value $value) {}



    abstract public function generate() : mixed;
}



?>