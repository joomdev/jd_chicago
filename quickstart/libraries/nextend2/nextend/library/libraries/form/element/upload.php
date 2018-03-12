<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import('libraries.form.element.text');

class N2ElementUpload extends N2ElementText
{

    public $fieldType = 'file';

    protected function getClass() {
        return 'n2-form-element-file ';
    }
}