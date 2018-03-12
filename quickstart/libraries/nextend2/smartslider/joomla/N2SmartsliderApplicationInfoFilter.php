<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

if (!defined('N2SSPRO')) {
    define('N2SSPRO', 0);

}

class N2SmartsliderApplicationInfoFilter
{

    /**
     * @param $info NextendApplicationInfo
     */
    public static function filter($info) {
        $info->setUrl(JUri::root() . 'administrator/index.php?option=com_smartslider3');
        $info->setAcl('com_smartslider3');
    }
}