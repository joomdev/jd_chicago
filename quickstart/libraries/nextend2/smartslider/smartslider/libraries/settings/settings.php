<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartSliderSettings
{

    static $settings = null;

    private static $_type = "settings";

    static function getAll() {
        if (self::$settings === null) {
            self::$settings = json_decode(N2Base::getApplication('smartslider')->storage->get(self::$_type), true);
            if (self::$settings === null) self::$settings = array();
        }
        return self::$settings;
    }

    static function get($key, $default = null) {
        if (self::$settings === null) self::getAll();
        if (!array_key_exists($key, self::$settings)) return $default;
        return self::$settings[$key];
    }

    static function set($key, $value) {
        self::getAll();
        self::$settings[$key] = $value;
        N2SmartSliderSettings::store(self::$_type, json_encode(self::$settings));
    }

    static function store($key, $value) {
        N2Base::getApplication('smartslider')->storage->set($key, '', $value);
    }

}