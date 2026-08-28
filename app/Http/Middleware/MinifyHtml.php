<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MinifyHtml
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Solo minificar respuestas HTML válidas
        if ($response instanceof \Illuminate\Http\Response && str_contains($response->headers->get('Content-Type') ?? '', 'text/html')) {
            $html = $response->getContent();

            // Simple y efectiva compresión HTML
            $replace = [
                '<!--(.*?)-->' => '', // Eliminar comentarios
                "/<\?php/" => '<?php ',
                "/\n([\S])/" => ' $1',
                "/\r/" => '',
                "/\n/" => '',
                "/\t/" => ' ',
                "/ +/" => ' ',
            ];

            $html = preg_replace(array_keys($replace), array_values($replace), $html);
            $response->setContent($html);
        }

        return $response;
    }
}
