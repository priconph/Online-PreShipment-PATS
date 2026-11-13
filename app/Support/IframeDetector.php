<?php

namespace App\Support;

// class IframeDetector
// {
    // public static function isIframe(){

    //     if (!app()->runningInConsole() && request()) {
    //         return request()->query('iframe') == 1;
    //     }

    //     return false;
    // }
// }

class IframeDetector
{
    protected static $isIframe = false;
    protected static $source = null;

    // Set per request
    public static function set($isIframe, $source = null)
    {
        static::$isIframe = $isIframe;
        static::$source = $source;
    }

    // Check if iframe
    public static function isIframe()
    {
        return static::$isIframe;
    }

    // Get iframe source (systemA, systemB, etc.)
    public static function source()
    {
        return static::$source;
    }
}

    // public static function isIframe($request = null)
    // {
    //     $request = $request ?: app()->bound('request') ? request() : null;

    //     if (!$request) {
    //         return false;
    //     }

    //     return (
    //         $request->query('iframe') == 1 ||
    //         $request->headers->get('sec-fetch-dest') === 'iframe' ||
    //         $request->headers->get('x-requested-with') === 'iframe'
    //     );
    // }
