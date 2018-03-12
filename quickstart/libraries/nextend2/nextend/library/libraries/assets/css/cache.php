<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2AssetsCacheCSS extends N2AssetsCache
{

    public $outputFileType = "css";

    private $baseUrl = '';

    protected function parseFile($content, $originalFilePath) {

        $this->baseUrl = N2Filesystem::pathToAbsoluteURL(dirname($originalFilePath));

        return preg_replace_callback('#url\([\'"]([^"\'\)]+)[\'"]\)#', array(
            $this,
            'makeUrl'
        ), $content);
    }

    private function makeUrl($matches) {
        if (substr($matches[1], 0, 5) == 'data:') return $matches[0];
        if (substr($matches[1], 0, 4) == 'http') return $matches[0];
        if (substr($matches[1], 0, 2) == '//') return $matches[0];

        return 'url(' . str_replace(array(
            'http://',
            'https://'
        ), '//', $this->baseUrl) . '/' . $matches[1] . ')';
    }
}