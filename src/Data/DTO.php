<?php



namespace Solenoid\X\Data;



use \Solenoid\X\Collection;

use \Solenoid\X\Data\Types\Value;



abstract class DTO
{
    protected bool      $is_valid;
    protected \stdClass $property_tree;



    public function validate_OLD (mixed $data) : bool
    {
        // (Setting the value)
        $this->is_valid = true;



        if ( !is_array( $data ) )
        {// (Value is not an array)
            // (Setting the value)
            $this->is_valid = false;

            // Returning the value
            return false;
        }



        // (Getting the value)
        $this->property_tree = new \stdClass();

        foreach ( ( new \ReflectionClass( $this ) )->getProperties( \ReflectionProperty::IS_PUBLIC ) as $property )
        {// Processing each entry
            if ( !$property->isInitialized( $this ) ) continue;



            // (Getting the value)
            $instance = $property->getValue( $this );



            // (Getting the value)
            $name = $property->getName();



            // (Getting the value)
            $raw_value = $data[ $name ] ?? null;



            if ( $instance instanceof Value || $instance instanceof DTO || $instance instanceof ArrayList )
            {// Match OK
                if ( !$instance->validate( $raw_value ) )
                {// (Validation failed)
                    // (Setting the value)
                    $this->is_valid = false;
                }



                // (Getting the value)
                $node = new \stdClass();

                // (Getting the value)
                $node->instance = $instance;



                /* ahcid to implementt

                if ( $instance instanceof DTO )
                {// Match OK
                    // (Getting the value)
                    $node->children = isset( $instance->property_tree ) ? $instance->property_tree : new \stdClass();
                }
                else
                if ( $instance instanceof ArrayList )
                {// Match OK
                    // (Getting the value)  
                    $node->children = array_map( function($inst) { return ( $inst instanceof DTO ) ? $inst->property_tree : null; }, $instance->instances );
                }

                */



                // (Getting the value)
                $this->property_tree->$name = $node;
            }
        }



        // Returning the value
        return $this->is_valid;
    }



    public function get_error () : string|\stdClass|null
    {
        if ( !isset( $this->property_tree ) )
        {// Value not found
            // Returning the value
            return 'object is required';
        }



        // (Getting the value)
        $error_tree = new \stdClass();



        // (Setting the value)
        $has_error = false;



        /* ahcid to deleted

        foreach ( ( new \ReflectionClass( $this ) )->getProperties( \ReflectionProperty::IS_PUBLIC ) as $property )
        {// Processing each entry
            // (Getting the value)
            $name = $property->getName();



            // (Getting the value)
            $instance = $property->getValue( $this );

            if ( $instance instanceof Value )
            {// Match OK
                // (Getting the value)
                $err = $instance->get_error();

                if ( $err )
                {// (Error found)
                    // (Getting the value)
                    $error_tree->$name = $err;

                    // (Setting the value)
                    $has_error = true;
                }
            }
            else
            if ( $instance instanceof DTO )
            {// Match OK
                // (Getting the value)
                $child_errors = $instance->get_error();

                if ( $child_errors !== null )
                {// (Error found)
                    // (Getting the value)
                    $error_tree->$name = $child_errors;

                    // (Setting the value)
                    $has_error = true;
                }
            }
        }

        */



        foreach ( $this->property_tree as $name => $node )
        {// Processing each entry
            // (Getting the value)
            $error = $node->instance->get_error();

            if ( $error === null ) continue;



            // (Getting the value)
            $error_tree->$name = $error;



            // (Setting the value)
            $has_error = true;
        }



        // Returning the value
        return $has_error ? $error_tree : null;
    }

    public function get_value () : self
    {
        // (Getting the value)
        $object = new ( get_class( $this ) )();

        foreach ( $this->property_tree as $name => $node )
        {// Processing each entry
            // (Getting the value)
            $object->$name = $node->instance->get_value();
        }



        // Returning the value
        return $object;
    }



    public function get (string $key, $default = null) : mixed
    {
        // Returning the value
        return ( new Collection( $this ) )->get( $key, $default );
    }

