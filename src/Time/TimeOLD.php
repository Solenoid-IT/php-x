<?php



namespace Solenoid\X\Time;



class TimeOLD
{
    private \DateTime $datetime;



    public readonly string $value;



    public function __construct (string $value)
    {
        // (Getting the value)
        $this->value = $value;



        // (Getting the value)
        $this->datetime = new \DateTime( $this->value );
    }



    public function to_local (string $timezone)
    {
        // Returning the value
        return new LocalTime( $this->value, 'UTC' )->to_timezone( $timezone );
    }



    public function format (string $format) : string
    {
        // Returning the value
        return $this->datetime->format( $format );
    }



    public function __toString ()
    {
        // Returning the value
        return $this->datetime->format( 'c' );
    }
}



?>