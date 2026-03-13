<?php



namespace Solenoid\X\CLI;



class Task
{
    private array $schedules = [];
    private bool  $mutex = false;



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
     * Returns whether this task is configured to use a mutex, which prevents multiple instances of the same task from running simultaneously.
     * @return bool True if mutex is enabled, false otherwise.
     */
    public function get_mutex () : bool
    {
        // Returning the value
        return $this->mutex;
    }

    /**
     * Enables or disables the mutex for this task. When enabled, only one instance of the task can run at a time, preventing overlapping executions.
     * @param bool $enabled Set to true to enable mutex, or false to disable it
     * @return self Returns the current Task instance for method chaining.
     */
    public function set_mutex (bool $enabled = true) : self
    {
        // (Getting the value)
        $this->mutex = $enabled;



        // Returning the value
        return $this;
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

                foreach ( $method->getAttributes( Mutex::class ) as $attribute )
                {// Processing each entry
                    // (Enabling the mutex)
                    $task->set_mutex( true );



                    // Breaking the iteration
                    break;
                }



                // (Appending the value)
                $tasks[] = $task;
            }
        }



        // Returning the value
        return $tasks;
    }



    /**
     * Formats the task as a string in the format "My/Custom/Class.method".
     * @return string The formatted task string.
     */
    public function format () : string
    {
        // Returning the value
        return str_replace( '\\', '/', $this->class ) . '.' . $this->method;
    }



    public function __toString () : string
    {
        // Returning the value
        return $this->class . '::' . $this->method . '()';
    }
}



?>