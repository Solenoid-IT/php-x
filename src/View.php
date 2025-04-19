<?php



namespace Solenoid\X;



use \eftec\bladeone\BladeOne;



class View
{
    public readonly string $basedir;



    public function __construct (string $basedir)
    {
        // (Getting the value)
        $this->basedir = $basedir;
    }



    public function raw (string $file_path, array $vars = []) : string
    {
        // (Getting the value)
        $blade = new BladeOne( $this->basedir, $this->basedir . '/_cache', BladeOne::MODE_AUTO );



        // Returning the value
        return $blade->run( $file_path, $vars );
    }

    public function html (string $file_path, array $vars = [], array $js_vars = []) : string
    {
        // (Getting the value)
        $content = $this->raw( $file_path, $vars );

        foreach ( $js_vars as $key => $value )
        {// Processing each entry
            // (Getting the value)
            $content = str_replace( '</head>', '<script>const ' . $key . ' = ' . json_encode( $value ) . ';' . '</script>' . '</head>', $content );
        }



        // Returning the value
        return $content;
    }
}



?>