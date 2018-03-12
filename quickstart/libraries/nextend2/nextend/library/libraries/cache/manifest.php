<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2CacheManifest extends N2Cache
{

    private $isRaw = false;

    public function __construct($group, $isAccessible = false, $isRaw = false) {
        parent::__construct($group, $isAccessible);
        $this->isRaw = $isRaw;
    }

    public function makeCache($fileName, $hash, $callable) {
        if (!$this->isCached($fileName, $hash)) {

            $return = call_user_func($callable);
            if ($return === false) {
                return false;
            }
            return $this->createCacheFile($fileName, $hash, $return);
        }
        if ($this->isAccessible) {
            return $this->getStorageFilePath($fileName);
        }
        return json_decode(N2Filesystem::readFile($this->getStorageFilePath($fileName)), true);
    }

    private function isCached($fileName, $hash) {

        if (N2Filesystem::existsFile($this->getManifestFilePath($fileName))) {
            $manifestData = json_decode(N2Filesystem::readFile($this->getManifestFilePath($fileName)), true);

            if (!$this->isCacheValid($manifestData) || $manifestData['hash'] != $hash) {
                $this->clean($fileName);
                return false;
            }
            return true;
        }
        return false;
    }

    private function createCacheFile($fileName, $hash, $content) {

        $manifestData = array();

        $manifestData['hash'] = $hash;
        $this->addManifestData($manifestData);
        N2Filesystem::createFile($this->getManifestFilePath($fileName), json_encode($manifestData));

        N2Filesystem::createFile($this->getStorageFilePath($fileName), $this->isRaw ? $content : json_encode($content));
        if ($this->isAccessible) {
            return $this->getStorageFilePath($fileName);
        }
        return $content;
    }

    protected function isCacheValid(&$manifestData) {
        return true;
    }

    protected function addManifestData(&$manifestData) {

    }

    public function clean($fileName) {
        if (N2Filesystem::existsFile($this->getManifestFilePath($fileName))) {
            unlink($this->getManifestFilePath($fileName));
        }
        if (N2Filesystem::existsFile($this->getStorageFilePath($fileName))) {
            unlink($this->getStorageFilePath($fileName));
        }
    }

    protected function getManifestFilePath($fileName) {
        return $this->getStorageFilePath($fileName) . '.manifest';
    }
}