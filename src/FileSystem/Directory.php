<?php



namespace Solenoid\X\FileSystem;



class Directory
{
    public readonly string $path;



    public function __construct (string $path)
    {
        // (Getting the value)
        $this->path = $path;
    }



    public function list () : array
    {
        // (Getting the value)
        $rdi = new \RecursiveDirectoryIterator( $this->path );



        // (Getting the value)
        $rii = new \RecursiveIteratorIterator( $rdi );



        // (Setting the value)
        $results = [];

        foreach ( $rii as $file )
        {// Processing each entry
            // (Getting the value)
            $path = substr( $file->getPathname(), strlen( $this->path ) );

            if ( in_array( basename( $path ), [ '.', '..' ] ) ) continue;

            if ( $file->isDir() )
            {// (Entry is a Directory)
                // (Appending the value)
                $path .= '/';
            }



            // (Appending the value)
            $results[] = $path;
        }



        // Returning the value
        return $results;
    }

    public function calc_size () : int
    {
        // (Setting the value)
        $size = 0;

        foreach ( $this->list() as $rel_path )
        {// Processing each entry
            // (Getting the value)
            $path = $this->path . '/' . $rel_path;

            // (Incrementing the value)
            $size += is_dir( $path ) ? 4096 : filesize( $path );
        }



        // Returning the value
        return $size;
    }
}



?>