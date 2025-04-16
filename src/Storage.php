<?php



namespace Solenoid\X;



class Storage
{
    const MODE_APPEND = 'a';
    const MODE_WRITE  = 'w';



    public readonly string $basedir;



    public function __construct (string $basedir)
    {
        // (Getting the value)
        $this->basedir = realpath( $basedir );
    }



    public function read (string $file_path) : string|false
    {
        if ( strpos( $file_path, '..' ) !== false ) return false;



        // (Getting the value)
        $content = file_get_contents( $this->basedir . $file_path );

        if ( $content === false )
        {// (Unable to read the file)
            // Returning the value
            return false;
        }



        // Returning the value
        return $content;
    }

    public function write (string $file_path, string $content, string $mode = self::MODE_WRITE) : self|false
    {
        if ( strpos( $file_path, '..' ) !== false ) return false;



        // (Getting the value)
        $dir = $this->basedir . dirname( $file_path );

        if ( !is_dir( $dir ) )
        {// (Directory not found)
            if ( !mkdir( $dir, 0777, true ) )
            {// (Unable to create the directory)
                // Returning the value
                return false;
            }
        }



        if ( file_put_contents( $this->basedir . $file_path, $content, $mode === self::MODE_APPEND ? FILE_APPEND : 0 ) === false )
        {// (Unable to write the file)
            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }



    public function log (string $file_path, string $message) : self|false
    {
        if ( strpos( $file_path, '..' ) !== false ) return false;



        if ( !$this->write( $file_path, date('c') . ' :: ' . '(' . getmypid() . ')' . ' :: ' . str_replace( "\n", '\\n', $message ) . "\n", self::MODE_APPEND ) )
        {// (Unable to write the file)
            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }
}



?>