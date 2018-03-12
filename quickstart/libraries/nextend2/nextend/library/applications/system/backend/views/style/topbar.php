<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

$this->widget->init('topbar', array(
    "menu"    => array(
        NHtml::tag('div', array(
            'class' => 'n2-form-dark'
        ), NHtml::tag('a', array(
                'href'  => '#',
                'id'    => 'n2-style-editor-set-as-linked',
                'class' => 'n2-button n2-button-blue n2-button-medium n2-h5 n2-b n2-uc',
            ), n2_('Apply as linked')))
    ),
    "actions" => array(
        NHtml::tag('a', array(
            'href'  => '#',
            'id'    => 'n2-style-editor-cancel',
            'class' => 'n2-button n2-button-red n2-button-big n2-h4 n2-b n2-uc'
        ), n2_('Cancel')),
        NHtml::tag('a', array(
            'href'  => '#',
            'id'    => 'n2-style-editor-save',
            'class' => 'n2-button n2-button-green n2-button-big n2-h4 n2-b n2-uc',
        ), n2_('Apply'))
    ),
    'fixTo'   => false
));
 