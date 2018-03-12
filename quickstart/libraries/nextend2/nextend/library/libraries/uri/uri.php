<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2UriAbstract
{

    var $_baseuri;

    var $_currentbase = '';

    static function getInstance() {

        static $instance;
        if (!is_object($instance)) {
            $instance = new N2Uri();
        } // if

        return $instance;
    }

    static function setBaseUri($uri) {
        $i           = N2Uri::getInstance();
        $i->_baseuri = $uri;
    }

    static function getBaseUri() {
        $i = N2Uri::getInstance();
        return $i->_baseuri;
    }

    static function pathToUri($path) {
        $i = N2Uri::getInstance();
        return $i->_baseuri . str_replace(array(
            N2Filesystem::getBasePath(),
            DIRECTORY_SEPARATOR
        ), array(
            '',
            '/'
        ), str_replace('/', DIRECTORY_SEPARATOR, $path));
    }

    static function ajaxUri($query = '', $magento = 'nextendlibrary') {
        $i = N2Uri::getInstance();
        return $i->_baseuri;
    }

    static function fixrelative($uri) {
        if (substr($uri, 0, 1) == '/' || strpos($uri, '://') !== false) return $uri;
        return self::getInstance()->_baseuri . $uri;
    }

    static function relativetoabsolute($uri) {
        if (substr($uri, 0, 1) == '/' || strpos($uri, '://') !== false) return $uri;
        return self::getInstance()->_currentbase . $uri;
    }
}

N2Loader::import("libraries.uri.uri", "platform");