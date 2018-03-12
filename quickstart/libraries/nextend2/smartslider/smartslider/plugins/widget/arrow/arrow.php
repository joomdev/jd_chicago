<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SSPluginWidgetArrow extends N2PluginBase
{

    private static $group = 'arrow';

    function onWidgetList(&$list) {
        $list[self::$group] = array(
            n2_('Arrows'),
            $this->getPath(),
            1
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . self::$group . DIRECTORY_SEPARATOR;
    }
}

N2Plugin::addPlugin('sswidget', 'N2SSPluginWidgetArrow');