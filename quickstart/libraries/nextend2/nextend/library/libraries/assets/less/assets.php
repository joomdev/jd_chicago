<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2AssetsLess extends N2AssetsAbstract
{

    public function __construct() {
        $this->cache = new N2AssetsCacheLess();
    }

    protected function uniqueFiles() {
        $this->initGroups();
    }

    public function getFiles() {
        $this->uniqueFiles();

        $files = array();
        foreach ($this->groups AS $group) {
            $files[$group] = $this->cache->getAssetFile($group, $this->files[$group], $this->codes[$group]);
        }
        return $files;
    }
} 