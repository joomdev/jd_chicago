<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('libraries.form.tab');

class N2TabTabbedSidebar extends N2Tab
{

    var $_tabs;

    function initTabs() {
        if (count($this->_tabs) == 0) {
            foreach ($this->_xml->params as $tab) {
                $test = N2XmlHelper::getAttribute($tab, 'test');
                if ($test == '' || $this->_form->makeTest($test)) {
                    $type = N2XmlHelper::getAttribute($tab, 'type');
                    if ($type == '') $type = 'default';
                    N2Loader::import('libraries.form.tabs.' . $type);
                    $class = 'N2Tab' . ucfirst($type);

                    $this->_tabs[N2XmlHelper::getAttribute($tab, 'name')] = new $class($this->_form, $tab);
                }
            }
        }
    }

    function render($control_name) {
        $this->initTabs();

        $count  = count($this->_tabs);
        $id     = 'n2-tabbed-' . $this->_name;
        $active = intval(N2XmlHelper::getAttribute($this->_xml, 'active'));
        $active = $active > 0 ? $active - 1 : 0;

        $underlined = N2XmlHelper::getAttribute($this->_xml, 'underlined');

        ?>

        <div id="<?php echo $id; ?>">
            <div
                class="n2-table n2-table-fixed n2-labels <?php echo N2XmlHelper::getAttribute($this->_xml, 'classes') . ($underlined ? ' n2-has-underline' : ''); ?>">
                <div class="n2-tr">
                    <?php
                    $i     = 0;
                    $class = ($underlined ? 'n2-underline' : '');
                    foreach ($this->_tabs AS $tabname => $tab) {
                        echo NHtml::tag('div', array(
                            'class' => "n2-td n2-h3 n2-uc n2-has-underline" . ($i == $active ? ' n2-active' : '')
                        ), NHtml::tag('span', array(
                            'class' => $class
                        ), n2_(N2XmlHelper::getAttribute($tab->_xml, 'label'))));
                        $i++;
                    }
                    ?>
                </div>
            </div>
            <div class="n2-tabs">
                <?php
                $tabs = array();
                $i    = 0;
                foreach ($this->_tabs AS $tabname => $tab) {
                    $display = 'none';
                    if ($i == $active) {
                        $display = 'block';
                    }
                    $tabs[] = "$('#" . $id . '_' . $i . "')";
                    echo NHtml::openTag('div', array(
                        'id'    => $id . '_' . $i,
                        'style' => 'display:' . $display . ';'
                    ));
                    $tab->render($control_name);
                    echo NHtml::closeTag('div');
                    $i++;
                }
                ?>
            </div>
        </div>
        <script type="text/javascript">
            nextend.ready(
                function ($) {
                    new NextendHeadingPane($('#<?php echo $id; ?> > .n2-labels .n2-td'), [
                        <?php echo implode(',', $tabs); ?>
                    ]);
                }
            );
        </script>
    <?php
    }

}
