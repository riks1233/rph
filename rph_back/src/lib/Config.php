<?php

class Config
{
    protected static $_config = [];

    public static function update($config)
    {
        static::$_config = array_merge(static::$_config, $config);
    }

    public static function get($key, $default = null)
    {
        if (array_key_exists($key, static::$_config)) {
            return static::$_config[$key];
        }

        if ($default !== null) {
            return $default;
        }

        exit("[$key] not found in Config");

        return null;
    }
}

?>
