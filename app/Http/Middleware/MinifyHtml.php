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

            // Extraer y preservar el arte ASCII para no dañar sus espacios
            $placeholders = [];
            $html = preg_replace_callback('/<!--(.*?)-->/s', function ($matches) use (&$placeholders) {
                if (str_contains($matches[0], '██')) {
                    $id = '<!--_ASCII_ART_'.count($placeholders).'_-->';
                    $placeholders[$id] = "\n".$matches[0]."\n";

                    return $id;
                }

                return ''; // Eliminar otros comentarios
            }, $html);

            // Simple y efectiva compresión HTML
            $replace = [
                "/<\?php/" => '<?php ',
                "/\n([\S])/" => ' $1',
                "/\r/" => '',
                "/\n/" => '',
                "/\t/" => ' ',
                '/ +/' => ' ',
            ];

            $html = preg_replace(array_keys($replace), array_values($replace), $html);

            // Restaurar el arte ASCII intacto
            if (! empty($placeholders)) {
                $html = str_replace(array_keys($placeholders), array_values($placeholders), $html);
            }

            $response->setContent($html);
        }

        return $response;
    }
}
