<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
/**
 * @todo: Refactor with fragments
 */
N2Loader::import('libraries.form.element.hidden');

class N2ElementPluginMatrix extends N2ElementHidden
{

    var $_list = null;

    function fetchElement() {
        $widgetTypes = $this->getOptions();

        $id = 'n2-form-matrix-' . $this->_id;

        $html = NHtml::openTag("div", array(
            'id'    => $id,
            "class" => "n2-form-matrix"
        ));

        $value = $this->getValue();

        $test = false;

        foreach ($widgetTypes AS $type => $v) {
            if ($value == $type) {
                $test = true;
                break;
            }
        }

        if (!$test) $value = 'arrow';

        $html .= NHtml::openTag('div', array('class' => 'n2-h2 n2-content-box-title-bg n2-form-matrix-views'));

        $class = 'n2-underline n2-h4 n2-uc n2-has-underline n2-form-matrix-menu';
        foreach ($widgetTypes AS $type => $v) {

            $html .= NHtml::tag("div", array(
                "onclick" => "n2('#{$this->_id}').val('{$type}');",
                "class"   => $class . ($value == $type ? ' n2-active' : '')
            ), NHtml::tag("span", array("class" => "n2-underline"), $v[0]));

        }
        $html .= NHtml::closeTag("div");


        $html .= NHtml::openTag("div", array(
            "class" => "n2-tabs"
        ));

        foreach ($widgetTypes AS $type => $v) {


            $html .= NHtml::openTag('div', array(
                'class' => 'n2-form-matrix-pane' . ($value == $type ? ' n2-active' : '')
            ));

            $GLOBALS['nextendbuffer'] = '';
            $form                     = new N2Form($this->_form->appType);

            $form->_data = &$this->_form->_data;

            $form->loadXMLFile($v[1] . 'config.xml');

            ob_start();
            $form->render($this->control_name);
            $html .= ob_get_clean();

            $html .= $GLOBALS['nextendbuffer'];

            $html .= NHtml::closeTag("div");
        }

        $html .= NHtml::closeTag("div");

        $html .= NHtml::closeTag("div");
        N2JS::addInline('
            (function(){
                var matrix = $("#' . $id . '"),
                    views = matrix.find("> .n2-form-matrix-views > div"),
                    panes = matrix.find("> .n2-tabs > div");
                views.on("click", function(){
                    views.removeClass("n2-active");
                    panes.removeClass("n2-active");
                    var i = views.index(this);
                    views.eq(i).addClass("n2-active");
                    panes.eq(i).addClass("n2-active");
                });
            })()
        ');

        return $html . parent::fetchElement();
    }

    function getOptions() {
        if ($this->_list == null) {
            $this->_list = array();
            N2Plugin::callPlugin(N2XmlHelper::getAttribute($this->_xml, 'group'), N2XmlHelper::getAttribute($this->_xml, 'method'), array(&$this->_list));
        }
        uasort($this->_list, array(
            $this,
            'sort'
        ));
        return $this->_list;
    }

    function sort($a, $b) {
        if (!isset($a[2])) $a[2] = 10000;
        if (!isset($b[2])) $b[2] = 10000;
        return $a[2] - $b[2];
    }
}