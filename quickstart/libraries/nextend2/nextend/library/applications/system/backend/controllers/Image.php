<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SystemBackendImageController extends N2SystemBackendVisualManagerController
{

    public $layoutName = "fulllightbox";

    protected $type = 'image';

    public function __construct($appType, $defaultParams) {
        $this->logoText = n2_('Image manager');

        N2Localization::addJS(array(
            'Generate',
            'Desktop image is empty!',
            'image',
            'images'
        ));

        parent::__construct($appType, $defaultParams);
    }

    public function getModel() {
        return new N2SystemImageModel();
    }
}