<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartsliderLayersModel extends N2Model
{

    function renderForm($data = array()) {

        N2Loader::import('libraries.animations.manager');

        $configurationXmlFile = dirname(__FILE__) . '/forms/layer.xml';

        N2Loader::import('libraries.form.form');
        $form = new N2Form();
        $form->loadArray($data);

        $form->loadXMLFile($configurationXmlFile);

        echo $form->render('layer');
    }

} 