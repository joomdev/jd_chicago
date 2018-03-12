<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Localization::addJS(array());

class N2ElementUrl extends N2ElementUrlAbstract
{

    protected function extendParams($params) {
        $params['labelButton']      = 'Joomla';
        $params['labelDescription'] = n2_('Select article or menu item from your site.');
        $params['image']            = '/element/link_platform.png';
        return $params;
    }
}