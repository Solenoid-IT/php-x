<?php



namespace Solenoid\X;



use \Solenoid\X\Dot;



class Element
{
    public int $id;

    public Dot $attributes;
    public Dot $relationships;

    public Dot $meta;



    public function __construct (int $id)
    {
        // (Getting the values)
        $this->id            = $id;
        $this->attributes    = new Dot();
        $this->relationships = new Dot();
        $this->meta          = new Dot();
    }



    public function get (string $key, $default = null) : mixed
    {
        // Returning the value
        return $this->attributes->get( $key, $default );
    }

    public function set (string $key, mixed $value) : self
    {
        // (Setting the value)
        $this->attributes->set( $key, $value );



        // Returning the value
        return $this;
    }

    public function unset (string $key) : self
    {
        // (Unsetting the value)
        $this->attributes->unset( $key );



        // Returning the value
        return $this;
    }



    public function get_relationship (string $name) : array|null
    {
        // Returning the value
        return $this->relationships->$name ?? null;
    }

    public function set_relationship (string $name, array $elements) : self
    {
        // (Getting the value)
        $this->relationships->$name = $elements;



        // Returning the value
        return $this;
    }



    public static function compress (&$element) : void
    {
        if ( is_array( $element ) )
        {// (Value is an array)
            foreach ( $element as &$item )
            {// Processing each entry
                // (Compressing the element)
                self::compress( $item );
            }



            // Returning the value
            return;
        }



        if ( !is_object( $element ) )
        {// (Value is not an object)
            // Returning the value
            return;
        }



        if ( isset( $element->attributes ) && is_object( $element->attributes ) )
        {// Match OK
            foreach ( get_object_vars( $element->attributes ) as $key => $value )
            {// Processing each entry
                // (Getting the value)
                $element->$key = $value;
            }

            // (Removing the element)
            unset( $element->attributes );
        }



        if ( isset( $element->relationships ) && is_object( $element->relationships ) )
        {// Match OK
            foreach ( get_object_vars( $element->relationships ) as $key => $value )
            {// Processing each entry
                // (Compressing the element)
                self::compress( $value );



                // (Getting the value)
                $element->$key = $value;
            }



            // (Removing the element)
            unset( $element->relationships );
        }

        if ( isset( $element->meta ) )
        {// Value found
            // (Removing the element)
            unset( $element->meta );
        }
    }



    public function __get (string $key)
    {
        // Returning the value
        return $this->get( $key );
    }

    public function __set (string $key, mixed $value)
    {
        // (Setting the value)
        $this->set( $key, $value );
    }

    public function __unset (string $key)
    {
        // (Unsetting the value)
        $this->unset( $key );
    }
}



?>