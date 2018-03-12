<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Class N2Filesystem
 */
class N2Filesystem extends N2FilesystemAbstract
{

    public function __construct() {
        $this->_basepath    = realpath(JPATH_SITE == '' ? '' : JPATH_SITE . NDS);
        $this->_cachepath   = realpath(JPATH_CACHE);
        $this->_librarypath = str_replace($this->_basepath, '', N2LIBRARY);
    }

    public static function getWebCachePath() {
        $i = N2Filesystem::getInstance();
        self::check($i->_basepath . '/media', 'nextend');
        return $i->_basepath . '/media/nextend';
    }

    public static function getNotWebCachePath() {
        self::check(JPATH_CACHE, 'nextend');
        return JPATH_CACHE . '/nextend';
    }

    public static function getImagesFolder() {
        $i = N2Filesystem::getInstance();
        if(defined('JPATH_IMAGES')){
            return $i->_basepath . JPATH_IMAGES;
        }
        return $i->_basepath . '/images';
    }

    /**
     * Calling JFile:exists() method
     *
     * @param $file
     *
     * @return bool
     */
    static function fileexists($file) {
        return JFile::exists($file);
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    static function folders($path) {
        return JFolder::folders($path);
    }

    /**
     * @param $path
     *
     * @return bool
     */
    static function is_writable($path) {
        return true;
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    static function createFolder($path) {
        return JFolder::create($path);
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    static function deleteFolder($path) {
        return JFolder::delete($path);
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    static function existsFolder($path) {
        return JFolder::exists($path);
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    static function files($path) {
        return JFolder::files($path);
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    static function existsFile($path) {
        return JFile::exists($path);
    }

    /**
     * @param $path
     * @param $buffer
     *
     * @return mixed
     */
    static function createFile($path, $buffer) {
        return JFile::write($path, $buffer);
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    static function readFile($path) {
        return JFile::read($path);
    }


}