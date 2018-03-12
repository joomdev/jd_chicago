<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2AnimationRenderer
{

    public static $sets = array();

    public static $mode;
}

N2AnimationRenderer::$mode = array(
    'solo' => array(
        'id'    => 'solo',
        'label' => n2_('Solo')
    ),
    '0'    => array(
        'id'    => '0',
        'label' => n2_('Chain')
    )
);