    public function set (string $key, mixed $value) : self
    {
        // (Getting the value)
        $parts = explode( '.', $key );

        if ( count( $parts ) > 1 )
        {// (Parts are multiple)
            // (Getting the values)
            $current_key   = array_shift( $parts );
            $remaining_key = implode( '.', $parts );

            if ( isset( $this->$current_key ) && $this->$current_key instanceof self )
            {// Match OK
                // (Setting the value)
                $this->$current_key->set( $remaining_key, $value );
            }



            // Returning the value
            return $this;
        }



        // (Getting the value)
        $name = $parts[0];



        // (Getting the value)
        $this->$name = $value;



        // Returning the value
        return $this;
    }

    public function unset (string $key) : self
    {
        // (Getting the value)
        $parts = explode( '.', $key );

        if ( count( $parts ) > 1 )
        {// (Parts are multiple)
            // (Getting the values)
            $current_key   = array_shift( $parts );
            $remaining_key = implode( '.', $parts );

            if ( isset( $this->$current_key ) && $this->$current_key instanceof self )
            {// Match OK
                // (Unsetting the value)
                $this->$current_key->unset( $remaining_key );
            }



            // Returning the value
            return $this;
        }



        // (Getting the value)
        $name = $parts[0];



        // (Unsetting the value)
        unset( $this->$name );



        // Returning the value
        return $this;
    }



    public function compress () : array
    {
        // Returning the value
        return ( new Collection( $this ) )->compress();
    }



    public static function get_fieldset (string|self $dto) : WriteFieldset|null
    {
        // (Setting the value)
        $record = null;

        foreach ( ( new \ReflectionClass( $dto ) )->getAttributes( WriteFieldset::class ) as $attribute )
        {// Processing each entry
            // (Appending the value)
            $record = $attribute->newInstance();

            // Breaking the iteration
            break;
        }



        // Returning the value
        return $record;
    }



    public static function from_array (array $data) : static
    {
        // (Getting the value)
        $instance = new static();

        foreach ( $instance as $key => $value )
        {// Processing each entry
            if ( $value instanceof DTO || $value instanceof ArrayList )
            {// (Value is a DTO or ArrayList)
                // (Getting the value)
                $instance->$key = is_array( $data[ $key ] ?? null ) ? get_class( $instance->$key )::from_array( $data[ $key ] ) : null;
            }
            else
            if ( $value instanceof Value )
            {// (Value is a Value)
                // (Getting the value)
                $instance->$key = $data[ $key ] ?? null;
            }
        }



        // Returning the value
        return $instance;
    }



