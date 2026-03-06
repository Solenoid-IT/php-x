<?php



namespace Solenoid\X\Data;



use \Solenoid\X\Data\Input;
use \Solenoid\X\Data\Output;
use \Solenoid\X\Data\Types\Value;
use \Solenoid\X\Data\DTO;
use \Solenoid\X\Data\ArrayList;
use \Solenoid\X\Data\ReadableStream;

use \Solenoid\X\CodeAnalyzer;



class ClassInspector
{
    private \ReflectionClass $reflection;

    private array $methods = [];
    private array $data    = [];



    private static function get_type (mixed $reference) : string|null
    {
        if ( $reference instanceof Value ) return 'Value';
        if ( is_string( $reference ) && is_subclass_of( $reference, DTO::class ) ) return 'DTO';
        if ( $reference instanceof ArrayList ) return 'ArrayList';
        if ( $reference instanceof ReadableStream ) return 'ReadableStream';
        


        // Returning the value
        return null;
    }



    public function __construct (public readonly string $class, private array $app_errors = [])
    {
        if ( !class_exists( $class ) )
        {// (Class not found)
            // Throwing the exception
            throw new \Exception( "Class '{$this->class}' does not exist" );
        }

        

        // (Getting the value)
        $this->reflection = new \ReflectionClass( $this->class );
        


        // (Analyzing class methods)
        $this->analyze_methods();
    }



    private function analyze_methods () : void
    {
        foreach ( $this->reflection->getMethods( \ReflectionMethod::IS_PUBLIC ) as $method )
        {// Processing each entry
            if ( $method->isConstructor() || $method->getDeclaringClass()->getName() !== $this->class ) continue;



            // (Analyzing the method)
            $method_data = $this->analyze_method( $method );

            if ( $method_data )
            {// Value found
                // (Appending the value)
                $this->methods[] = $method_data;



                // (Getting the value)
                $this->data[ $method->getName() ] = $method_data;
            }
        }
    }

    private function analyze_method (\ReflectionMethod $method) : array|null
    {
        // (Getting the value)
        $method_name = $method->getName();



        // Returning the value
        return
        [
            'name'   => $method_name,
            'input'  => $this->extract_method_input( $method ),
            'output' => $this->extract_method_output( $method ),
            'errors' => $this->extract_errors( $this->class, $method_name )
        ]
        ;
    }

    private function extract_method_input (\ReflectionMethod $method): array|null
    {
        // (Getting the value)
        $attributes = $method->getAttributes( Input::class );

        if ( empty( $attributes ) ) return null;



        // (Getting the value)
        $input = $attributes[0]->getArguments()[0] ?? null;

        if ( $input )
        {// Value found
            // Returning the value
            return $this->parse_io( $input );
        }



        // Returning the value
        return null;
    }

    private function extract_method_output (\ReflectionMethod $method): array|null
    {
        // (Getting the value)
        $attributes = $method->getAttributes( Output::class );
        
        if ( empty( $attributes ) ) return null;
        


        // (Getting the value)
        $output = $attributes[0]->getArguments()[0] ?? null;
        
        if ( $output )
        {// Value found
            // Returning the value
            return $this->parse_io( $output );
        }



        // Returning the value
        return null;
    }

    private function extract_errors (string $class, string $method) : array
    {
        // (Setting the value)
        $errors = [];

        foreach ( ( new CodeAnalyzer( $class, $method ) )->exclude( [ 'Solenoid\MySQL\Connection::*' ] )->find( 'error' ) as $result )
        {// Processing each entry
            // (Getting the value)
            $code = $result->args[0]->value->value;

            if ( !isset( $this->app_errors[ $code ] ) || !$this->app_errors[ $code ]['exposed'] ) continue;



            // (Getting the value)
            $errors[ $code ] = $this->app_errors[ $code ];



            // (Removing the element)
            unset( $errors[ $code ]['exposed'] );



            // (Getting the values)
            $errors[ $code ]['code']       = (int) $errors[ $code ]['code'];
            $errors[ $code ]['http_code']  = (int) $errors[ $code ]['http_code'];
            $errors[ $code ]['notifiable'] = $errors[ $code ]['notifiable'] === '1';
        }



        // Returning the value
        return $errors;
    }

