<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('libraries.form.element');

/**
 * Class N2Tab
 */
class N2Tab
{

    /**
     * @var
     */
    var $_form;

    /**
     * @var
     */
    var $_xml;

    /**
     * @var string
     */
    var $_name;

    /**
     * @var
     */
    var $_attributes;

    /**
     * @var
     */
    var $_elements;

    var $_hide = false;

    /**
     * @param $form
     * @param $xml
     */
    public function __construct(&$form, &$xml) {
        $this->_form      = $form;
        $this->_xml       = $xml;
        $this->_name      = N2XmlHelper::getAttribute($xml, 'name');
        $this->_hidetitle = N2XmlHelper::getAttribute($xml, 'hidetitle');
        $this->initElements();
    }

    function initElements() {
        $this->_elements = array();
        foreach ($this->_xml->param AS $element) {
            $test = N2XmlHelper::getAttribute($element, 'test');
            if ($this->_form->makeTest($test)) {

                $class = N2Form::importElement(N2XmlHelper::getAttribute($element, 'type'));
                if (!class_exists($class, false)) {
                    throw new Exception($class . ' missing in ' . $this->_form->_xmlfile);
                    n2_exit(true);
                }

                $field = new $class($this->_form, $this, $element);
                if ($field->_name) {
                    $this->_elements[$field->_name] = $field;
                } else {
                    $this->_elements[] = $field;
                }
            }
        }
    }

    /**
     * @param $control_name
     */
    function render($control_name) {

        ob_start();
        $this->decorateTitle();
        $this->decorateGroupStart();
        $keys = array_keys($this->_elements);
        for ($i = 0; $i < count($keys); $i++) {
            $this->decorateElement($this->_elements[$keys[$i]], $this->_elements[$keys[$i]]->render($control_name), $i);
        }
        $this->decorateGroupEnd();

        if ($this->_hide) {
            echo NHtml::tag('div', array('style' => 'display: none;'), ob_get_clean());
        } else {
            echo ob_get_clean();
        }

    }

    function decorateTitle() {
        echo "<div id='n2-tab-" . N2XmlHelper::getAttribute($this->_xml, 'name') . "' class='n2-form-tab " . N2XmlHelper::getAttribute($this->_xml, 'class') . "'>";
        if ($this->_hidetitle != 1) {
            echo NHtml::tag('div', array(
                'class' => 'n2-h2 n2-content-box-title-bg'
            ), n2_(N2XmlHelper::getAttribute($this->_xml, 'label')));
        }
    }

    function decorateGroupStart() {
        echo "<table>";
        echo NHtml::tag('colgroup', array(), NHtml::tag('col', array('class' => 'n2-label-col')) . NHtml::tag('col', array('class' => 'n2-element-col')));
    }

    function decorateGroupEnd() {
        echo "</table>";
        echo "</div>";
    }

    /**
     * @param $el
     * @param $out
     * @param $i
     */
    function decorateElement(&$el, $out, $i) {
        echo "<tr class='" . N2XmlHelper::getAttribute($el->_xml, 'class') . "'>";
        $colSpan = '';
        if ($out[0] != '') {
            echo "<td class='n2-label'>" . $out[0] . "</td>";
        } else {
            $colSpan = 'colspan="2"';
        }
        echo "<td class='n2-element' {$colSpan}>" . $out[1] . "</td>";
        echo "</tr>";
    }
}

class N2TabDark extends N2Tab
{

    function decorateTitle() {
        echo "<div id='n2-tab-" . N2XmlHelper::getAttribute($this->_xml, 'name') . "' class='n2-form-tab " . N2XmlHelper::getAttribute($this->_xml, 'class') . "'>";
        if ($this->_hidetitle != 1) {
            echo NHtml::tag('div', array(
                'class' => 'n2-h3 n2-sidebar-header-bg n2-uc'
            ), n2_(N2XmlHelper::getAttribute($this->_xml, 'label')));
        }
    }
}

?>