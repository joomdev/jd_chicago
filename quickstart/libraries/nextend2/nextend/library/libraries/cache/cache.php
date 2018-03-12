<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2Cache
{

    public static $accessiblePath = '';
    public static $notAccessiblePath = '';

    protected $group = '';
    protected $isAccessible = false;
    protected $currentPath = '';

    public static function init() {
        static $inited;
        if (!$inited) {
            self::$accessiblePath    = N2Filesystem::getWebCachePath();
            self::$notAccessiblePath = N2Filesystem::getNotWebCachePath();
            $inited                  = true;
        }
    }

    public static function clearGroup($group) {
        N2Cache::init();

        if (N2Filesystem::existsFolder(self::$accessiblePath . NDS . $group)) {
            N2Filesystem::deleteFolder(self::$accessiblePath . NDS . $group);
        }
        if (N2Filesystem::existsFolder(self::$notAccessiblePath . NDS . $group)) {
            N2Filesystem::deleteFolder(self::$notAccessiblePath . NDS . $group);
        }
    }

    public function __construct($group, $isAccessible = false) {
        $this->group        = $group;
        $this->isAccessible = $isAccessible;
        $this->setCurrentPath();
    }

    protected function setCurrentPath() {
        N2Cache::init();
        if ($this->isAccessible) {
            $this->currentPath = self::$accessiblePath . NDS . $this->group;
        } else {
            $this->currentPath = self::$notAccessiblePath . NDS . $this->group;
        }
        if (!N2Filesystem::existsFolder($this->currentPath)) {
            N2Filesystem::createFolder($this->currentPath);
        }
    }

    protected function clearCurrentGroup() {
        self::clearGroup($this->group);
        if (!N2Filesystem::existsFolder($this->currentPath)) {
            N2Filesystem::createFolder($this->currentPath);
        }
    }

    protected function getStorageFilePath($fileName) {
        return $this->currentPath . NDS . $fileName;
    }
}
