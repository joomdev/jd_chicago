<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2Settings
{

    private static $data;

    public static function init() {

        $config = array(
            'jquery'            => 1,
            'gsap'              => 1,
            'async'             => 0,
            'combine-js'        => 0,
            'minify-js'         => 0,
            'protocol-relative' => 1
        );

        if (!defined('NEXTEND_INSTALL')) {
            foreach (N2StorageSectionAdmin::getAll('system', 'global') AS $data) {
                $config[$data['referencekey']] = $data['value'];
            }
        }

        self::$data = new N2Data();
        self::$data->loadArray($config);
    }

    public static function get($key) {
        return self::$data->get($key);
    }

    public static function getAll() {
        return self::$data->toArray();
    }

    public static function set($key, $value) {
        self::$data->set($key, $value);
        N2StorageSectionAdmin::set('system', 'global', $key, $value, 1, 1);
    }

    public static function setAll($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (self::$data->get($key, null) !== null) {
                    self::set($key, $value);
                }
            }
            return true;
        }
        return false;
    }

}

N2Settings::init();