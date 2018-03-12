<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2AssetsCache
{

    public $outputFileType;

    protected $group, $files, $codes;

    public function getAssetRaw($group, &$files = array(), &$codes = array()) {
        $this->group = $group;
        $this->files = $files;
        $this->codes = $codes;

        return $this->getCachedContent();
    }

    public function getAssetFile($group, &$files = array(), &$codes = array()) {
        $this->group = $group;
        $this->files = $files;
        $this->codes = $codes;

        $cache = new N2CacheManifest($group, true, true);

        $hash = $this->getHash();
        return $cache->makeCache($group . "." . $this->outputFileType, $hash, array(
            $this,
            'getCachedContent'
        ));
    }

    protected function getHash() {
        $hash = '';
        foreach ($this->files AS $file) {
            $hash .= $this->makeFileHash($file);
        }
        foreach ($this->codes AS $code) {
            $hash .= $code;
        }

        return md5($hash);
    }

    protected function getCacheFileName() {
        $hash = '';
        foreach ($this->files AS $file) {
            $hash .= $this->makeFileHash($file);
        }
        foreach ($this->codes AS $code) {
            $hash .= $code;
        }

        return md5($hash) . "." . $this->outputFileType;
    }

    public function getCachedContent() {
        $fileContents = '';
        foreach ($this->files AS $file) {
            $fileContents .= $this->parseFile(N2Filesystem::readFile($file), $file) . "\n";
        }

        foreach ($this->codes AS $code) {
            $fileContents .= $code . "\n";
        }
        return $fileContents;
    }

    protected function makeFileHash($file) {
        return $file . filemtime($file);
    }

    protected function parseFile($content, $originalFilePath) {
        return $content;
    }

}
