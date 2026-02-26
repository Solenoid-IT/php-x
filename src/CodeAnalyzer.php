<?php



namespace Solenoid\X;



use \PhpParser\Node;
use \PhpParser\NodeFinder;
use \PhpParser\ParserFactory;
use \PhpParser\Parser;
use \PhpParser\Node\Stmt\ClassMethod;
use \PhpParser\Node\Stmt\Class_;
use \PhpParser\PrettyPrinter\Standard as StandardPrettyPrinter;
use \PhpParser\Node\Name;
use \PhpParser\Node\Identifier;
use \PhpParser\Node\Expr\Variable;
use \PhpParser\Node\Expr\FuncCall;
use \PhpParser\Node\Expr\StaticCall;
use \PhpParser\Node\Expr\MethodCall;
use \PhpParser\Node\Expr\New_;
use \PhpParser\Node\Expr\PropertyFetch;
use \PhpParser\NodeTraverser;
use \PhpParser\NodeVisitor\NameResolver;



class CodeAnalyzer
{
    private Parser     $parser;
    private NodeFinder $nodeFinder;

    private int        $loop_limit       = 5;
    private array      $excluded_methods = [];



    private function resolve_class (Node $node) : string|null
    {
        if ( $node instanceof StaticCall && $node->class instanceof Name )
        {// Match OK
            // Returning the value
            return $node->class->toString();
        }



        if ( $node instanceof MethodCall )
        {// Match OK
            if ( $node->var instanceof Variable && $node->var->name === 'this' ) return $this->class;

            if ( $node->var instanceof New_ && $node->var->class instanceof Name ) return $node->var->class->toString();

            if ( $node->var instanceof PropertyFetch && $node->var->var instanceof Variable && $node->var->var->name === 'this' )
            {// Match OK
                // (Getting the value)
                $prop_name = $node->var->name->toString();



                // (Getting the value)
                $ref = new \ReflectionClass( $this->class );

                if ( $ref->hasProperty( $prop_name ) )
                {// Match OK
                    // (Getting the value)
                    $prop = $ref->getProperty( $prop_name );

                    if ( $prop->hasType() ) return $prop->getType()->getName();
                }
            }

            if ( $node->var instanceof MethodCall )
            {// Match OK
                // (Getting the value)
                $parent_class = $this->resolve_class( $node->var );

                if ( $parent_class && class_exists( $parent_class ) )
                {// Match OK
                    // (Getting the value)
                    $method_name = $node->var->name->toString();



                    // (Getting the value)
                    $return_type = ( new \ReflectionMethod( $parent_class, $method_name ) )->getReturnType();

                    if ( $return_type instanceof \ReflectionNamedType )
                    {// Match OK
                        // (Getting the value)
                        $type_name = $return_type->getName();

                        if ( in_array( $type_name, [ 'static', 'self', 'this' ] ) )
                        {// Match OK
                            // (Getting the value)
                            $type_name = $parent_class;
                        }



                        // Returning the value
                        return $type_name;
                    }
                }
            }
        }



        // Returning the value
        return null;
    }



    public function __construct (public string $class, public string $method)
    {
        // (Getting the values)
        $this->parser     = ( new ParserFactory() )->createForNewestSupportedVersion();
        $this->nodeFinder = new NodeFinder();
    }



    public function set_loop_limit (int $limit) : self
    {
        // (Getting the value)
        $this->loop_limit = $limit;



        // Returning the value
        return $this;
    }

    public function set_excluded_methods (array $methods) : self
    {
        // (Getting the value)
        $this->excluded_methods = $methods;



        // Returning the value
        return $this;
    }



