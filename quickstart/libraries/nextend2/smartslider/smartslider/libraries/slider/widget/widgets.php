<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartSliderWidgets
{

    public $enabledWidgets = array();

    public $widgets = array();

    private $positions = array(
        1  => array(
            'side'       => 'vertical',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'vertical',
            'horizontal' => array(
                'side'     => 'left',
                'position' => 'width/2-{widgetname}width/2'
            ),

            'vertical'   => array(
                'side'     => 'bottom',
                'position' => 'height'
            )
        ),
        2  => array(
            'side'       => 'both',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'horizontal',
            'horizontal' => array(
                'side'     => 'left',
                'position' => '0'
            ),

            'vertical'   => array(
                'side'     => 'top',
                'position' => '0'
            )
        ),
        3  => array(
            'side'       => 'vertical',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'vertical',
            'horizontal' => array(
                'side'     => 'left',
                'position' => 'width/2-{widgetname}width/2'
            ),

            'vertical'   => array(
                'side'     => 'top',
                'position' => '0'
            )
        ),
        4  => array(
            'side'       => 'both',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'horizontal',
            'horizontal' => array(
                'side'     => 'right',
                'position' => '0'
            ),

            'vertical'   => array(
                'side'     => 'top',
                'position' => '0'
            )
        ),
        5  => array(
            'side'       => 'horizontal',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'horizontal',
            'horizontal' => array(
                'side'     => 'right',
                'position' => 'width'
            ),

            'vertical'   => array(
                'side'     => 'top',
                'position' => 'height/2-{widgetname}height/2'
            )
        ),
        6  => array(
            'side'       => 'horizontal',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'horizontal',
            'horizontal' => array(
                'side'     => 'left',
                'position' => '0'
            ),

            'vertical'   => array(
                'side'     => 'top',
                'position' => 'height/2-{widgetname}height/2'
            )
        ),
        7  => array(
            'side'       => 'horizontal',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'horizontal',
            'horizontal' => array(
                'side'     => 'right',
                'position' => '0'
            ),

            'vertical'   => array(
                'side'     => 'top',
                'position' => 'height/2-{widgetname}height/2'
            )
        ),
        8  => array(
            'side'       => 'horizontal',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'horizontal',
            'horizontal' => array(
                'side'     => 'left',
                'position' => 'width'
            ),

            'vertical'   => array(
                'side'     => 'top',
                'position' => 'height/2-{widgetname}height/2'
            )
        ),
        9  => array(
            'side'       => 'both',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'horizontal',
            'horizontal' => array(
                'side'     => 'left',
                'position' => '0'
            ),

            'vertical'   => array(
                'side'     => 'bottom',
                'position' => '0'
            )
        ),
        10 => array(
            'side'       => 'vertical',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'vertical',
            'horizontal' => array(
                'side'     => 'left',
                'position' => 'width/2-{widgetname}width/2'
            ),

            'vertical'   => array(
                'side'     => 'bottom',
                'position' => '0'
            )
        ),
        11 => array(
            'side'       => 'both',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'horizontal',
            'horizontal' => array(
                'side'     => 'right',
                'position' => '0'
            ),

            'vertical'   => array(
                'side'     => 'bottom',
                'position' => '0'
            )
        ),
        12 => array(
            'side'       => 'vertical',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'vertical',
            'horizontal' => array(
                'side'     => 'left',
                'position' => 'width/2-{widgetname}width/2'
            ),

            'vertical'   => array(
                'side'     => 'top',
                'position' => 'height'
            )
        )
    );

    /**
     * @param $slider N2SmartSlider
     */
    public function __construct($slider) {

        if (!$slider->isAdmin) {
            $params  = $slider->params;
            $plugins = array();

            N2Plugin::callPlugin('sswidget', 'onWidgetList', array(&$plugins));

            foreach ($plugins AS $k => $v) {
                $widget = $params->get('widget' . $k);
                if ($widget && $widget != 'disabled') {
                    $this->enabledWidgets[$k] = $widget;
                }
            }

            $positions = array();
            foreach ($this->enabledWidgets AS $k => $v) {
                $class = 'N2SSPluginWidget' . $k . $v;
                if (class_exists($class, false)) {
                    $params->fillDefault(call_user_func(array(
                        $class,
                        'getDefaults'
                    )));

                    $positions += call_user_func_array(array(
                        $class,
                        'getPositions'
                    ), array(&$params));
                } else {
                    unset($this->enabledWidgets[$k]);
                }
            }

            $this->makePositions($positions, $params);
            foreach ($this->enabledWidgets AS $k => $v) {
                $class = 'N2SSPluginWidget' . $k . $v;

                $this->widgets[$k] = call_user_func(array(
                    $class,
                    'render'
                ), $slider, $slider->elementId, $params);
            }
        }
    }

    function echoOnce($k) {
        if (isset($this->widgets[$k])) {
            echo $this->widgets[$k];
            unset($this->widgets[$k]);
        }
    }

    function echoOne($k) {
        if (isset($this->widgets[$k])) {
            echo $this->widgets[$k];
        }
    }

    function echoRemainder() {
        foreach ($this->widgets AS $v) {
            echo $v . "\n";
        }
    }

    function makePositions($positions, &$params) {
        $priority = array(
            array(),
            array(),
            array(),
            array()
        );
        foreach ($positions AS $k => $v) {
            list($key, $name) = $v;
            if ($params->get($key . 'mode') == 'simple') {
                $priority[intval($params->get($key . 'stack', 1)) - 1][] = array(
                    $k => $positions[$k]
                );
            } else {
                unset($positions[$k]);
            }
        }

        foreach ($priority AS $current) {
            foreach ($current AS $positions) {
                foreach ($positions AS $k => $v) {
                    $this->makePositionByIndex($params, $v[0], $v[1]);
                }
            }
        }
    }

    function makePositionByIndex(&$params, $key, $name) {

        $values = array();

        $area = intval($params->get($key . 'area'));

        $position = $this->positions[$area];

        $values['horizontal']          = $position['horizontal']['side'];
        $values['horizontal-position'] = str_replace('{widgetname}', $name, $position['horizontal']['position']);
        $values['horizontal-unit']     = 'px';

        $values['vertical']          = $position['vertical']['side'];
        $values['vertical-position'] = str_replace('{widgetname}', $name, $position['vertical']['position']);
        $values['vertical-unit']     = 'px';

        $offset = intval($params->get($key . 'offset', 0));

        if ($offset != 0 && ($position['side'] == 'vertical' || $position['side'] == 'both')) {
            $values['vertical-position'] .= "+" . $position['modifierV'] * $offset;
        }

        if ($offset != 0 && ($position['side'] == 'horizontal' || $position['side'] == 'both')) {
            $values['horizontal-position'] .= "+" . $position['modifierH'] * $offset;
        }

        if ($position['stack'] == 'vertical') {
            if ($offset > 0) {
                $calc = "({$name}height > 0 ? {$name}height+{$offset} : 0)";
            } else {
                $calc = "{$name}height";
            }
            if ($position['modifierV'] != 1) {
                $calc = $position['modifierV'] . "*{$calc}";
            }
            $this->positions[$area]['vertical']['position'] .= '+' . $calc;
            /* check if we need stacking on both side
            if ($position['side'] == 'both') {
                $this->positions[$area]['horizontal']['position'] .= '+(' . $position['modifierH'] . "*{$offset})";
            }
            */
        }

        if ($position['stack'] == 'horizontal') {
            if ($offset > 0) {
                $calc = "({$name}width > 0 ? {$name}width+{$offset} : 0)";
            } else {
                $calc = "{$name}width";
            }
            if ($position['modifierH'] != 1) {
                $calc = $position['modifierH'] . "*{$calc}";
            }
            $this->positions[$area]['horizontal']['position'] .= '+' . $calc;
            /* check if we need stacking on both side
            if ($position['side'] == 'both') {
                $this->positions[$area]['vertical']['position'] .= '+(' . $position['modifierV'] . "*{$offset})";
            }
            */
        }

        foreach ($values AS $k => $v) {
            $params->set($key . $k, $v);
        }
    }
}