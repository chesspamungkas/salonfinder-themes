<?php
namespace DV\core;

class Constants {
    
    public static function Define($key, $value) {
        if(!defined($key)) {
            return define($key, $value);
        }
        return false;
    }

    public static function Set($key, $value) {
        return self::Define($key, $value);
    }

    public static function Get($key, $default='') {
        if(defined($key)) {
            return constant($key);
        }
        return $default;
    }

    public static function getViewBaseDir() {
        $baseDir = dirname(__FILE__).'/../views/';
        return $baseDir;
    }

}