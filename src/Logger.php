<?php



namespace Solenoid\X;



use \Monolog\Logger as Monolog;
use \Monolog\Handler\RotatingFileHandler;
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



    private Monolog $monolog;



    public function __construct (public readonly string $file_path, private int $max_files = 4, private string $channel = 'main', private bool $pid = false)
    {
        // (Getting the value)
        $this->monolog = new Monolog( $channel );



        // (Getting the value)
        $handler = new RotatingFileHandler( $file_path, $max_files );



        // (Getting the value)
        $pid = $this->pid ? '(' . getmypid() . ') ' : '';



        // (Setting the formatter)
        $handler->setFormatter( new LineFormatter( "%datetime% %channel% %level_name% $pid:: %message%\n", 'c', true, true ) );



        // (Getting the value)
        $start_of_week = new \DateTime();

        if ( $start_of_week->format( 'N' ) !== '1' )
        {// Match failed
            // (Modifying the date)
            $start_of_week->modify( 'last monday' );
        }



        // (Getting the value)
        $end_of_week = clone $start_of_week;
        $end_of_week->modify( 'next sunday' );



        // (Setting the filename format)
        $handler->setFilenameFormat( '{filename}_{date}', $start_of_week->format('Y-m') . '_' . $start_of_week->format('d') . '-' . $end_of_week->format('d') );



        // (Getting the value)
        $last_file_path = $handler->getUrl();

        if ( $last_file_path )
        {// Value found
            if ( !file_exists( $this->file_path ) )
            {// (File not found)
                // (Making the alias)
                symlink( $last_file_path, $this->file_path );
            }
        }



        // (Setting the handler)
        $this->monolog->pushHandler( $handler );
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