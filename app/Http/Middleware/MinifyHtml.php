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

        // No minificar en entorno local para facilitar depuración y no alterar líneas de código en consola
        if (app()->isLocal() && ! $request->boolean('force_minify')) {
            return $response;
        }

        // Solo minificar respuestas HTML válidas
        if ($response instanceof \Illuminate\Http\Response && str_contains($response->headers->get('Content-Type') ?? '', 'text/html')) {
            $html = $response->getContent();

            $placeholders = [];

            // Extraer y preservar bloques sensibles a saltos de línea (scripts, estilos, pre, textarea)
            $html = preg_replace_callback('/<(script|style|pre|textarea)\b[^>]*>.*?<\/\1>/is', function ($matches) use (&$placeholders) {
                $id = '<!--_PRESERVE_BLOCK_'.count($placeholders).'_-->';
                $placeholders[$id] = $matches[0];

                return $id;
            }, $html);

            // Extraer y preservar el arte ASCII para no dañar sus espacios
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

            // Restaurar bloques preservados
            if (! empty($placeholders)) {
                $html = str_replace(array_keys($placeholders), array_values($placeholders), $html);
            }

            $response->setContent($html);
        }

        return $response;
    }
}
