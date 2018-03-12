<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import(array(
    'libraries.stylemanager.storage'
));

class N2SystemStyleModel extends N2SystemVisualModel
{

    public $type = 'style';

    public function renderForm() {
        $form = new N2Form();
        $form->loadXMLFile(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . 'form.xml');
        $form->render('n2-style-editor');
    }

    public function renderFormExtra() {
        $form = new N2Form();
        $form->loadXMLFile(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . 'extra.xml');
        $form->render('n2-style-editor');
    }
}