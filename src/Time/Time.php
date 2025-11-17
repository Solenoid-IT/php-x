<?php



namespace Solenoid\X\Time;



class Time
{
    public readonly string $value;
    public readonly string $timezone;



    public function __construct (string $value, string $timezone)
    {
        // (Getting the values)
        $this->value    = $value;
        $this->timezone = $timezone;
    }



    public function convert (string $timezone) : self
    {
        // (Getting the value)
        $datetime = new \DateTime( $this->value, new \DateTimeZone( $this->timezone ) );



        // (Setting the timezone)
        $datetime->setTimezone( new \DateTimeZone( $timezone ) );



        // Returning the value
        return new self( $datetime->format( 'c' ), $timezone );
    }



    public function __toString () : string
    {
        // Returning the value
        return new \DateTime( $this->value, new \DateTimeZone( $this->timezone ) )->format( 'c' );
    }
}



?>