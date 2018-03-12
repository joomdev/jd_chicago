<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2LESS
{

    public static function addFile($pathToFile, $group, $context = array(), $importDir = null) {
        N2AssetsManager::$less->addFile(array(
            'file'      => $pathToFile,
            'context'   => $context,
            'importDir' => $importDir
        ), $group);
    }

    public static function build() {
        foreach (N2AssetsManager::$less->getFiles() AS $group => $file) {
            N2CSS::addFile($file, $group);
        }
    }
}