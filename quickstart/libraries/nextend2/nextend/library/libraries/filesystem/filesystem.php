<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

define('N2_DS_INV', DIRECTORY_SEPARATOR == '/' ? '\\' : '/');

/**
 * Class N2FilesystemAbstract
 */
abstract class N2FilesystemAbstract
{

    /**
     * @var string /home/path/www/path/
     */
    public $_basepath;

    public $_librarypath;

    public static function getInstance() {
        static $instance;
        if (!is_object($instance)) {
            $instance = new N2Filesystem();
        }
        return $instance;
    }

    public static function check($base, $folder) {
        static $checked = array();
        if (!isset($checked[$base . '/' . $folder])) {
            $cacheFolder = $base . '/' . $folder;
            if (!self::existsFolder($cacheFolder)) {
                if (self::is_writable($base)) {
                    self::createFolder($cacheFolder);
                } else {
                    die('<div style="position:fixed;background:#fff;width:100%;height:100%;top:0;left:0;z-index:100000;">' . sprintf('<h2><b>%s</b> is not writable.</h2>', $base) . '<br><br><iframe style="width:100%;max-width:760px;height:100%;" src="http://doc.smartslider3.com/article/482-cache-folder-is-not-writable"></iframe></div>');
                }
            } else if (!self::is_writable($cacheFolder)) {
                die('<div style="position:fixed;background:#fff;width:100%;height:100%;top:0;left:0;z-index:100000;">' . sprintf('<h2><b>%s</b> is not writable.</h2>', $cacheFolder) . '<br><br><iframe style="width:100%;max-width:760px;height:100%;" src="http://doc.smartslider3.com/article/482-cache-folder-is-not-writable"></iframe></div>');
            }
            $checked[$base . '/' . $folder] = true;
        }
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    public static function toLinux($path) {
        return str_replace(DIRECTORY_SEPARATOR, '/', $path);
    }

    /**
     * @return string
     */
    public static function getBasePath() {
        $i = N2Filesystem::getInstance();
        return $i->_basepath;
    }

    public static function getWebCachePath() {
        self::check(self::getBasePath(), 'cache');
        return self::getBasePath() . '/cache/nextend/web';
    }

    public static function getNotWebCachePath() {
        return self::getBasePath() . '/cache/nextend/notweb';
    }

    /**
     * @param $path
     */
    public static function setBasePath($path) {
        $i            = N2Filesystem::getInstance();
        $i->_basepath = $path;
    }

    /**
     * @return mixed
     */
    public static function getLibraryPath() {
        $i = N2Filesystem::getInstance();
        return $i->_librarypath;
    }

    /**
     * @param $path
     */
    public static function setLibraryPath($path) {
        $i               = N2Filesystem::getInstance();
        $i->_librarypath = $path;
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    public static function pathToAbsoluteURL($path) {
        return N2Uri::pathToUri($path);
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    public static function pathToRelativePath($path) {
        $i = N2Filesystem::getInstance();
        return str_replace($i->_basepath, '', str_replace('/', DIRECTORY_SEPARATOR, $path));
    }

    /**
     * @param $path
     *
     * @return string
     */
    public static function pathToAbsolutePath($path) {
        $i = N2Filesystem::getInstance();
        return $i->_basepath . str_replace('/', DIRECTORY_SEPARATOR, $path);
    }

    /**
     * @param $url
     *
     * @return mixed
     */
    public static function absoluteURLToPath($url) {
        $baseUri = N2Uri::getBaseUri();
        if (substr($url, 0, strlen($baseUri)) == $baseUri) {
            $i = N2Filesystem::getInstance();
            return str_replace($baseUri, $i->_basepath, $url);
        }
        return $url;
    }

    /**
     * @param $file
     *
     * @return bool
     */
    public static function fileexists($file) {
        return is_file($file);
    }

    /**
     * @param $file
     *
     * @return bool
     */
    public static function safefileexists($file) {
        return realpath($file) && is_file($file);
    }

    /**
     * @param $dir
     *
     * @return array|bool
     */
    public static function folders($dir) {
        if (!is_dir($dir)) return false;
        $folders = array();
        foreach (scandir($dir) as $file) {
            if ($file == '.' || $file == '..') continue;
            if (is_dir($dir . DIRECTORY_SEPARATOR . $file)) $folders[] = $file;
        }
        return $folders;
    }

    /**
     * @param $path
     *
     * @return bool
     */
    public static function is_writable($path) {
        return is_writable($path);
    }

    /**
     * @param $path
     *
     * @return bool
     */
    public static function createFolder($path) {
        return mkdir($path, 0777, true);
    }

    /**
     * @param $dir
     *
     * @return bool
     */
    public static function deleteFolder($dir) {
        if (!is_dir($dir) || is_link($dir)) return unlink($dir);
        foreach (scandir($dir) as $file) {
            if ($file == '.' || $file == '..') continue;
            if (!self::deleteFolder($dir . DIRECTORY_SEPARATOR . $file)) {
                chmod($dir . DIRECTORY_SEPARATOR . $file, 0777);
                if (!self::deleteFolder($dir . DIRECTORY_SEPARATOR . $file)) return false;
            };
        }
        return rmdir($dir);
    }

    /**
     * @param $path
     *
     * @return bool
     */
    public static function existsFolder($path) {
        return is_dir($path);
    }

    /**
     * @param $path
     *
     * @return array
     */
    public static function files($path) {
        $files = array();
        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while (($file = readdir($dh)) !== false) {
                    $files[] = $file;
                }
                closedir($dh);
            }
        }
        return $files;
    }

    /**
     * @param $path
     *
     * @return bool
     */
    public static function existsFile($path) {
        return file_exists($path);
    }

    /**
     * @param $path
     * @param $buffer
     *
     * @return int
     */
    public static function createFile($path, $buffer) {
        return file_put_contents($path, $buffer);
    }

    /**
     * @param $path
     *
     * @return string
     */
    public static function readFile($path) {
        return file_get_contents($path);
    }

    /**
     * convert dir alias to normal format
     *
     * @param $pathName
     *
     * @return mixed
     */
    public static function dirFormat($pathName) {
        return str_replace(".", NDS, $pathName);
    }

    public static function getImagesFolder() {
        return '';
    }

    public static function realpath($path) {
        return rtrim(realpath($path), '/\\');
    }

    private static $translate = array();

    public static function registerTranslate($from, $to) {
        self::$translate[$from] = $to;
    }

    public static function translate($path) {
        $path = self::fixPathSeparator($path);
        foreach (self::$translate AS $k => $v) {
            if (strpos($path, $k) === 0) {
                return str_replace($k, $v, $path);
            }
        }
        return $path;
    }

    public static function fixPathSeparator($path) {
        return str_replace(N2_DS_INV, DIRECTORY_SEPARATOR, $path);
    }
}

N2Loader::import('libraries.filesystem.filesystem', 'platform');
