<?php



namespace Solenoid\X\Data;



use \Attribute;

use \Solenoid\X\Data\Types\Value;



#[ Attribute( Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY ) ]
class ArrayList
{
    private Value|DTO|string $reference;

    private ?int $min_size;
    private ?int $max_size;

    protected bool  $is_valid;
    protected array $instances;

    private ?string $pre_error = null;



    public function __construct (Value|DTO|string $reference, ?int $min_size = 0, ?int $max_size = null)
    {
        // (Getting the value)
        $this->reference = $reference;



        // (Getting the values)
        $this->min_size  = $min_size;
        $this->max_size  = $max_size;
    }



    public function validate (mixed $data) : bool
    {
        // (Setting the value)
        $this->is_valid = true;



        if ( !is_array( $data ) )
        {// (Value is not an array)
            // (Setting the value)
            $this->is_valid = false;



            // (Getting the value)
            $this->pre_error = "List of " . ( $this->reference instanceof Value ? 'Value' : $this->reference ) . " is required";



            // Returning the value
            return false;
        }



        // (Getting the value)
        $num_elements = count( $data );

        if ( $this->min_size !== null && $num_elements < $this->min_size )
        {// (Limit exceeded)
            // (Setting the value)
            $this->is_valid = false;



            // (Getting the value)
            $this->pre_error = "List of " . ( $this->reference instanceof Value ? 'Value' : $this->reference ) . " must not contain less than {$this->min_size} elements";



            // Returning the value
            return false;
        }

        if ( $this->max_size !== null && $num_elements > $this->max_size )
        {// (Limit exceeded)
            // (Setting the value)
            $this->is_valid = false;



            // (Getting the value)
            $this->pre_error = "List of " . ( $this->reference instanceof Value ? 'Value' : $this->reference ) . " must not contain more than {$this->max_size} elements";



            // Returning the value
            return false;
        }



        // (Setting the value)
        $this->instances = [];

        foreach ( $data as $i => $item_data )
        {// Processing each entry
            if ( $this->reference instanceof Value )
            {// (Reference is a Value)
                // (Getting the value)
                $copy = clone $this->reference;
                
                if ( !$copy->validate( $item_data ) )
                {// Match failed
                    // (Setting the value)
                    $this->is_valid = false;
                }



                // (Appending the value)
                $this->instances[] = $copy;
            }
            else
            if ( is_string( $this->reference ) && is_subclass_of( $this->reference, DTO::class ) )
            {// (Reference is a DTO)
                if ( !( $item_data instanceof $this->reference || is_array( $item_data ) ) )
                {// Match failed
                    // (Setting the value)
                    $this->is_valid = false;



                    // (Appending the value)
                    $this->instances[] = "Entry #$i is not an instance of {$this->reference}";



                    // Continuing the iteration
                    continue;
                }



                // (Getting the value)
                $analysis = ( $this->reference )::analyze( $item_data, true );
                
                if ( !$analysis->valid )
                {// (Validation failed)
                    // (Setting the value)
                    $this->is_valid = false;
                }



                // (Appending the value)
                $this->instances[] = $analysis;
            }
        }



        // Returning the value
        return $this->is_valid;
    }



    public function get_error () : string|array|null
    {
        if ( $this->pre_error !== null )
        {// Value found
            // Returning the value
            return $this->pre_error;
        }



        if ( !isset( $this->instances ) )
        {// Value not found
            // (Getting the value)
            $type = $this->reference instanceof DTO ? 'DTO' : 'Value';

            // Returning the value
            return "list of $type is required";
        }



        // (Setting the value)
        $has_error = false;



        // (Setting the value)
        $errors = [];

        foreach ( $this->instances as $i => $instance )
        {// Processing each entry
            if ( $this->reference instanceof Value )
            {// (Reference is a Value)
                // (Getting the value)
                $error = $instance->get_error();
            }
            else
            if ( is_string( $this->reference ) && is_subclass_of( $this->reference, DTO::class ) )
            {// (Reference is a DTO)
                // (Getting the value)
                $error = $instance->valid ? null : $instance->errors;
            }



            if ( $error !== null )
            {// (Error found)
                // (Setting the value)
                $has_error = true;
            }



            // (Getting the value)
            $errors[ $i ] = $error;
        }



        // Returning the value
        return $has_error ? $errors : null;
    }

    public function get_value () : array
    {
        // Returning the value
        return array_map( function ($instance) { return $this->reference instanceof Value ? $instance->get_value() : $instance->input; }, $this->instances );
    }



    public function get_reference () : Value|DTO|string
    {
        // Returning the value
        return $this->reference;
    }



    public function is_valid () : bool
    {
        // Returning the value
        return $this->is_valid;
    }
}



?>