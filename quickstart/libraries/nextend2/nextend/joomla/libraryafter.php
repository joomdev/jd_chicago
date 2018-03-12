<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

if (class_exists('JEventDispatcher', false)) {
    $dispatcher = JEventDispatcher::getInstance();
} else {
    $dispatcher = JDispatcher::getInstance();
}

$dispatcher->trigger('onInitN2Library');