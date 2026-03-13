<?php



namespace Solenoid\X\CLI;



class Task
{
    private array $schedules = [];



    public function __construct (public readonly string $class, public readonly string $method = 'run') {}



    /**
     * Adds a schedule to this task.
     * @param Schedule $schedule The schedule to add.
     * @return self Returns the current Task instance for method chaining.
     */
    public function add_schedule (Schedule $schedule) : self
    {
        // (Appending the value)
        $this->schedules[] = $schedule;



        // Returning the value
        return $this;
    }

    /**
     * Returns an array of schedules associated with this task.
     * @return array<Schedule> An array of schedules.
     */
    public function list_schedules () : array
    {
        // Returning the value
        return $this->schedules;
    }



    /**
     * Scans the specified directory for PHP files, looking for each class methods (tasks), and returns an array of tasks with their associated class, method, and schedules.
     * @param string $directory The directory to scan for PHP files.
     * @param string $namespace_prefix The namespace prefix to use when constructing class names from file
     * @return array<Task> An array of tasks.
     */
    public static function scan (string $directory, string $namespace_prefix = 'App\\Tasks') : array
    {
        // (Setting the value)
        $tasks = [];

        foreach ( new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $directory ) ) as $file )
        {// Processing each entry
            if ( $file->isDir() || $file->getExtension() !== 'php' ) continue;



            // (Getting the value)
            $relative_path = str_replace( [ $directory, '.php', '/' ], [ '', '', '\\' ], $file->getRealPath() );



            // (Getting the value)
            $class = rtrim( $namespace_prefix, '\\' ) . '\\' . ltrim( $relative_path, '\\' );

            if ( !class_exists( $class ) ) continue;



            foreach ( new \ReflectionClass( $class ) as $method )
            {// Processing each entry
                // (Getting the value)
                $task = new Task( $class, $method->getName() );

                foreach ( $method->getAttributes( Schedule::class ) as $attribute )
                {// Processing each entry
                    // (Adding the schedule)
                    $task->add_schedule( $attribute->newInstance() );
                }



                // (Appending the value)
                $tasks[] = $task;
            }
        }



        // Returning the value
        return $tasks;
    }



    public function __toString () : string
    {
        // Returning the value
        return $this->class . '::' . $this->method . '()';
    }
}



?>