<?php



namespace Solenoid\X;



use \Monolog\Logger as Monolog;
use \Monolog\Handler\StreamHandler;
use \Monolog\Formatter\LineFormatter;



class Logger
{
    const EMERGENCY = 0;# 'system is unusable'
    const ALERT     = 1;# 'action must be taken immediately'
    const CRITICAL  = 2;# 'critical'
    const ERROR     = 3;# 'error'
    const WARNING   = 4;# 'warning'
    const NOTICE    = 5;# 'normal'
    const INFO      = 6;# 'info message'
    const DEBUG     = 7;# 'debug message'

    const MAP =
    [
        'emergency' => self::EMERGENCY,
        'alert'     => self::ALERT,
        'critical'  => self::CRITICAL,
        'error'     => self::ERROR,
        'warning'   => self::WARNING,
        'notice'    => self::NOTICE,
        'info'      => self::INFO,
        'debug'     => self::DEBUG,

        'EM'        => self::EMERGENCY,
        'A'         => self::ALERT,
        'C'         => self::CRITICAL,
        'E'         => self::ERROR,
        'W'         => self::WARNING,
        'N'         => self::NOTICE,
        'I'         => self::INFO,
        'D'         => self::DEBUG,
    ]
    ;

    const DURATIONS =
    [
        's' => 1,
        'm' => 60,
        'h' => 3600,
        'd' => 86400,
        'w' => 7 * 86400
    ]
    ;

    const SIZES =
    [
        'B'  => 1,
        'KB' => [ 10, 3 ],
        'MB' => [ 10, 6 ],
        'GB' => [ 10, 9 ],
        'TB' => [ 10, 12 ],
        'PB' => [ 10, 15 ]
    ]
    ;



    private string  $file_name;
    private string  $archive_folder_path;

    private ?int     $duration = 7 * 86400;# '7 days'
    private ?int     $size = 128000000;# '128 MB'

    private ?int     $archive_duration = 30 * 86400;# '30 days'

    private Monolog $monolog;



    private function rotate_files () : void
    {
        if ( !file_exists( $this->file_path ) ) return;



        // (Getting the values)
        $this->file_name           = pathinfo( $this->file_path, PATHINFO_FILENAME );
        $this->archive_folder_path = dirname( $this->file_path ) . "/{$this->file_name}.archive";



        // (Getting the values)
        $now_time           = time();
        $last_modified_time = filemtime( $this->file_path );



        // (Getting the values)
        $duration_exceeded = $this->duration ? ( $now_time - $last_modified_time > $this->duration ) : false;
        $size_exceeded     = $this->size ? ( filesize( $this->file_path ) > $this->size ) : false;

        if ( $duration_exceeded || $size_exceeded )
        {// (Limit has been exceeded)
            if ( !is_dir( $this->archive_folder_path ) )
            {// (Directory not found)
                if ( !mkdir( $this->archive_folder_path, 0777, true ) )
                {// (Unable to create the directory)
                    // Throwing the exception
                    throw new \Exception( "Unable to create the directory '{$this->archive_folder_path}'" );
                }
            }



            // (Getting the value)
            $last_modified_date = date( 'Y-m-d', $last_modified_time );



            // (Getting the value)
            $archived_file_path = "{$this->archive_folder_path}/{$this->file_name}_{$last_modified_date}_$now_time.log";

            if ( !rename( $this->file_path, $archived_file_path ) )
            {// (Unable to move the file)
                // Throwing the exception
                throw new \Exception( "Unable to move the file '{$this->file_path}' to '$archived_file_path'" );
            }
        }



        if ( $this->archive_duration )
        {// Value found
            if ( is_dir( $this->archive_folder_path ) )
            {// (Directory found)
                foreach ( scandir( $this->archive_folder_path ) as $file_name )
                {// Processing each entry
                    // (Getting the value)
                    $file_path = $this->archive_folder_path . '/' . $file_name;

                    if ( $now_time - filemtime( $file_path ) > $this->archive_duration )
                    {// (Limit has been exceeded)
                        if ( !unlink( $file_path ) )
                        {// (Unable to delete the file)
                            // Throwing the exception
                            throw new \Exception( "Unable to delete the file '$file_path'" );
                        }
                    }
                }
            }
        }
    }



    public function __construct (public readonly string $file_path, private string $channel = 'main', private bool $pid = false)
    {
        // (Rotating files)
        $this->rotate_files();



        // (Getting the value)
        $handler = new StreamHandler( $this->file_path );



        // (Getting the value)
        $pid = $this->pid ? '(' . getmypid() . ') ' : '';



        // (Setting the formatter)
        $handler->setFormatter( new LineFormatter( "%datetime% %channel% %level_name% $pid:: %message%\n", 'c', true, true ) );



        // (Getting the value)
        $this->monolog = new Monolog( $channel );



        // (Setting the handler)
        $this->monolog->pushHandler( $handler );
    }



    public function set_duration (int|string $duration) : self
    {
        if ( is_string( $duration ) )
        {// Value found
            // (Getting the values)
            sscanf( $duration, '%d%s', $number, $factor );



            // (Getting the value)
            $factor = self::DURATIONS[ $factor ] ?? 1;



            // (Getting the value)
            $duration = $factor * $number;
        }



        // (Getting the value)
        $this->duration = $duration;



        // Returning the value
        return $this;
    }

    public function set_size (int|string $size) : self
    {
        if ( is_string( $size ) )
        {// Match OK
            // (Getting the values)
            sscanf( $size, '%d%s', $number, $factor );

            if ( $factor && strlen( $factor ) === 1 ) $factor .= 'B';



            // (Getting the value)
            $factor = self::SIZES[ $factor ] ?? 1;
            $factor = is_array( $factor ) ? pow( $factor[0], $factor[1] ) : $factor;



            // (Getting the value)
            $size = $factor * $number;
        }



        // (Getting the value)
        $this->size = $size;



        // Returning the value
        return $this;
    }



    public function set_archive_duration (int|string $duration) : self
    {
        if ( is_string( $duration ) )
        {// Value found
            // (Getting the values)
            sscanf( $duration, '%d%s', $number, $factor );



            // (Getting the value)
            $factor = self::DURATIONS[ $factor ] ?? 1;



            // (Getting the value)
            $duration = $factor * $number;
        }



        // (Getting the value)
        $this->duration = $duration;



        // Returning the value
        return $this;
    }



    public function rotate () : self
    {
        // (Rotating files)
        $this->rotate_files();



        // Returning the value
        return $this;
    }



    public function push (string $message, int|string $level = 'info') : self
    {
        // (Getting the value)
        $level = is_string( $level ) ? self::MAP[ $level ] : $level;



        // (Logging the message)
        $this->monolog->log( $level, str_replace( [ "\r", "\n", "\t" ], [ '\\r', '\\n', '\\t' ], $message ) );



        // Returning the value
        return $this;
    }



    public function __toString () : string
    {
        // Returning the value
        return $this->file_path;
    }
}



?>