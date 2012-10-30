<?php

namespace C2is\Lib;

class String
{
    static public function camelize($string)
    {
        $string = preg_replace("/([_-\s]?([a-z0-9]+))/e",
            "ucwords('\\2')",
            $string
        );

        return strtoupper($string[0]) . substr($string, 1);
    }

    public static function underscore($string)
    {
        $string = preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/')
            , '\\1_\\2',
            strtr($string, '_', '.')
        );

        return strtolower($string);
    }
}
