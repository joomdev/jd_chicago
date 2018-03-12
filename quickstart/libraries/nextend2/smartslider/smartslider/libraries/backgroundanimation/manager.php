<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('libraries.backgroundanimation.storage', 'smartslider');

class N2BackgroundAnimationManager
{

    public static function init() {
        static $inited = false;
        if (!$inited) {

            N2Pluggable::addAction('afterApplicationContent', 'N2BackgroundAnimationManager::load');
            $inited = true;
        }
    }

    public static function load() {
        N2Base::getApplication('system')->getApplicationType('backend');
        N2Base::getApplication('smartslider')->getApplicationType('backend')->run(array(
            'useRequest' => false,
            'controller' => 'backgroundanimation',
            'action'     => 'index'
        ));
    }
}

N2BackgroundAnimationManager::init();