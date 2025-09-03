<?php



namespace Solenoid\X;



class Error
{
    private static array $map = [];



    public readonly int    $code;
    public readonly string $name;
    public readonly string $description;



    public function __construct (int $code, string $name = '', string $description = '')
    {
        // (Getting the values)
        $this->code        = $code;
        $this->name        = $name;
        $this->description = $description;
    }



    public static function get (int $code) : self|null
    {
        // Returning the value
        return self::$map[ $code ] ?? null;
    }

    public static function register (self $error) : void
    {
        // (Getting the value)
        self::$map[ $error->code ] = $error;
    }



    public function __toString () : string
    {
        // Returning the value
        return "Error {$this->code} :: {$this->name} :: {$this->description}";
    }
}



?>