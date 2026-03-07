<?php



namespace Solenoid\X\Testing;



use \Solenoid\X\Data\ClassScanner;



class FluidTester
{
    private array $endpoints;
    private array $results      = [];
    private int   $total_tests  = 0;
    private int   $passed_tests = 0;
    private int   $failed_tests = 0;




    private function test_endpoint (array $endpoint) : void
    {
        // (Getting the value)
        $class = $endpoint['name'] ?? 'Unknown';



        // Printing the value
        echo "📁 Testing class {$class}\n";
        echo str_repeat( '-', 50 ) . "\n";
        
        if ( isset( $endpoint['methods'] ) && is_array( $endpoint['methods'] ) )
        {// Value found
            foreach ( $endpoint['methods'] as $method )
            {// Processing each entry
                // (Testing the method)
                $this->test_endpoint_method( $class, $method );
            }
        }



        // Printing the value
        echo "\n";
    }

    private function test_endpoint_method (string $class, array $method) : void
    {
        // (Incrementing the value)
        $this->total_tests += 1;



        // (Getting the value)
        $method_name = $method['name'] ?? 'unknown';
        


        // (Getting the value)
        $url = "/api/token?m=$class.$method_name";
        


        // Printing the value
        echo "    🔧 {$method_name}: ";
        


        try
        {
            // (Preparing test data)
            $test_data = $this->prepare_test_data( $method['input'] ?? null );
            


            // (Making the request)
            $response = $this->make_http_request( 'RUN', $url, $test_data );
            


            // (Validating response)
            $result = $this->validate_response( $response, $method );
            
            if ( $result['success'] )
            {// (Request OK)
                // Printing the value
                echo "✅ PASS";



                // (Appending the value)
                $this->passed_tests++;
            }
            else
            {// (Request failed)
                // Printing the value
                echo "❌ FAIL - " . $result['message'];



                // (Appending the value)
                $this->failed_tests++;
            }
            


            if ( $result['details'] )
            {// Value found
                // Printing the value
                echo " (" . $result['details'] . ")";
            }
        }
        catch (\Exception $e)
        {
            // Printing the value
            echo "❌ ERROR - " . $e->getMessage();



            // (Incrementing the value)
            $this->failed_tests += 1;
        }



        // Printing the value
        echo "\n";



        // (Appending the value)
        $this->results[] =
        [
            'endpoint'  => $url,
            'method'    => $method_name,
            'class'     => $class,
            'status'    => $result['success'] ?? false,
            'message'   => $result['message'] ?? $e->getMessage() ?? '',
            'timestamp' => date('Y-m-d H:i:s')
        ]
        ;
    }

    private function prepare_test_data (?array $input) : array
    {
        if ( !$input ) return [];



        // (Setting the value)
        $data = [];
        
        switch ( $input['type'] ?? '' )
        {
            case 'Value':
                // (Getting the values)
                $field_name          = $this->get_field_name_from_spec( $input );
                $data[ $field_name ] = $this->generate_value_from_spec( $input );
            break;
                
            case 'DTO':
                if ( isset( $input['properties'] ) )
                {// Value found
                    foreach ( $input['properties'] as $property )
                    {// Processing each entry
                        // (Getting the value)
                        $data[ $property['name'] ?? 'unknown'] = $this->generate_value_from_property( $property );
                    }
                }
            break;
                
            case 'ArrayList':
                // (Setting the value)
                $data = [1];
            break;
        }



        // Returning the value
        return $data;
    }

    private function generate_value_from_spec (array $spec) : mixed
    {
        // (Getting the value)
        $class = $spec['short_name'] ?? '';
        
        switch ( $class )
        {
            case 'IntValue':
                // Returning the value
                return 1;
            break;

            case 'StringValue':
                // Returning the value
                return 'test';
            break;

            case 'BoolValue':
                // Returning the value
                return true;
            break;

            case 'FloatValue':
                // Returning the value
                return 1.5;
            break;

            default:
                // Returning the value
                return null;
        }
    }

    private function get_field_name_from_spec (array $spec) : string
    {
        if ( isset( $spec['properties'] ) )
        {// Value found
            foreach ( $spec['properties'] as $property )
            {// Processing each entry
                if ( $property['name'] === 'name' && isset( $property['value'] ) )
                {// Match OK
                    // Returning the value
                    return $property['value'];
                }
            }
        }
        


        // // (Getting the value)
        $class = $spec['short_name'] ?? '';

        switch ( $class )
        {
            case 'IntValue':
                // Returning the value
                return 'id';
            break;

            case 'StringValue':
                // Returning the value
                return 'value';
            break;

            case 'BoolValue':
                // Returning the value
                return 'flag';
            break;

            case 'FloatValue':
                // Returning the value
                return 'amount';
            break;

            default:
                // Returning the value
                return 'data';
        }
    }

