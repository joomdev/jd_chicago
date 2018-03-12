<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartSliderItem
{

    public static $i = array();

    public $slider, $slide;

    /**
     * @var N2SSPluginItemAbstract[]
     */
    private static $items = array();

    private static function _load() {
        static $loaded;
        if (!$loaded) {
            N2Plugin::callPlugin('ssitem', 'onNextendSliderItemShortcode', array(&self::$items));
            $loaded = true;
        }
    }

    /**
     * @param $slider N2SmartSliderAbstract
     * @param $slide  N2SmartSliderSlide
     */
    public function __construct($slider, $slide) {
        self::_load();

        $this->slider = $slider;
        $this->slide  = $slide;

        if (!isset(self::$i[$slider->elementId])) {
            self::$i[$slider->elementId] = 0;
        }

    }

    public function render($item) {
        $type = $item['type'];
        if (isset(self::$items[$type])) {
            $data = new N2Data($item['values']);
            self::$i[$this->slider->elementId]++;


            $itemId = $this->slider->elementId . 'item' . self::$i[$this->slider->elementId];
            /**
             * @var N2SSPluginItemAbstract
             */
            if ($this->slider->isAdmin) {
                return self::$items[$type]->renderAdmin($data, $itemId, $this->slider, $this->slide);
            }

            return self::$items[$type]->render($data, $itemId, $this->slider, $this->slide);
        }

        return '';
    }

    public function getFilled($item) {
        $type = $item['type'];
        if (isset(self::$items[$type])) {
            $item['values'] = self::$items[$type]->getFilled($this->slide, new N2Data($item['values']))
                                                 ->toArray();
        }
        return $item;
    }

    /**
     * @param N2SmartSliderExport      $export
     * @param                          $item
     */
    public static function prepareExport($export, $item) {
        self::_load();
        $type = $item['type'];
        if (isset(self::$items[$type])) {
            self::$items[$type]->prepareExport($export, new N2Data($item['values']));
        }
    }

    /**
     * @param N2SmartSliderImport      $import
     * @param                          $item
     *
     * @return mixed
     */
    public static function prepareImport($import, $item) {
        self::_load();
        $type = $item['type'];
        if (isset(self::$items[$type])) {
            $item['values'] = self::$items[$type]->prepareImport($import, new N2Data($item['values']))
                                                 ->toArray();
        }
        return $item;
    }
}


class N2SmartSliderItemHelper
{

    public $layer;
    public $data = array(
        'type'   => null,
        'values' => array()
    );

    public function __construct($slide, $type, $layerProperties = array(), $properties = array()) {

        $this->layer = new N2SmartSliderLayerHelper();
        $this->set('type', $type);
        $class      = 'N2SSPluginItem' . $type;
        $item       = new $class();
        $properties = array_merge($item->getValues(), $properties);
        foreach ($properties as $k => $v) {
            $this->setValues($k, $v);
        }
        foreach ($item->getLayerProperties() AS $k => $v) {
            if ($k == 'width' || $k == 'height' || $k == 'top' || $k == 'left') {

                $this->layer->set('desktopportrait' . $k, $v);
            } else {
                $this->layer->set($k, $v);
            }
        }
        $this->layer->set('name', $item->_title . ' layer')
                    ->set('items', array($this->data));

        foreach ($layerProperties AS $k => $v) {
            $this->layer->set($k, $v);
        }
        $slide->addLayer($this->layer);
    }

    public function set($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }

    public function setValues($key, $value) {
        $this->data['values'][$key] = $value;
        return $this;
    }

}