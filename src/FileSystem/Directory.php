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
            $path = $file->getPathname();

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
}



?>