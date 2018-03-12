<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2RequestStorage
{

    public static $REQUEST, $COOKIE, $POST, $GET;

    public static function init() {
        self::$GET     = self::stripslashesRecursive($_GET);
        self::$POST    = self::stripslashesRecursive($_POST);
        self::$COOKIE  = self::stripslashesRecursive($_COOKIE);
        self::$REQUEST = self::stripslashesRecursive($_REQUEST);
    }

    public static function stripslashesRecursive($array) {
        foreach ($array as $key => $value) {
            $array[$key] = is_array($value) ? self::stripslashesRecursive($value) : stripslashes($value);
        }
        return $array;
    }
}

class N2Request
{

    public static $storage, $_requestUri;

    public static function init() {
        self::$storage = N2RequestStorage::$REQUEST;
    }

    /**
     * @param $var
     * @param $val
     */
    static function set($var, $val) {
        self::$storage[$var] = $val;
    }

    /**
     * @param      $var
     * @param null $default
     *
     * @return null
     */
    static function getVar($var, $default = null) {
        $val = isset(self::$storage[$var]) ? self::$storage[$var] : $default;
        return $val;
    }

    /**
     * @param     $var
     * @param int $default
     *
     * @return int
     */
    static function getInt($var, $default = 0) {
        $val = isset(self::$storage[$var]) ? self::$storage[$var] : $default;
        return intval($val);
    }

    /**
     * @param        $var
     * @param string $default
     *
     * @return mixed
     */
    static function getCmd($var, $default = '') {
        $val = isset(self::$storage[$var]) ? self::$storage[$var] : $default;
        return preg_replace("/[^\w_]/", "", $val);
    }

    /**
     * @return bool
     */
    public static function getIsAjaxRequest() {

        if (isset(self::$storage["nextendajax"]) || isset(self::$storage["najax"])) {
            return true;
        }

        return false;
    }

    /**
     * @param array|string $url
     * @param integer      $statusCode
     * @param bool         $terminate
     */
    public static function redirect($url, $statusCode = 302, $terminate = true) {

        header('Location: ' . $url, true, $statusCode);
        if ($terminate) {
            n2_exit(true);
        }
    }

    public static function getUrlReferrer() {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    }

    /**
     * @return mixed|string
     * @throws Exception
     */
    public static function getRequestUri() {
        if (self::$_requestUri === null) {
            if (isset($_SERVER['HTTP_X_REWRITE_URL'])) // IIS
                self::$_requestUri = $_SERVER['HTTP_X_REWRITE_URL']; elseif (isset($_SERVER['REQUEST_URI'])) {
                self::$_requestUri = $_SERVER['REQUEST_URI'];
                if (!empty($_SERVER['HTTP_HOST'])) {
                    if (strpos(self::$_requestUri, $_SERVER['HTTP_HOST']) !== false) self::$_requestUri = preg_replace('/^\w+:\/\/[^\/]+/', '', self::$_requestUri);
                } else
                    self::$_requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', self::$_requestUri);
            } elseif (isset($_SERVER['ORIG_PATH_INFO'])) // IIS 5.0 CGI
            {
                self::$_requestUri = $_SERVER['ORIG_PATH_INFO'];
                if (!empty($_SERVER['QUERY_STRING'])) self::$_requestUri .= '?' . $_SERVER['QUERY_STRING'];
            } else
                throw new Exception(__CLASS__ . ' is unable to determine the request URI.');
        }

        return self::$_requestUri;
    }

}

class N2Get
{

    public static $storage;

    public static function init() {
        self::$storage = N2RequestStorage::$GET;
    }

    static function set($var, $val) {
        self::$storage[$var] = $val;
    }

    static function getVar($var, $default = null) {
        $val = isset(self::$storage[$var]) ? self::$storage[$var] : $default;
        return $val;
    }

    static function getInt($var, $default = 0) {
        $val = isset(self::$storage[$var]) ? self::$storage[$var] : $default;
        return intval($val);
    }

    static function getCmd($var, $default = '') {
        $val = isset(self::$storage[$var]) ? self::$storage[$var] : $default;
        return preg_replace("/[^\w_]/", "", $val);
    }
}

class N2Post
{

    public static $storage;

    public static function init() {
        self::$storage = N2RequestStorage::$POST;
    }

    static function set($var, $val) {
        self::$storage[$var] = $val;
    }

    static function getVar($var, $default = null) {
        $val = isset(self::$storage[$var]) ? self::$storage[$var] : $default;
        return $val;
    }

    static function getInt($var, $default = 0) {
        $val = isset(self::$storage[$var]) ? self::$storage[$var] : $default;
        return intval($val);
    }

    static function getCmd($var, $default = '') {
        $val = isset(self::$storage[$var]) ? self::$storage[$var] : $default;
        return preg_replace("/[^\w_]/", "", $val);
    }
}

class N2Cookie
{

    public static $storage;

    public static function init() {
        self::$storage = N2RequestStorage::$COOKIE;
    }

    static function set($var, $val) {
        self::$storage[$var] = $val;
    }

    static function getVar($var, $default = null) {
        $val = isset(self::$storage[$var]) ? self::$storage[$var] : $default;
        return $val;
    }

    static function getInt($var, $default = 0) {
        $val = isset(self::$storage[$var]) ? self::$storage[$var] : $default;
        return intval($val);
    }

    static function getCmd($var, $default = '') {
        $val = isset(self::$storage[$var]) ? self::$storage[$var] : $default;
        return preg_replace("/[^\w_]/", "", $val);
    }
}

N2RequestStorage::init();
N2Request::init();
N2Get::init();
N2Post::init();
N2Cookie::init();