    public function find (string $fn, ?string $class = null, array &$visited = [], array $context = []) : array
    {
        // (Getting the value)
        $signature = $this->class . '::' . $this->method;

        foreach ( $this->excluded_methods as $pattern )
        {// Processing each entry
            if ( fnmatch( $pattern, $signature ) ) return [];
        }



        if ( !isset( $visited[ $signature ] ) ) $visited[ $signature ] = 0;



        // (Incrementing the value)
        $visited[ $signature ] += 1;

        if ( $visited[ $signature ] > $this->loop_limit ) return [];



        // (Getting the value)
        $reflection = new \ReflectionMethod( $this->class, $this->method );



        // (Getting the value)
        $declaring_class = $reflection->getDeclaringClass();

        if ( $declaring_class->isInternal() ) return [];



        // (Getting the values)
        $file_path = $reflection->getFileName();

        if ( !$file_path || !file_exists( $file_path ) ) return [];



        // (Getting the value)
        $code = file_get_contents( $file_path );



        // (Getting the value)
        $ast = $this->parser->parse( $code );



        // (Getting the value)
        $node_traverser = new NodeTraverser();

        // (Adding the visitor)
        $node_traverser->addVisitor( new NameResolver() );

        // (Traversing the AST)
        $ast = $node_traverser->traverse( $ast );



        // (Getting the value)
        $class_node = $this->nodeFinder->findFirst
        (
            $ast,
            function (Node $node) use ($declaring_class)
            {
                if ( !( $node instanceof Class_ ) ) return false;



                // (Getting the value)
                $class_path = isset( $node->namespacedName ) ? $node->namespacedName->toString() : $node->name->toString();



                // Returning the value
                #return $class_path === $this->class;
                return $class_path === $declaring_class->getName();
            }
        )
        ;

        if ( !$class_node ) return [];



        // (Getting the value)
        $method_node = $this->nodeFinder->findFirst
        (
            $class_node->stmts,
            function (Node $node)
            {
                // Returning the value
                return $node instanceof ClassMethod && $node->name->toString() === $this->method;
            }
        )
        ;

        if ( !$method_node ) return [];



        foreach ( $method_node->params as $i => $param )
        {// Processing each entry
            if ( isset( $context[$i] ) )
            {// Value found
                // (Getting the value)
                $context[ $param->var->name ] = $context[ $i ];
            }
        }



        // (Setting the value)
        $results = [];



        // (Getting the value)
        $nodes = $this->nodeFinder->find
        (
            $method_node->stmts,
            function (Node $node)
            {
                // Returning the value
                return $node instanceof FuncCall || $node instanceof StaticCall || $node instanceof MethodCall;
            }
        )
        ;

        foreach ( $nodes as $node )
        {// Processing each entry
            if ( !( $node->name instanceof Identifier || $node->name instanceof Name ) ) continue;



            foreach ( $node->args as $arg )
            {// Processing each entry
                if ( $arg->value instanceof Variable && isset( $context[ $arg->value->name ] ) )
                {// Match OK
                    // (Getting the value)
                    $arg->value = $context[ $arg->value->name ];
                }
            }



            // (Setting the value)
            $node_found = false;

            if ( $class === null )
            {// (Function is global)
                if ( $node instanceof FuncCall && $node->name instanceof Name && $node->name->toString() === $fn ) $node_found = true;
            }
            else
            {// (Function is a method)
                if ( $node instanceof StaticCall && $node->class instanceof Name && $node->class->toString() === $class && $node->name->toString() === $fn ) $node_found = true;
                if ( $node instanceof MethodCall && $node->name->toString() === $fn ) $node_found = true;
            }

            if ( $node_found ) 
            {// Value is true
                // (Appending the value)
                $results[] = $node;

                // Continuing the iteration
                continue;
            }



            if ( $node instanceof MethodCall || $node instanceof StaticCall )
            {// Match OK
                // (Getting the value)
                $target_class = $this->resolve_class( $node );



                // (Getting the value)
                $target_method = $node->name->toString();



                if ( $target_class && class_exists( $target_class ) && method_exists( $target_class, $target_method ) )
                {// Match OK
                    // (Setting the value)
                    $next_context = [];

                    foreach ( $node->args as $i => $arg )
                    {// Processing each entry
                        // (Getting the value)
                        $next_context[ $i ] = $arg->value;
                    }



                    // (Getting the value)
                    $results = array_merge( $results, ( new self( $target_class, $target_method ) )->find( $fn, $class, $visited, $next_context ) );
                }
            }
        }



        // Returning the value
        return $results;
    }



    public static function get_value (Node $node) : string
    {
        // Returning the value
        return ( new StandardPrettyPrinter() )->prettyPrintExpr( $node );
    }
}