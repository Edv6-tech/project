<?php

class EnvLoader {
    private static $loaded = false;

    public static function load($path = null) {
        if (self::$loaded) {
            return;
        }

        $path = $path ?: __DIR__ . '/../.env';
        
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments and empty lines
            if (strpos(trim($line), '#') === 0 || trim($line) === '') {
                continue;
            }

            // Parse key=value pairs
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                $value = trim($value, '"\'');
                
                // Set environment variable
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }

        self::$loaded = true;
    }

    public static function get($key, $default = null) {
        self::load();
        return $_ENV[$key] ?? $default;
    }

    public static function require($key) {
        $value = self::get($key);
        if ($value === null) {
            throw new Exception("Required environment variable '$key' is not set");
        }
        return $value;
    }
}

// Auto-load environment variables
EnvLoader::load();
