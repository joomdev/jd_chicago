<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('helpers.controllers.VisualManager', 'system.backend');

class N2SmartSliderBackendLayoutController extends N2SystemBackendVisualManagerController
{

    public $layoutName = "sidebar";

    protected $type = 'layout';

    public function initialize() {
        parent::initialize();

        N2Localization::addJS(array(
            'Load layout',
            'Load whole slide',
            'Load only layers'
        ));
    }

    protected function loadModel() {

        N2Loader::import(array(
            'models.' . $this->type
        ), 'smartslider');
    }

    public function getModel() {
        return new N2SmartSliderLayoutModel();
    }

}