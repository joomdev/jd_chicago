<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2Localization extends N2LocalizationAbstract
{

    static function getLocale() {
        $lang = JFactory::getLanguage();
        return str_replace('-', '_', $lang->getTag());
    }
}