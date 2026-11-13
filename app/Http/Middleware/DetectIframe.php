<?php

namespace App\Http\Middleware;

use Closure;
use App\Support\IframeDetector;

class DetectIframe
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next){
        $isIframe = false;
        $source = null;

        // detect query -> working
        // if ($request->query('iframe') == 1){
        //     $isIframe = true;
        //     $source = $request->query('source');
        // }

        // detect ajax header
        if ($request->header('X-Iframe-Mode') == 1){
            $isIframe = true;
            $source = $request->header('X-Iframe-Source');
        }
        // dd($isIframe1, $source1, $isIframe2, $source2);
            // dd($request->headers->all());

        IframeDetector::set($isIframe, $source);

        return $next($request);
    }
}
