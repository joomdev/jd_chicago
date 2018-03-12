<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2CacheCombine extends N2Cache
{

    protected $files = array();
    protected $fileType = '';
    protected $minify = false;
    protected $options = array();

    public function __construct($fileType, $minify = false, $options = array()) {
        $this->fileType          = $fileType;
        $this->minify            = $minify;
        $this->options           = $options;
        $this->options['minify'] = $this->minify;
        parent::__construct('combined', true);
    }

    public function add($file) {
        if (!in_array($file, $this->files)) {
            $this->files[] = $file;
        }
    }

    protected function getHash() {
        $hash = '';
        for ($i = 0; $i < count($this->files); $i++) {
            $hash .= $this->files[$i] . filemtime($this->files[$i]);
        }
        return md5($hash . json_encode($this->options));
    }

    public function make() {
        $hash = $this->getHash();
        $file = $this->getStorageFilePath($hash . '.' . $this->fileType);
        if (!$this->isCached($file)) {
            $buffer = '';
            for ($i = 0; $i < count($this->files); $i++) {
                $buffer .= file_get_contents($this->files[$i]);
            }
            if ($this->minify !== false) {
                $buffer = call_user_func($this->minify, $buffer);
            }
            N2Filesystem::createFile($file, $buffer);
        }
        return $file;
    }

    private function isCached($file) {

        if (N2Filesystem::existsFile($file)) {
            return true;
        }
        return false;
    }
}