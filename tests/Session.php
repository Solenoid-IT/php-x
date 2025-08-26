<?php



use \Solenoid\X\HTTP\Cookie;
use \Solenoid\X\HTTP\Session;



class Test
{
    const APP_FQDN = 'app.dev';
    const BASEDIR  = '/var/www/simba/' . self::APP_FQDN . '/core/sessions';



    public static function run ()
    {
        // (Getting the value)
        $session = new Session( new Cookie( 'user', '', '/', true, true, 'Lax' ), 3600, true );



        // (Setting the handler)
        $session->set_handler('validate_id', function ($session, $id) {
            // Returning the value
            return preg_match( '/^[\w]+$/', $id ) === 1;
        });

        // (Setting the handler)
        $session->set_handler('generate_id', function ($session) {
            // Returning the value
            return bin2hex( random_bytes( 128 / 2 ) );
        });

        // (Setting the handler)
        $session->set_handler('find', function ($session, $id) {
            // (Getting the value)
            $file_path = self::BASEDIR . '/' . $id;

            if ( !file_exists( $file_path ) )
            {// (File not found)
                return false;
            }



            // (Getting the value)
            $object = json_decode( file_get_contents( $file_path ), true );

            // (Getting the value)
            $object['data'] = json_decode( $object['data'], true );



            // Returning the value
            return $object;
        });

        // (Setting the handler)
        $session->set_handler('change_id', function ($session, $new_id) {
            // (Getting the value)
            $file_path = self::BASEDIR . '/' . $session->id;

            if ( !file_exists( $file_path ) )
            {// (File not found)
                return false;
            }



            if ( !rename( $file_path, dirname( $file_path ) . '/' . $new_id ) )
            {// (Unable to rename the file)
                // Returning the value
                return false;
            }



            // Returning the value
            return true;
        });

        // (Setting the handler)
        $session->set_handler('set_duration', function ($session, $duration) {
            // (Getting the value)
            $file_path = self::BASEDIR . '/' . $session->id;

            if ( !file_exists( $file_path ) )
            {// (File not found)
                return false;
            }



            // (Getting the value)
            $object = json_decode( file_get_contents( $file_path ), true );



            // (Getting the value)
            $object['expiration_timestamp'] = $duration ? time() + $duration : null;



            if ( file_put_contents( $file_path, json_encode( $object ) ) === false )
            {// (Unable to write to the file)
                // Returning the value
                return false;
            }



            // Returning the value
            return $object['expiration_timestamp'];
        });

        // (Setting the handler)
        $session->set_handler('delete', function ($session) {
            // (Getting the value)
            $file_path = self::BASEDIR . '/' . $session->id;

            if ( !file_exists( $file_path ) )
            {// (File not found)
                return false;
            }



            if ( !unlink( $file_path ) )
            {// (Unable to remove the file)
                // Returning the value
                return false;
            }



            // Returning the value
            return true;
        });

        // (Setting the handler)
        $session->set_handler('update', function ($session) {
            // (Getting the value)
            $file_path = self::BASEDIR . '/' . $session->id;

            if ( !file_exists( $file_path ) )
            {// (File not found)
                return false;
            }



            // (Getting the value)
            $object =
            [
                'creation_timestamp'    => $session->creation_timestamp,
                'last_update_timestamp' => $session->last_update_timestamp,
                'expiration_timestamp'  => $session->expiration_timestamp,
                'data'                  => json_encode( $session->data )
            ]
            ;



            if ( file_put_contents( $file_path, json_encode( $object ) ) === false )
            {// (Unable to write to the file)
                // Returning the value
                return false;
            }



            // Returning the value
            return true;
        });



        // (Listening for the event)
        $session->on('update', function ($session) { /* custom callback code ... */ });



        // (Starting the session)
        $session->start();



        // (Setting the value)
        $session->data['user'] = 1;



        // (Getting the value)
        $session->data['timestamp'] = date( 'c' );
    }
}



?>