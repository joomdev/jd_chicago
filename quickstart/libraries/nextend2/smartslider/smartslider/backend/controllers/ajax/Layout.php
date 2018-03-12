<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Base::getApplication('system')->getApplicationType('backend');
N2Loader::import('helpers.controllers.VisualManagerAjax', 'system.backend');

class N2SmartSliderBackendLayoutControllerAjax extends N2SystemBackendVisualManagerControllerAjax
{

    protected $type = 'layout';

    public function getModel() {
        N2Loader::import(array(
            'models.' . $this->type
        ), 'smartslider');
        return new N2SmartSliderLayoutModel();
    }
}