    private function generate_value_from_property (array $property) : mixed
    {
        $type = $property['class'] ?? '';
        $name = $property['name'] ?? '';
        
        if ( strpos( $type, 'IntValue' ) !== false )
        {// Match OK
            // Returning the value
            return ( $name === 'id' ) ? 1 : rand( 1, 100 );
        }
        else
        if ( strpos( $type, 'StringValue' ) !== false )
        {// Match OK
            if ( $name === 'email' ) return 'test@example.com';
            if ( $name === 'name' ) return 'Test Name';



            // Returning the value
            return 'test_' . $name;
        }
        else
        if ( strpos( $type, 'BoolValue' ) !== false )
        {// Match OK
            // Returning the value
            return true;
        }
        else
        if ( strpos( $type, 'FloatValue' ) !== false )
        {// Match OK
            // Returning the value
            return ( $name === 'price' ) ? 19.99 : rand( 1, 100 ) / 10;
        }



        // Returning the value
        return 'test_value';
    }

    private function make_http_request (string $method, string $path, array $data) : array
    {
        // (Getting the value)
        $ch = curl_init();
        


        // (Getting the value)
        $full_url = "http://localhost/$path";



        // (Setting the options)
        curl_setopt_array
        (
            $ch,
            [
                CURLOPT_URL            => $full_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_CUSTOMREQUEST  => $method
            ]
        )
        ;



        // (Setting the options)
        curl_setopt( $ch, CURLOPT_POSTFIELDS, empty( $data ) ? '' : json_encode( $data ) );
        curl_setopt
        (
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Content-Type: application/json',
                'Accept: application/json',
                "Personal-Token: {$this->token}"
            ]
        )
        ;



        // (Getting the values)
        $response  = curl_exec( $ch );
        $http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );



        // (Getting the value)
        $error = curl_error( $ch );

        if ( $error )
        {// Value found
            // Throwing the exception
            throw new \Exception( "cURL Error :: $error" );
        }
        


        // Returning the value
        return
        [
            'http_code' => $http_code,
            'body'      => $response,
            'data'      => json_decode( $response, true )
        ]
        ;
    }

    private function validate_response (array $response, array $method_spec) : array
    {
        $http_code = $response['http_code'];
        $body      = $response['body'];
        $data      = $response['data'];

        if ( $http_code >= 200 && $http_code < 300 )
        {// Match OK
            // Returning the value
            return
            [
                'success' => true,
                'message' => "HTTP $http_code",
                'details' => 'Response OK'
            ]
            ;
        }

        if ( isset( $method_spec['errors'] ) && !empty( $method_spec['errors'] ) )
        {// Value found
            foreach ( $method_spec['errors'] as $error_code => $error_spec )
            {// Processing each entry
                if ( $http_code == $error_spec['http_code'] )
                {// Match OK
                    // Returning the value
                    return
                    [
                        'success' => true,
                        'message' => "Expected error $error_code",
                        'details' => "HTTP $http_code"
                    ]
                    ;
                }
            }
        }



        // Returning the value
        return
        [
            'success' => false,
            'message' => "Unexpected HTTP $http_code",
            'details' => substr( $body, 0, 100 )
        ]
        ;
    }

    private function print_summary () : void
    {
        // Printing the value
        echo "=== SUMMARY ===\n";
        echo "Total tests: {$this->total_tests}\n";
        echo "✅ Passed: {$this->passed_tests}\n";
        echo "❌ Failed: {$this->failed_tests}\n";



        // (Getting the value)
        $success_rate = $this->total_tests > 0 ? round( ( $this->passed_tests / $this->total_tests ) * 100, 2 ) : 0;



        // Printing the value
        echo "📊 Success rate: {$success_rate}%\n\n";
        


        // (Saving result to file)
        $this->save_result_to_file();
    }

    private function save_result_to_file () : void
    {
        // (Getting the value)
        $log_folder_path = dirname( __DIR__, 2 ) . '/log';

        if ( !is_dir( $log_folder_path ))
        {// Value not found
            if ( !mkdir( $log_folder_path, 0755, true ) )
            {// Failed to create directory
                // Printing the value
                echo "⚠️  Warning: Failed to create log directory at $log_folder_path\n";



                // Ruetning the value
                return;
            }
        }



        // (Getting the value)
        $result_file = $log_folder_path . '/fluid_test_results_' . date('Y-m-d_H-i-s') . '.json';
        
        $summary =
        [
            'timestamp'    => date( 'Y-m-d H:i:s' ),
            'total_tests'  => $this->total_tests,
            'passed_tests' => $this->passed_tests,
            'failed_tests' => $this->failed_tests,
            'success_rate' => $this->total_tests > 0 ? round( ( $this->passed_tests / $this->total_tests ) * 100, 2 ) : 0,
            'details'      => $this->results
        ]
        ;



        // (Writing to the file)
        file_put_contents( $result_file, json_encode( $summary, JSON_PRETTY_PRINT ) );



        // Printing the value
        echo "📄 Detailed results saved to: " . basename( $result_file ) . "\n";
    }



    public function __construct (public readonly string $path, private string $token, array $app_errors = [], string $root_namespace = 'App\\Endpoints')
    {
        // (Getting the value)
        $this->endpoints = ( new ClassScanner( $path, $app_errors, $root_namespace ) )->scan();
    }



    public function run () : void
    {
        // Printing the value
        echo "=== FLUID API TESTING ===\n\n";



        foreach ( $this->endpoints as $endpoint )
        {// Processing each entry
            // (Testing the endpoint)
            $this->test_endpoint( $endpoint );
        }
        


        // (Printing the summary)
        $this->print_summary();
    }
}



?>