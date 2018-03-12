<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import('libraries.plugins.N2SliderWidgetAbstract', 'smartslider');

class N2SSPluginWidgetShadowShadow extends N2SSPluginWidgetAbstract
{

    var $_name = 'shadow';

    private static $key = 'widget-shadow-';

    static function getDefaults() {
        return array(
            'widget-shadow-position-mode'  => 'simple',
            'widget-shadow-position-area'  => 12,
            'widget-shadow-position-stack' => 4,
            'widget-shadow-width'          => '100%',
            'widget-shadow-shadow-image'   => '',
            'widget-shadow-shadow'         => '$ss$/plugins/widgetshadow/shadow/shadow/shadow/dark.png'
        );
    }

    function onShadowList(&$list) {
        $list[$this->_name] = $this->getPath();
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'shadow' . DIRECTORY_SEPARATOR;
    }

    static function getPositions(&$params) {
        $positions                    = array();
        $positions['shadow-position'] = array(
            self::$key . 'position-',
            'shadow'
        );
        return $positions;
    }

    static function render($slider, $id, $params) {

        $shadow = $params->get(self::$key . 'shadow-image');
        if (empty($shadow)) {
            $shadow = $params->get(self::$key . 'shadow');
            if ($shadow == -1) {
                $shadow = null;
            }
        }
        if (!$shadow) {
            return '';
        }

        N2JS::addFile(N2Filesystem::translate(dirname(__FILE__) . '/shadow/shadow.js'), $id);

        list($displayClass, $displayAttributes) = self::getDisplayAttributes($params, self::$key);

        list($style, $attributes) = self::getPosition($params, self::$key);

        $width = $params->get(self::$key . 'width');
        if (is_numeric($width) || substr($width, -1) == '%' || substr($width, -2) == 'px') {
            $style .= 'width:' . $width . ';';
        } else {
            $attributes['data-sswidth'] = $width;
        }

        $parameters = array(
            'overlay' => $params->get(self::$key . 'position-mode') != 'simple' || 0,
            'area'    => intval($params->get(self::$key . 'position-area'))
        );

        N2JS::addInline('new NextendSmartSliderWidgetShadow("' . $id . '", ' . json_encode($parameters) . ');');


        return NHtml::tag('div', $displayAttributes + $attributes + array(
                'class' => $displayClass . "nextend-shadow",
                'style' => $style
            ), NHtml::image(N2ImageHelper::fixed($shadow), '', array('style' => 'display: block; width:100%;max-width:none;','class'=>'nextend-shadow-image')));
    }

    public static function prepareExport($export, $params) {
        $export->addImage($params->get(self::$key . 'shadow-image', ''));
    }

    public static function prepareImport($import, $params) {

        $params->set(self::$key . 'shadow-image', $import->fixImage($params->get(self::$key . 'shadow-image', '')));
    }
}

N2Plugin::addPlugin('sswidgetshadow', 'N2SSPluginWidgetShadowShadow');
