<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2Uri extends N2UriAbstract
{

    function __construct() {
        $this->_baseuri = rtrim(JURI::root(), '/');

        $this->_currentbase = JURI::base();

        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off') {
            $this->_baseuri = str_replace('http://', 'https://', $this->_baseuri);
        }
    }

    static function ajaxUri($query = '', $magento = 'nextendlibrary') {
        return JUri::current();
    }

}