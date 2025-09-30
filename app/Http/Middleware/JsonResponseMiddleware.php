<?php
// app/Http/Middleware/JsonResponseMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class JsonResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Si la respuesta es JSON, agregar opciones de encoding
        if ($response->headers->get('Content-Type') === 'application/json') {
            $response->setEncodingOptions(
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
            );
        }
        
        return $response;
    }
}