    public static function analyze (array $data, bool $include_input = false) : Analysis
    {
        // (Setting the values)
        $valid  = true;
        $input  = [];
        $errors = [];



        // (Setting the value)
        $map =
        [
            'integer' => 'int',
            'boolean' => 'bool',
            'double'  => 'float'
        ]
        ;



        foreach ( ( new \ReflectionClass( static::class ) )->getProperties( \ReflectionProperty::IS_PUBLIC ) as $property )
        {// Processing each entry
            // (Getting the values)
            $name        = $property->getName();
            $type        = $property->getType();
            $type_name   = $type instanceof \ReflectionNamedType ? $type->getName() : 'mixed';
            $is_required = !$property->hasDefaultValue();

            if ( $is_required )
            {// Value is true
                // (Getting the value)
                $constructor = ( new \ReflectionClass( static::class ) )->getConstructor();

                if ( $constructor )
                {// Value found
                    foreach ( $constructor->getParameters() as $param )
                    {// Processing each entry
                        if ( $param->getName() !== $name ) continue;



                        if ( $param->isDefaultValueAvailable() )
                        {// Match OK
                            // (Setting the value)
                            $is_required = false;



                            // (Getting the value)
                            $default_value = $param->getDefaultValue();



                            // Breaking the iteration
                            break;
                        }
                    }
                }
            }



            if ( !array_key_exists( $name, $data ) )
            {// Value not found
                if ( $is_required )
                {// Value is true
                    // (Getting the value)
                    $errors[ $name ] = "$type_name $name :: Param is required";



                    // (Setting the value)
                    $valid = false;
                }
                else
                {// Value is false
                    if ( $include_input )
                    {// Value is true
                        // (Getting the value)
                        $input[ $name ] = $default_value ?? $property->getDefaultValue();
                    }
                }



                // Continuing the iteration
                continue;
            }



            // (Getting the value)
            $value = $data[ $name ];



            if ( class_exists( $type_name ) && is_subclass_of( $type_name, self::class ) )
            {// Match OK
                if ( is_array( $value ) )
                {// Match OK
                    // (Getting the value)
                    $sub_analysis = $type_name::analyze( $value, $include_input );

                    if ( !$sub_analysis->valid )
                    {// (Validation failed)
                        // (Setting the value)
                        $valid = false;



                        // (Getting the value)
                        $errors[ $name ] = $sub_analysis->errors;
                    }



                    if ( $include_input )
                    {// Value is true
                        // (Getting the value)
                        $input[ $name ] = $sub_analysis->input;
                    }
                }
                else
                if ( $value === null && $type->allowsNull() )
                {// Match OK
                    if ( $include_input )
                    {// Value is true
                        // (Setting the value)
                        $input[ $name ] = null;
                    }
                }
                else
                {// Match failed
                    // (Getting the value)
                    $errors[ $name ] = "$type_name $name :: Expected array for sub-DTO instead of " . gettype( $value );



                    // (Setting the value)
                    $valid = false;
                }



                // Continuing the iteration
                continue;
            }



            if ( $type_name !== 'mixed' )
            {// Match OK
                // (Getting the value)
                $current_type = gettype( $value );



                // (Getting the value)
                $normalized_type = $map[ $current_type ] ?? $current_type;



                // (Getting the value)
                $type_match  = ( $normalized_type === $type_name );
                $class_match = ( $current_type === 'object' && $value instanceof $type_name );
                $null_match  = ( $type->allowsNull() && $value === null );

                if ( !$type_match && !$class_match && !$null_match )
                {// Match failed
                    // (Getting the value)
                    $errors[ $name ] = "$type_name $name :: Expected type '$type_name' instead of '$normalized_type'";



                    // (Setting the value)
                    $valid = false;



                    // Continuing the iteration
                    continue;
                }
            }



            foreach ( $property->getAttributes( Value::class, \ReflectionAttribute::IS_INSTANCEOF ) as $attribute )
            {// Processing each entry
                // (Getting the value)
                $validator = $attribute->newInstance();

                if ( !$validator->validate( $value ) )
                {// (Validation failed)
                    // (Getting the value)
                    $errors[ $name ] = "$type_name $name :: " . $validator->get_error();



                    // (Setting the value)
                    $valid = false;



                    // Continuing the iteration
                    continue;
                }



                // Breaking the iteration
                break;
            }



            // (Setting the value)
            $validator = null;

            foreach ( $property->getAttributes( ArrayList::class, \ReflectionAttribute::IS_INSTANCEOF ) as $attribute )
            {// Processing each entry
                // (Getting the value)
                $validator = $attribute->newInstance();

                if ( !$validator->validate( $value ) )
                {// (Validation failed)
                    // (Getting the value)
                    $errors[ $name ] = /*"$type_name $name :: " . */$validator->get_error();



                    // (Setting the value)
                    $valid = false;



                    // Continuing the iteration
                    continue;
                }



                // Breaking the iteration
                break;
            }



            if ( $validator && $validator->is_valid() )
            {// Value found
                // (Getting the value)
                $value = $validator->get_value();
            }



            if ( $include_input )
            {// Value is true
                // (Getting the value)
                $input[ $name ] = $value;
            }
        }



        if ( $include_input )
        {// Value is true
            // (Getting the value)
            $input = $valid ? new static( ...$input ) : $input;
        }



        // Returning the value
        return new Analysis( $valid, $input, $errors );
    }

    public static function import (array $data, ?array &$errors = null) : static|null
    {
        // (Getting the value)
        $analysis = static::analyze( $data, true );



        // (Getting the value)
        $errors = $analysis->errors;



        // Returning the value
        return $analysis->valid ? $analysis->input : null;
    }
}



?>