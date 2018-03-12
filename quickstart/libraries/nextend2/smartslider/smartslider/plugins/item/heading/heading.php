<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import('libraries.plugins.N2SliderItemAbstract', 'smartslider');

class N2SSPluginItemHeading extends N2SSPluginItemAbstract
{

    var $_identifier = 'heading';

    protected $priority = 2;

    private static $font = 1009;

    public function __construct() {
        $this->_title = n2_x('Heading', 'Slide item');
    }

    private static function initDefaultFont() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-heading-font');
            if (is_array($res)) {
                self::$font = $res['value'];
            }
            if (is_numeric(self::$font)) {
                N2FontRenderer::preLoad(self::$font);
            }
            $inited = true;
        }
    }

    private static $style = '';

    private static function initDefaultStyle() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-heading-style');
            if (is_array($res)) {
                self::$style = $res['value'];
            }
            if (is_numeric(self::$style)) {
                N2StyleRenderer::preLoad(self::$style);
            }
            $inited = true;
        }
    }

    public static function onSmartsliderDefaultSettings(&$settings) {
        self::initDefaultFont();
        $settings['font'][] = '<param name="item-heading-font" type="font" previewmode="hover" label="' . n2_('Item') . ' - ' . n2_('Heading') . '" default="' . self::$font . '" />';

        self::initDefaultStyle();
        $settings['style'][] = '<param name="item-heading-style" type="style" set="heading" previewmode="heading" label="' . n2_('Item') . ' - ' . n2_('Heading') . '" default="' . self::$style . '" />';
    }

    function getTemplate($slider) {

        return "<div><h{priority} id='{uid}' class='{fontclass} {styleclass} {class}' style='display: {display}; {extrastyle};'><a href='#' class='{afontclass}' onclick='return false;'>{heading}</a></h{priority}>" . NHtml::scriptTemplate($this->getJs($slider->elementId, "{uid}")) . "</div>";
    }

    function getJs($sliderId, $id) {
        return '';
    
    }

    function _render($data, $itemId, $slider, $slide) {
        return $this->getHtml($data, $itemId, $slider, $slide);
    }

    function _renderAdmin($data, $itemId, $slider, $slide) {
        return $this->getHtml($data, $itemId, $slider, $slide);
    }

    private function getHtml($data, $id, $slider, $slide) {
        $attributes = array();

        $font  = N2FontRenderer::render($data->get('font'), 'hover', $slider->elementId, 'div#' . $slider->elementId . ' ', $slider->fontSize);
        $style = N2StyleRenderer::render($data->get('style'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' ');

        $linkAttributes = array();
        if ($this->isEditor) {
            $linkAttributes['onclick'] = 'return false;';
        }

        $title = $data->get('title', '');
        if (!empty($title)) {
            $attributes['title'] = $title;
        }

        list($link) = (array)N2Parse::parse($data->get('link', '#|*|'));
        if (!empty($link) && $link != '#') {
            $linkAttributes['class'] = $font;
            $font                    = '';
        }

        return $this->heading($data->get('priority', 2), $attributes + array(
                "id"    => $id,
                "class" => $font . $style . " " . $data->get('class', ''),
                "style" => "display:" . ($data->get('fullwidth', 1) ? 'block' : 'inline-block') . ";" . ($data->get('nowrap', 1) ? 'white-space:nowrap;' : '')
            ), $this->getLink($slide, $data, str_replace("\n", '<br />', strip_tags($slide->fill($data->get('heading', '')))), $linkAttributes));
    }

    private function heading($type, $attributes, $content) {
        return NHtml::tag("h{$type}", $attributes, $content);
    }

    function getValues() {
        self::initDefaultFont();
        self::initDefaultStyle();
        return array(
            'priority'                       => '2',
            'fullwidth'                      => 1,
            'nowrap'                         => 1,
            'heading'                        => n2_('Heading layer'),
            'title'                          => '',
            'link'                           => '#|*|_self',
            'font'                           => self::$font,
            'style'                          => self::$style,

            'split-text-transform-origin'    => '50|*|50|*|0',
            'split-text-backface-visibility' => 1,

            'split-text-animation-in'        => '',
            'split-text-delay-in'            => 0,

            'split-text-animation-out'       => '',
            'split-text-delay-out'           => 0,

            'class'                          => ''
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->_identifier . DIRECTORY_SEPARATOR;
    }

    public function getFilled($slide, $data) {
        $data->set('heading', $slide->fill($data->get('heading', '')));
        $data->set('link', $slide->fill($data->get('link', '#|*|')));
        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addVisual($data->get('font'));
        $export->addVisual($data->get('style'));
        $export->addLightbox($data->get('link'));
    }

    public function prepareImport($import, $data) {
        $data->set('font', $import->fixSection($data->get('font')));
        $data->set('style', $import->fixSection($data->get('style')));
        $data->set('link', $import->fixLightbox($data->get('link')));
        return $data;
    }

}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemHeading');

N2Pluggable::addAction('smartsliderDefault', 'N2SSPluginItemHeading::onSmartsliderDefaultSettings');

