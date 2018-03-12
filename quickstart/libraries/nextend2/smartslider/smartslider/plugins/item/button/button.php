<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import('libraries.plugins.N2SliderItemAbstract', 'smartslider');

class N2SSPluginItemButton extends N2SSPluginItemAbstract
{

    public $_identifier = 'button';

    protected $priority = 4;

    private static $font = 1103;

    public function __construct() {
        $this->_title = n2_x('Button', 'Slide item');
    }

    private static function initDefaultFont() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-button-font');
            if (is_array($res)) {
                self::$font = $res['value'];
            }
            if (is_numeric(self::$font)) {
                N2FontRenderer::preLoad(self::$font);
            }
            $inited = true;
        }
    }

    private static $style = 1101;

    private static function initDefaultStyle() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-button-style');
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
        $settings['font'][] = '<param name="item-button-font" type="font" previewmode="link" set="1100" label="' . n2_('Item') . ' - ' . n2_('Button') . '" default="' . self::$font . '" />';

        self::initDefaultStyle();
        $settings['style'][] = '<param name="item-button-style" type="style" previewmode="button" set="1100" label="' . n2_('Item') . ' - ' . n2_('Button') . '" default="' . self::$style . '" />';
    }

    public function getTemplate($slider) {
        return NHtml::tag("div", array(
            "class" => "nextend-smartslider-button-container {fontclass}",
            "style" => "cursor: pointer; display: {display}; {extrastyle};"
        ), NHtml::link("{content}", "{url}", array(
            "onclick" => 'return false;',
            "target"  => "{target}",
            "style"   => "display: {display}",
            "class"   => "{styleclass} {class}"
        )));
    }

    public function _render($data, $itemId, $slider, $slide) {
        return $this->getHtml($data, $itemId, $slider, $slide);
    }

    function _renderAdmin($data, $itemId, $slider, $slide) {
        return $this->getHtml($data, $itemId, $slider, $slide);
    }

    private function getHtml($data, $id, $slider, $slide) {

        $font = N2FontRenderer::render($data->get('font'), 'link', $slider->elementId, 'div#' . $slider->elementId . ' ', $slider->fontSize);

        $html = NHtml::openTag("div", array(
            "class" => "nextend-smartslider-button-container {$font}",
            "style" => "cursor: pointer; display:" . ($data->get('fullwidth', 0) ? 'block' : 'inline-block') . ";" . ($data->get('nowrap', 1) ? 'white-space:nowrap;' : '')
        ));

        $style = N2StyleRenderer::render($data->get('style'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' ');

        $html .= $this->getLink($slide, $data, $slide->fill($data->get("content")), array(
            "style" => "display:" . ($data->get('fullwidth', 0) ? 'block' : 'inline-block') . ";",
            "class" => "{$style} {$data->get('class', '')}"
        ), true);

        $html .= NHtml::closeTag("div");

        return $html;
    }

    function getValues() {
        self::initDefaultFont();
        self::initDefaultStyle();

        return array(
            'content'      => n2_('MORE'),
            'nowrap'       => 1,
            'fullwidth'    => 0,
            'link'         => '#|*|_self',
            'font'         => self::$font,
            'style'        => self::$style,
            'class'        => ''
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->_identifier . DIRECTORY_SEPARATOR;
    }

    public function getFilled($slide, $data) {
        $data->set('content', $slide->fill($data->get('content', '')));
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

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemButton');

N2Pluggable::addAction('smartsliderDefault', 'N2SSPluginItemButton::onSmartsliderDefaultSettings');
