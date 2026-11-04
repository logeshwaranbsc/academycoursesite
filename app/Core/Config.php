<?php

namespace App\Core;

class Config
{
    private static array $configs = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $file = array_shift($segments);

        if (!isset(self::$configs[$file])) {
            $path = dirname(__DIR__, 2) . "/config/{$file}.php";
            if (file_exists($path)) {
                self::$configs[$file] = require $path;
            } else {
                self::$configs[$file] = [];
            }
        }

        $value = self::$configs[$file];
        foreach ($segments as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }

        return $value;
    }
}
