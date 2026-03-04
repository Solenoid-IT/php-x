<?php



namespace Solenoid\X;



use \Monolog\Logger as Monolog;
use \Monolog\Handler\RotatingFileHandler;
use \Monolog\Processor\IntrospectionProcessor;



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



    public function __construct (public readonly string $file_path, private int $max_files = 7, private string $channel = 'main')
    {
        // (Getting the value)
        $this->monolog = new Monolog( $channel );



        // (Setting the handler)
        $this->monolog->pushHandler( new RotatingFileHandler( $file_path, $max_files ) );



        // (Setting the processor)
        $this->monolog->pushProcessor( new IntrospectionProcessor() );
    }



    public function log (int|string $level, string $message) : self
    {
        // (Getting the value)
        $level = is_string( $level ) ? self::MAP[ $level ] : $level;



        // (Logging the message)
        $this->monolog->log( $level, $message );



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