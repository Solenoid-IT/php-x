<?php



namespace Solenoid\X;



interface MiddlewareInterface
{
    public function handle ($input, callable $next);
}



?>