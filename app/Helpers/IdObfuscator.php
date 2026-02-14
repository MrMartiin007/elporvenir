<?php

namespace App\Helpers;

class IdObfuscator
{
    // Alphabet for encoding (removed visually similar characters like I, l, 1, O, 0)
    private static $alphabet = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    private static $base = 56; // Length of alphabet

    public static function encode($id)
    {
        $id = (int) $id;
        if ($id === 0) {
            return static::$alphabet[0];
        }

        $result = '';
        while ($id > 0) {
            $remainder = $id % static::$base;
            $result = static::$alphabet[$remainder] . $result;
            $id = ($id - $remainder) / static::$base;
        }

        return $result;
    }

    public static function decode($hash)
    {
        $id = 0;
        $len = strlen($hash);
        for ($i = 0; $i < $len; $i++) {
            $char = $hash[$i];
            $pos = strpos(static::$alphabet, $char);
            if ($pos === false) {
                return null; // Invalid character
            }
            $id = $id * static::$base + $pos;
        }

        return $id;
    }
}