    private function parse_io (mixed $reference, ?string $name = null): array
    {
        // (Getting the value)
        $reflection = is_object( $reference ) ? new \ReflectionObject( $reference ) : new \ReflectionClass( $reference );



        // (Getting the value)
        $class = $reflection->getName();
        


        // (Getting the value)
        $data =
        [
            'name'       => $name,
            'class'      => $class,
            'short_name' => $reflection->getShortName(),
            'type'       => self::get_type( $reference ),
            'properties' => []
        ]
        ;



        switch ( self::get_type( $reference ) )
        {
            case 'Value':
                foreach ( $reflection->getProperties( \ReflectionProperty::IS_PUBLIC ) as $property )
                {// Processing each entry
                    // (Appending the value)
                    $data['properties'][] =
                    [
                        'scope'  => ( $property->getDeclaringClass()->getName() === $class ) ? 'SPECIFIC' : 'BASE',
                        'name'   => $property->getName(),
                        'value'  => $property->getValue( $reference ),
                        'origin' => $property->getDeclaringClass()->getName()
                    ]
                    ;
                }
                break;

            case 'DTO':
                // (Getting the value)
                $constructor = $reflection->getConstructor();
                
                if ( $constructor )
                {// Value found
                    foreach ( $constructor->getParameters() as $parameter )
                    {// Processing each entry
                        // (Getting the values)
                        $prop_name = $parameter->getName();
                        $prop_type = $parameter->getType();
                        $type_name = $prop_type instanceof \ReflectionNamedType ? $prop_type->getName() : 'mixed';



                        // (Getting the values)
                        $value_attr = $parameter->getAttributes( Value::class, \ReflectionAttribute::IS_INSTANCEOF )[0] ?? null;
                        $list_attr  = $parameter->getAttributes( ArrayList::class)[0] ?? null;

                        if ( $value_attr )
                        {// (Value is a Value)
                            // (Appending the value)
                            $data['properties'][] = array_merge( $this->parse_io( $value_attr->newInstance(), $prop_name ), [ 'scope' => 'SPECIFIC' ] );
                        }
                        else
                        if ( $list_attr )
                        {// (Value is an ArrayList)
                            // (Getting the values)
                            $list_instance   = $list_attr->newInstance();
                            $list_reflection = new \ReflectionObject( $list_instance );
                            $ref_prop        = $list_reflection->getProperty( 'reference' );
                            $inner_ref       = $ref_prop->getValue( $list_instance );



                            // (Appending the value)
                            $data['properties'][] =
                            [
                                'scope'      => 'SPECIFIC',
                                'name'       => $prop_name,
                                'type'       => 'ArrayList',
                                'template'   => $this->parse_io( $inner_ref ),
                                'inner_type' => self::get_type( $inner_ref )
                            ]
                            ;
                        }
                        else
                        if ( class_exists( $type_name ) && is_subclass_of( $type_name, DTO::class ) )
                        {// (Value is a DTO)
                            // (Appending the value)
                            $data['properties'][] = array_merge( $this->parse_io( $type_name, $prop_name ), [ 'scope' => 'SPECIFIC' ] );
                        }
                        else
                        {// Match failed
                            // (Appending the value)
                            $data['properties'][] =
                            [
                                'scope' => 'SPECIFIC',
                                'name'  => $prop_name,
                                'type'  => $type_name,
                                'value' => $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null
                            ]
                            ;
                        }
                    }
                }
                break;

            case 'ArrayList':
                // (Getting the value
                $property  = $reflection->getProperty( 'reference' );
                $reference = $property->getValue( $reference );
                
                $data['template']   = $this->parse_io( $reference );
                $data['inner_type'] = self::get_type( $reference );
            break;

            case 'ReadableStream':
                // (Setting the value)
                $data['type'] = 'ReadableStream';
            break;
        }



        // (Sorting the array)
        usort
        (
            $data['properties'],
            function ($a, $b)
            {
                if ( $a['scope'] === $b['scope'] ) return 0;



                // Returning the value
                return ( $a['scope'] < $b['scope'] ) ? -1 : 1;
            }
            )
        ;



        // Returning the value
        return $data;
    }



    public function get_methods () : array
    {
        // Returning the value
        return $this->methods;
    }

    public function get_method (string $name) : array|null
    {
        // Returning the value
        return $this->data[ $name ] ?? null;
    }



    public function get_method_input (string $method_name) : array|null
    {
        // (Getting the value)
        $method = $this->get_method( $method_name );



        // Returning the value
        return $method ? $method['input'] : null;
    }

    public function get_method_output (string $method_name) : array|null
    {
        // (Getting the value)
        $method = $this->get_method( $method_name );



        // Returning the value
        return $method ? $method['output'] : null;
    }

    public function get_method_errors (string $method_name) : array
    {
        // (Getting the value)
        $method = $this->get_method( $method_name );



        // Returning the value
        return $method ? $method['errors'] : [];
    }



    public function has_method (string $name) : bool
    {
        // Returning the value
        return isset( $this->data[ $name ] );
    }



    public function to_array () : array
    {
        // Returning the value
        return
        [
            'class'      => $this->class,
            'short_name' => $this->reflection->getShortName(),
            'methods'    => $this->methods
        ]
        ;
    }
}



?>
