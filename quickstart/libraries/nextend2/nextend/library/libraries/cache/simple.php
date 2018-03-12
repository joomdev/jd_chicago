<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2CacheSimple extends N2Cache
{

    public function makeCache($fileName, $callable) {
        if (!$this->isCached($fileName)) {
            $this->createCacheFile($fileName, call_user_func($callable));
        }
        return $this->getStorageFilePath($fileName);
    }

    private function isCached($fileName) {
        if (N2Filesystem::existsFile($this->getStorageFilePath($fileName))) {
            return true;
        }
        return false;
    }

    private function createCacheFile($fileName, $content) {
        return N2Filesystem::createFile($this->getStorageFilePath($fileName), $content);
    }
}