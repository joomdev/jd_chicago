<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SystemBackendStyleController extends N2SystemBackendVisualManagerController
{

    protected $type = 'style';

    public function __construct($appType, $defaultParams) {
        $this->logoText = n2_('Style manager');

        N2Localization::addJS(array(
            'style',
            'styles',
        ));

        parent::__construct($appType, $defaultParams);
    }

    public function getModel() {
        return new N2SystemStyleModel();
    }

}