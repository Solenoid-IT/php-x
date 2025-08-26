<?php



namespace Solenoid\X\HTTP;



use \Solenoid\X\HTTP\Cookie;



class Session
{
    private Cookie $cookie;
    private ?int   $duration;
    private bool   $persistent;

    private array  $event_listeners;
    private array  $handlers;



    public ?string $found_id;
    public ?string $generated_id;
    public ?string $destroyed_id;

    public ?string $id;

    public int     $creation_timestamp;
    public ?int    $last_update_timestamp;
    public ?int    $expiration_timestamp;

    public array   $data;

    public bool    $closed;



    public function __construct (Cookie $cookie, ?int $duration = null, bool $persistent = false)
    {
        // (Getting the values)
        $this->cookie     = $cookie;
        $this->duration   = $duration;
        $this->persistent = $persistent;



        // (Setting the values)
        $this->found_id      = null;
        $this->generated_id  = null;
        $this->destroyed_id  = null;

        $this->id            = null;

        $this->closed        = false;



        // (Setting the values)
        $this->event_listeners = [];
        $this->handlers        = [];
    }



    public function start () : self|false
    {
        // (Getting the value)
        $id = $_COOKIE[ $this->cookie->name ] ?? null;

        if ( $id === null )
        {// (Cookie not found)
            // (Getting the value)
            $id = $this->handlers[ 'generate_id' ]( $this );



            // (Getting the values)
            $this->creation_timestamp    = time();
            $this->last_update_timestamp = null;
            $this->expiration_timestamp  = $this->duration ? $this->creation_timestamp + $this->duration : null;
            $this->data                  = [];



            // (Getting the value)
            $this->generated_id = $id;
        }
        else
        {// (Cookie found)
            if ( !$this->handlers[ 'validate_id' ]( $this, $id ) )
            {// (Validation is failed)
                // Returning the value
                return false;
            }



            // (Getting the value)
            $object = $this->handlers[ 'find' ]( $this, $id );

            if ( $object )
            {// (Resource found)
                // (Getting the values)
                $this->creation_timestamp    = $object['creation_timestamp'];
                $this->last_update_timestamp = $object['last_update_timestamp'];
                $this->expiration_timestamp  = $object['expiration_timestamp'];
                $this->data                  = $object['data'];



                if ( $this->expiration_timestamp <= time() )
                {// (Resource is expired)
                    // (Setting the value)
                    $this->data = [];
                }
            }
            else
            {// (Resource not found)
                // (Getting the values)
                $this->creation_timestamp    = time();
                $this->last_update_timestamp = null;
                $this->expiration_timestamp  = $this->duration ? $this->creation_timestamp + $this->duration : null;
                $this->data                  = [];
            }



            // (Getting the value)
            $this->found_id = $id;
        }



        // (Getting the value)
        $this->id = $id;



        /*

        if ( $this->generated_id )
        {// Value found
            // (Setting the cookie)
            $this->cookie->set( $this->id, $this->persistent ? $this->expiration_timestamp : null );
        }

        */



        // Returning the value
        return $this;
    }

    public function regenerate_id () : self|false
    {
        if ( !$this->id )
        {// (Session has not been started yet)
            // Returning the value
            return false;
        }



        if ( $this->generated_id )
        {// (ID has been already generated)
            // Returning the value
            return $this;
        }



        // (Getting the value)
        $new_id = $this->handlers[ 'generate_id' ]( $this );



        if ( !$this->handlers[ 'change_id' ]( $this, $new_id ) )
        {// (Unable to change the id)
            // Returning the value
            return false;
        }



        // (Getting the value)
        $this->id = $new_id;



        // (Setting the cookie)
        #$this->cookie->set( $this->id, $this->persistent ? $this->expiration_timestamp : null );



        // Returning the value
        return $this;
    }

    public function set_duration (?int $duration = null) : self|false
    {
        if ( !$this->id )
        {// (Session has not been started yet)
            // Returning the value
            return false;
        }



        // (Getting the value)
        $expiration_timestamp = $this->handlers[ 'set_duration' ]( $this, $duration ?? $this->duration );

        if ( $expiration_timestamp === false )
        {// (Unable to set the duration)
            // Returning the value
            return false;
        }



        // (Getting the value)
        $this->expiration_timestamp = $expiration_timestamp;



        // (Setting the cookie)
        #$this->cookie->set( $this->id, $this->persistent ? $this->expiration_timestamp : null );



        // Returning the value
        return $this;
    }



    public function destroy () : self|false
    {
        if ( !$this->id )
        {// (Session has not been started yet)
            // Returning the value
            return false;
        }



        if ( !$this->handlers[ 'delete' ]( $this ) )
        {// (Unable to delete the resource)
            // Returning the value
            return false;
        }



        // (Unsetting the cookie)
        #$this->cookie->unset();



        // (Getting the value)
        $this->destroyed_id = $this->id;



        // (Setting the value)
        $this->id = null;



        // Returning the value
        return $this;
    }



    public function write () : self|false
    {
        if ( $this->closed )
        {// (Session has been closed)
            // Returning the value
            return false;
        }

        if ( !$this->id )
        {// (Session has not been started yet)
            // Returning the value
            return false;
        }

        if ( $this->destroyed_id )
        {// (Session has been destroyed)
            // Returning the value
            return false;
        }



        // (Getting the value)
        $this->last_update_timestamp = time();



        if ( !$this->handlers[ 'update' ]( $this ) )
        {// (Unable to update the resource)
            // Returning the value
            return false;
        }



        // (Triggering the event)
        $this->trigger_event( 'update', $this );



        // Returning the value
        return $this;
    }



    public function close () : self|false
    {
        if ( !$this->id )
        {// (Session has not been started yet)
            // Returning the value
            return false;
        }



        // (Setting the value)
        $this->closed = true;



        // Returning the value
        return $this;
    }



    public function on (string $type, callable $callback) : self
    {
        // (Appending the value)
        $this->event_listeners[ $type ][] = $callback;



        // Returning the value
        return $this;
    }

    public function trigger_event (string $type, mixed $data) : self
    {
        foreach ( $this->event_listeners[ $type ] as $callback )
        {// Processing each entry
            // (Calling the function)
            $callback( $data );
        }



        // Returning the value
        return $this;
    }



    public function set_handler (string $type, callable $callback) : self
    {
        // (Getting the value)
        $this->handlers[ $type ] = $callback;



        // Returning the value
        return $this;
    }



    public function __destruct ()
    {
        if ( $this->destroyed_id )
        {// (Resource has been deleted)
            // (Unsetting the cookie)
            $this->cookie->unset();
        }
        else
        {// (Resource has not been deleted)
            // (Setting the cookie)
            $this->cookie->set( $this->id, $this->persistent ? $this->expiration_timestamp : null );
        }



        // (Updating the resource)
        $this->write();
    }
}



?>