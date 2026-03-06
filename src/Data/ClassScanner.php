<?php



namespace Solenoid\X\Data;



class ClassScanner
{
    private string $basedir;



    public function __construct (public readonly string $path, private array $app_errors = [], private string $root_namespace = 'App\\Endpoints') {}



    private function recursive_scan (string $current_path) : array
    {
        // (Setting the value)
        $result = [];

        foreach ( scandir( $current_path ) as $entry )
        {// Processing each entry
            if ( in_array( $entry, [ '.', '..' ] ) ) continue;



            // (Getting the value)
            $path = "$current_path/$entry";

            if ( is_dir( $path ) )
            {// (Entry is a Directory)
                // (Appending the value)
                $result[] =
                [
                    'type'    => 'folder',
                    'name'    => $entry,
                    'content' => $this->recursive_scan( $path )
                ]
                ;
            }
            else
            if ( pathinfo( $path, PATHINFO_EXTENSION ) === 'php' )
            {// (Entry is a PHP file)
                // (Getting the value)
                $class = $this->get_class_by_file( $path );

                if ( class_exists( $class ) )
                {// (Class found)
                    // (Getting the value)
                    $methods = ( new ClassInspector( $class, $this->app_errors ) )->get_methods();

                    if ( !empty( $methods ) )
                    {// Value is not empty
                        // (Appending the value)
                        $result[] =
                        [
                            'type'    => 'file',
                            'name'    => $entry,
                            'class'   => $class,
                            'methods' => $methods
                        ]
                        ;
                    }
                }
            }
        }



        // Returning the value
        return $result;
    }

    private function get_class_by_file (string $path) : string
    {
        // Returning the value
        return $this->root_namespace . '\\' . str_replace( '/', '\\', preg_replace( '/\.php$/', '', substr( $path, strlen( $this->basedir ) + 1 ) ) );
    }



    public function scan (string $path) : array
    {
        // (Getting the value)
        $basedir = realpath( $path );

        if ( !$basedir || !is_dir( $basedir ) )
        {// (Directory not found)
            // Throwing the exception
            throw new \Exception( "Directory '{$path}' not found" );
        }



        // (Getting the value)
        $this->basedir = $basedir;



        // Returning the value
        return $this->recursive_scan( $basedir );
    }
}



?>