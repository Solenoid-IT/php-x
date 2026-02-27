<?php



namespace Solenoid\X\Data;



class Analysis
{
    public function __construct (public bool $valid, public array|DTO $input, public array $errors) {}
}



?>