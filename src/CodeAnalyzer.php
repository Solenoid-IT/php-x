<?php



namespace Solenoid\X;



use \PhpParser\Node;
use \PhpParser\NodeFinder;
use \PhpParser\ParserFactory;
use \PhpParser\Parser;
use \PhpParser\Node\Stmt\ClassMethod;



class CodeAnalyzer
{
    private Parser     $parser;
    private NodeFinder $nodeFinder;



    public function __construct (public string $class, public string $method)
    {
        // (Getting the values)
        $this->parser     = ( new ParserFactory() )->createForNewestSupportedVersion();
        $this->nodeFinder = new NodeFinder();
    }



    public function find (string $fn, ?string $class = null) : array
    {
        // (Getting the value)
        $reflection = new \ReflectionMethod( $this->class, $this->method );



        // (Getting the values)
        $file_path = $reflection->getFileName();



        // (Getting the value)
        $code = file_get_contents( $file_path );



        // (Getting the value)
        $ast = $this->parser->parse( $code );



        // (Getting the value)
        $method_node = $this->nodeFinder->findFirst
        (
            $ast,
            function (Node $node)
            {
                // Returning the value
                return $node instanceof ClassMethod && $node->name->toString() === $this->method;
            }
        )
        ;

        if ( !$method_node ) return [];



        // (Getting the value)
        $results = $this->nodeFinder->find
        (
            $method_node->stmts,
            function (Node $node) use ($fn, $class)
            {
                if ( $class === null )
                {// (Function is global)
                    // Returning the value
                    return $node instanceof Node\Expr\FuncCall && $node->name instanceof Node\Name && $node->name->toString() === $fn;
                }
                else
                {// (Function is a method)
                    if ( $node instanceof Node\Expr\StaticCall )
                    {// (Method is static)
                        // Returning the value
                        return $node->class instanceof Node\Name && $node->class->toString() === $class && $node->name->toString() === $fn;
                    }

                    if ( $node instanceof Node\Expr\MethodCall )
                    {// (Method is not static)
                        // Returning the value
                        return $node->name->toString() === $fn;
                    }
                }



                // Returning the value
                return false;
            }
        )
        ;



        // Returning the value
        return $results;
    }
}