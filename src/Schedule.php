<?php



namespace Solenoid\X;



use \Attribute;



#[ Attribute( Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE ) ]
class Schedule
{
    private static function calc_seconds (string $interval_part) : int
    {
        if ( preg_match( '/(\d+)?\s*(second|seconds|minute|minutes|hour|hours|day|days|week|weeks)/', $interval_part, $matches ) )
        {// Match OK
            // (Getting the values)
            $number = (int) ( $matches[1] ?? 1 );
            $unit   = $matches[2];
            


            switch ( $unit )
            {
                case 'second':
                case 'seconds':
                    // Returning the value
                    return $number;
                break;

                case 'minute':
                case 'minutes':
                    // Returning the value
                    return $number * 60;
                break;

                case 'hour':
                case 'hours':
                    // Returning the value
                    return $number * 3600;
                break;

                case 'day':
                case 'days':
                    // Returning the value
                    return $number * 86400;
                break;

                case 'week':
                case 'weeks':
                    // Returning the value
                    return $number * 604800;
                break;
            }
        }
        


        // Returning the value
        return 3600;
    }



    public function __construct (public string $interval, public bool $enabled = true) {}



    public function check (int $reference_timestamp) : bool
    {
        if ( !$this->enabled ) return false;



        // (Getting the values)
        $parts         = explode(' AT ', strtoupper($this->interval));
        $interval_part = strtolower(str_replace('EVERY ', '', $parts[0]));
        $at_part       = $parts[1] ?? null;



        // (Getting the value)
        $dt = new \DateTime();



        // (Setting the timestamp)
        $dt->setTimestamp( $reference_timestamp );



        // (Setting the time)
        $dt->setTime( 0, 0, 0 );



        if ( $at_part )
        {// Value found
            // (Getting the value)
            $time = explode( ':', $at_part );



            // (Setting the time)
            $dt->setTime( (int) $time[0], (int) ( $time[1] ?? 0 ), (int) ( $time[2] ?? 0 ) );
        }



        // (Getting the value)
        $current_slot_dt = clone $dt;



        if ( str_contains( $interval_part, 'month' ) || str_contains( $interval_part, 'year' ) )
        {// (Handling month/year intervals)
            // (Modifying the time)
            $current_slot_dt->modify( 'first day of this month' );

            if ( str_contains( $interval_part, 'year' ) )
            {// Match OK
                // (Modifying the time)
                $current_slot_dt->modify( 'first day of January' );
            }
            


            if ( $at_part )
            {// Value found
                // (Getting the value)
                $time = explode( ':', $at_part );



                // (Setting the time)
                $current_slot_dt->setTime( (int) $time[0], (int) ( $time[1] ?? 0 ), (int) ( $time[2] ?? 0 ) );
            }



            // (Getting the timestamp)
            $last_slot_start = $current_slot_dt->getTimestamp();
            
            while ( $current_slot_dt->getTimestamp() <= $reference_timestamp )
            {// Processing each iteration
                // (Getting the timestamp)
                $last_slot_start = $current_slot_dt->getTimestamp();



                // (Modifying the time)
                $current_slot_dt->modify( '+' . $interval_part );



                if ( $current_slot_dt->getTimestamp() > $reference_timestamp ) break;
            }
        }
        else
        {// Match failed
            // (Getting the values)
            $start_timestamp  = $dt->getTimestamp();
            $interval_seconds = self::calc_seconds( $interval_part );



            if ( $reference_timestamp < $start_timestamp ) return false;



            // (Getting the value)
            $elapsed = $reference_timestamp - $start_timestamp;



            // (Getting the value)
            $last_slot_start = $start_timestamp + ( floor( $elapsed / $interval_seconds ) * $interval_seconds );
        }



        // (Getting the value)
        $interval_seconds = self::calc_seconds( $interval_part );

        if ( $interval_seconds <= 60 )
        {// Match OK
            // (Setting the value)
            $tolerance = 1;
        }
        else
        {// Match failed
            // (Getting the value)
            $tolerance = min( 60, $interval_seconds );
        }



        // Returning the value
        return $reference_timestamp >= $last_slot_start && $reference_timestamp < $last_slot_start + $tolerance;
    }



    /**
     * Scans the specified directory for PHP files, looking for methods with the Schedule attribute, and returns an array of schedules with their associated class, method, and command.
     * @param string $directory The directory to scan for PHP files.
     * @param string $namespace_prefix The namespace prefix to use when constructing class names from file
     * @return array An array of schedules, each containing 'action' and 'schedule' keys.
     */
    public static function scan (string $directory, string $namespace_prefix = 'App\\Tasks\\Scheduled') : array
    {
        // (Setting the value)
        $schedules = [];

        foreach ( new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $directory ) ) as $file )
        {// Processing each entry
            if ( $file->isDir() || $file->getExtension() !== 'php' ) continue;



            // (Getting the value)
            $relative_path = str_replace( [ $directory, '.php', '/' ], [ '', '', '\\' ], $file->getRealPath() );



            // (Getting the value)
            $class = rtrim( $namespace_prefix, '\\' ) . '\\' . ltrim( $relative_path, '\\' );

            if ( !class_exists( $class ) ) continue;



            // (Getting the value)
            $reflection = new \ReflectionClass( $class );
            
            foreach ( $reflection->getMethods() as $method )
            {// Processing each entry
                foreach ( $method->getAttributes( self::class ) as $attribute )
                {// Processing each entry
                    // (Appending the value)
                    $schedules[] =
                    [
                        'action'   => new Action( $class, $method->getName() ),
                        'schedule' => $attribute->newInstance()
                    ]
                    ;
                }
            }
        }



        // Returning the value
        return $schedules;
    }
}



?>