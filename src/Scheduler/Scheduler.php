<?php



namespace Solenoid\X;



use Solenoid\X\Scheduler\Task;



class Scheduler
{
    const TIME_UNITS =
    [
        'SECOND' => 1,
        'MINUTE' => 60,
        'HOUR'   => 3600,
        'DAY'    => 86400
    ]
    ;



    public bool  $enabled = false;
    public array $tasks   = [];



    private static function match_rules (array $rules, int $timestamp) : array
    {
        // (Getting the value)
        $day_ts = strtotime( date('Y-m-d') . ' 00:00:00' );



        // (Setting the value)
        $matches = [];

        foreach ( $rules as $rule )
        {// Processing each entry
            // (Getting the values)
            $parts = explode( ' ', $rule );

            switch ( $parts[0] )
            {
                case 'EVERY':
                    // (Getting the values)
                    $factor = (int) $parts[1];
                    $unit   = $parts[2];



                    // (Getting the values)
                    $start_ts = $parts[4] ? strtotime( $parts[4] ) : $day_ts;
                    $end_ts   = $parts[6] ? strtotime( $parts[6] ) : false;



                    // (Getting the value)
                    $ts_exceeded = $end_ts !== false && $timestamp >= $end_ts;

                    if ( !$ts_exceeded )
                    {// Match OK
                        switch ( $unit )
                        {
                            case 'SECOND':
                            case 'MINUTE':
                            case 'HOUR':
                            case 'DAY':
                                // (Getting the value)
                                $duration = $factor * self::TIME_UNITS[ $unit ];
                            break;

                            case 'WEEK':
                                // (Getting the value)
                                $duration = strtotime( "+$factor week", $start_ts ) - $start_ts;
                            break;

                            case 'MONTH':
                                // (Getting the value)
                                $duration = strtotime( "+$factor month", $start_ts ) - $start_ts;
                            break;

                            case 'YEAR':
                                // (Getting the value)
                                $duration = strtotime( "+$factor year", $start_ts ) - $start_ts;
                            break;

                            default:
                                // Returning the value
                                return [];
                        }



                        if ( ( $timestamp - $start_ts ) % $duration === 0 )
                        {// (Delta-Timestamp is a multiple of duration)
                            // (Appending the value)
                            $matches[] = $rule;
                        }
                    }
                break;

                case 'AT':
                    // (Getting the value)
                    $end_ts = $parts[3] ? strtotime( $parts[3] ) : false;



                    // (Getting the value)
                    $ts_exceeded = $end_ts !== false && $timestamp >= $end_ts;

                    if ( !$ts_exceeded )
                    {// Match OK
                        if ( date( 'H:i:s', $timestamp ) === $parts[1] )
                        {// (HMS is the same)
                            // (Appending the value)
                            $matches[] = $rule;
                        }
                    }
                break;

                default:
                    // Returning the value
                    return [];
            }
        }



        // Returning the value
        return $matches;
    }



    public function add_task (Task $task) : self
    {
        // (Appending the value)
        $this->tasks[] = $task;



        // Returning the value
        return $this;
    }



    public function match_tasks (int $timestamp) : array
    {
        // (Setting the value)
        $tasks = [];



        if ( !$this->enabled ) return [];

        foreach ( $this->tasks as $task )
        {// Processing each entry
            if ( !$task->enabled ) continue;

            foreach ( $task->rules as $rule )
            {// Processing each entry
                // (Getting the value)
                $matches = self::match_rules( $rule, $timestamp );

                if ( !$matches ) continue;



                // (Appending the value)
                $tasks[] = $task;
            }
        }



        // Returning the value
        return $tasks;
    }
}



?>