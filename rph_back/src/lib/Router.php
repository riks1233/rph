<?php

/**
 * Router's `$_base_route` contains everything that goes after hostname in URL.
 * `$_remaining_subroutes` is an array, elements of which are strings from base route, delimited by '/'.
 *
 * The idea of Router usage is to pop leftmost subroute from `$_remaining_subroutes` and route based on that.
 *
 * Note: Router will not receive real file paths under `public/` directory - `public/.htaccess` restriction.
 */
class Router
{
    protected static $_base_route;
    protected static $_remaining_subroutes;

    public static function init()
    {
        $url = request_param('url');
        static::$_base_route = htmlspecialchars($url);
        static::$_remaining_subroutes = explode('/', static::$_base_route);
    }

    public static function pop_next_subroute() {
        $next_param = "";
        while (count(static::$_remaining_subroutes) > 0 && ($next_param = trim(array_shift(static::$_remaining_subroutes))) == "") {
            // Skip empty params.
        }

        return $next_param;
    }

    public static function peek_next_subroute() {
        $next_param = "";
        if (count(static::$_remaining_subroutes) > 0) {
            $next_param = trim(static::$_remaining_subroutes[0]);
        }

        return $next_param;
    }
}

?>