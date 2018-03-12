<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class  N2SmartSliderLayer
{

    private $slider, $slide, $item;

    /**
     * @param $slider N2SmartSliderAbstract
     * @param $slide  N2SmartSliderSlide
     */
    public function __construct($slider, $slide) {
        $this->slider = $slider;
        $this->slide  = $slide;
        $this->item   = new N2SmartSliderItem($slider, $slide);
    }

    private function WHUnit($value) {
        if ($value == 'auto' || substr($value, -1) == '%') {
            return $value;
        }
        return $value . 'px';
    }

    public function render($layer) {

        $innerHTML = '';
        for ($i = 0; $i < count($layer['items']); $i++) {
            $innerHTML .= $this->item->render($layer['items'][$i]);
        }
        unset($layer['items']);

        $cropStyle = $layer['crop'];

        if ($this->slider->isAdmin) {
            if ($layer['crop'] == 'auto') {
                $cropStyle = 'hidden';
            }
        }

        if ($layer['crop'] == 'mask') {
            $cropStyle = 'hidden';
            $innerHTML = NHtml::tag('div', array('class' => 'n2-ss-layer-mask'), $innerHTML);
        } else if (!$this->slider->isAdmin && $layer['parallax'] > 0) {
            $innerHTML = NHtml::tag('div', array(
                'class' => 'n2-ss-layer-parallax'
            ), $innerHTML);
        }

        if (!isset($layer['responsiveposition'])) {
            $layer['responsiveposition'] = 1;
        }

        if (!isset($layer['responsivesize'])) {
            $layer['responsivesize'] = 1;
        }


        $style = '';
        /*if (isset($layer['adaptivefont']) && $layer['adaptivefont']) {
            $style .= 'font-size: ' . $this->slider->fontSize . 'px;';
        }*/
        if (isset($layer['inneralign'])) {
            $style .= 'text-align:' . $layer['inneralign'];
        }

        $style .= ';left:' . $layer['desktopportraitleft'] . 'px';
        $style .= ';top:' . $layer['desktopportraittop'] . 'px';
        $style .= ';width:' . $this->WHUnit($layer['desktopportraitwidth']);
        $style .= ';height:' . $this->WHUnit($layer['desktopportraitheight']);

        if (isset($layer['zIndex'])) {
            $zIndex = $layer['zIndex'];
            unset($layer['zIndex']);
        } else {
            preg_match('/z\-index:.*?([0-9]+);/', $layer['style'], $out);
            $zIndex = $out[1];
            unset($layer['style']);
        }

        $attributes = array(
            'class'           => 'n2-ss-layer',
            'style'           => 'z-index:' . $zIndex . ';overflow:' . $cropStyle . ';' . $style . ';',
            'data-animations' => base64_encode(json_encode($layer['animations']))
        );

        if (!empty($layer['id'])) {
            $attributes['id'] = $layer['id'];
            unset($layer['id']);
        }

        unset($layer['animations']);

        if (!$this->slider->isAdmin && $layer['parallax'] < 1) {
            unset($layer['parallax']);
        }

        if (!$this->slider->isAdmin) {
            $this->getEventAttributes($attributes, $layer, $this->slider->elementId);
        }

        foreach ($layer AS $k => $data) {
            $attributes['data-' . $k] = $data;
        }
        return NHtml::tag('div', $attributes, $innerHTML);
    }

    public function getFilled($layer) {
        $items = array();
        for ($i = 0; $i < count($layer['items']); $i++) {
            $items [] = $this->item->getFilled($layer['items'][$i]);
        }
        $layer['items'] = $items;
        return $layer;
    }

    /**
     * @param N2SmartSliderExport      $export
     * @param                          $rawLayers
     */
    public static function prepareExport($export, $rawLayers) {
        $layers = json_decode($rawLayers, true);
        foreach ($layers AS $layer) {

            foreach ($layer['items'] AS $item) {
                N2SmartSliderItem::prepareExport($export, $item);
            }
        }
    }

    /**
     * @param N2SmartSliderImport      $import
     * @param                          $rawLayers
     *
     * @return mixed|string|void
     */
    public static function prepareImport($import, $rawLayers) {
        $layers = json_decode($rawLayers, true);
        for ($i = 0; $i < count($layers); $i++) {
            for ($j = 0; $j < count($layers[$i]['items']); $j++) {
                $layers[$i]['items'][$j] = N2SmartSliderItem::prepareImport($import, $layers[$i]['items'][$j]);
            }
        }
        return json_encode($layers);
    }

    public static function sort($layers) {
        $children = array();
        for ($i = count($layers) - 1; $i >= 0; $i--) {
            if (!empty($layers[$i]['parentid'])) {
                $parentId = $layers[$i]['parentid'];
                if (!isset($children[$parentId])) {
                    $children[$parentId] = array();
                }
                $children[$parentId][] = $layers[$i];
                array_splice($layers, $i, 1);
            }
        }

        for ($i = 0; $i < count($layers); $i++) {
            if (isset($layers[$i]['id']) && isset($children[$layers[$i]['id']])) {
                array_splice($layers, $i + 1, 0, $children[$layers[$i]['id']]);
                unset($children[$layers[$i]['id']]);
            }
        }
        return $layers;
    }

    private static function uid($length = 12) {
        $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString     = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function translateIds($layers) {
        $layers        = self::sort($layers);
        $idTranslation = array();
        for ($i = 0; $i < count($layers); $i++) {
            if (!empty($layers[$i]['id'])) {
                $newId                            = 'd' . self::uid();
                $idTranslation[$layers[$i]['id']] = $newId;
                $layers[$i]['id']                 = $newId;
            }
            if (!empty($layers[$i]['parentid'])) {
                if (isset($idTranslation[$layers[$i]['parentid']])) {
                    $layers[$i]['parentid'] = $idTranslation[$layers[$i]['parentid']];
                } else {
                    $layers[$i]['parentid'] = '';
                }
            }
        }
        return $layers;
    }

    protected function getEventAttributes(&$attributes, &$layer, $sliderId) {
        if (!empty($layer['mouseenter'])) {
            $attributes['data-mouseenter'] = $this->parseEventCode($layer['mouseenter'], $sliderId);
            unset($layer['mouseenter']);
        }
        if (!empty($layer['click'])) {
            $attributes['data-click'] = $this->parseEventCode($layer['click'], $sliderId);
            $attributes['style'] .= 'cursor:pointer;';
            unset($layer['click']);
        }
        if (!empty($layer['mouseleave'])) {
            $attributes['data-mouseleave'] = $this->parseEventCode($layer['mouseleave'], $sliderId);
            unset($layer['mouseleave']);
        }
        if (!empty($layer['play'])) {
            $attributes['data-play'] = $this->parseEventCode($layer['play'], $sliderId);
            unset($layer['play']);
        }
        if (!empty($layer['pause'])) {
            $attributes['data-pause'] = $this->parseEventCode($layer['pause'], $sliderId);
            unset($layer['pause']);
        }
        if (!empty($layer['stop'])) {
            $attributes['data-stop'] = $this->parseEventCode($layer['stop'], $sliderId);
            unset($layer['stop']);
        }
    }

    protected function parseEventCode($code, $elementId) {
        if (preg_match('/^[a-zA-Z0-9_\-,]+$/', $code)) {
            if (is_numeric($code)) {
                $code = "window['" . $elementId . "'].changeTo(" . ($code - 1) . ");";
            } else if ($code == 'next') {
                $code = "window['" . $elementId . "'].next();";
            } else if ($code == 'previous') {
                $code = "window['" . $elementId . "'].previous();";
            } else {
                $code = "n2ss.trigger(this, '" . $code . "');";
            }
        }
        return $code;
    }
}


class N2SmartSliderLayerHelper
{

    public $data = array(
        "zIndex"                      => 1,
        "eye"                         => false,
        "lock"                        => false,
        "animations"                  => array(
            "specialZeroIn"       => 0,
            "transformOriginIn"   => "50|*|50|*|0",
            "inPlayEvent"         => "",
            "repeatCount"         => 0,
            "repeatStartDelay"    => 0,
            "transformOriginLoop" => "50|*|50|*|0",
            "loopPlayEvent"       => "",
            "loopPauseEvent"      => "",
            "loopStopEvent"       => "",
            "transformOriginOut"  => "50|*|50|*|0",
            "outPlayEvent"        => "",
            "instantOut"          => 1,
            "in"                  => array(),
            "loop"                => array(),
            "out"                 => array()
        ),
        "id"                          => null,
        "parentid"                    => null,
        "name"                        => "Layer",
        "namesynced"                  => 1,
        "crop"                        => "visible",
        "inneralign"                  => "left",
        "parallax"                    => 0,
        "adaptivefont"                => 0,
        "desktopportrait"             => 1,
        "desktoplandscape"            => 1,
        "tabletportrait"              => 1,
        "tabletlandscape"             => 1,
        "mobileportrait"              => 1,
        "mobilelandscape"             => 1,
        "responsiveposition"          => 1,
        "responsivesize"              => 1,
        "desktopportraitleft"         => 0,
        "desktopportraittop"          => 0,
        "desktopportraitwidth"        => "auto",
        "desktopportraitheight"       => "auto",
        "desktopportraitalign"        => "center",
        "desktopportraitvalign"       => "middle",
        "desktopportraitparentalign"  => "center",
        "desktopportraitparentvalign" => "middle",
        "desktopportraitfontsize"     => 100,
        "items"                       => array()

    );

    public function __construct($properties = array()) {
        foreach ($properties as $k => $v) {
            $this->data[$k] = $v;
        }
    }

    public function set($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }
}