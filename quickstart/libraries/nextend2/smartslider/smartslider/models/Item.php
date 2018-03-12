<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import('libraries.form.form');

class N2SmartsliderItemModel extends N2Model
{

    function renderForm($type, $item, $data = array()) {

        $configurationXmlFile = $item[4] . 'configuration.xml';

        $form = new N2Form(N2Base::getApplication('smartslider')->getApplicationType('backend'));
        $form->loadArray($data);

        $form->loadXMLFile($configurationXmlFile);

        echo $form->render('item_' . $type);
    }

} 