<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('libraries.form.tab');

class N2TabTabbed extends N2Tab
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

        $id = 'n2-form-matrix-' . $this->_name;

        $active = intval(N2XmlHelper::getAttribute($this->_xml, 'active'));
        $active = $active > 0 ? $active - 1 : 0;

        $underlined = N2XmlHelper::getAttribute($this->_xml, 'underlined');

        $classes = N2XmlHelper::getAttribute($this->_xml, 'classes');
        ?>

        <div id="<?php echo $id; ?>" class="n2-form-tab n2-form-matrix">
            <div
                class="n2-h2 n2-content-box-title-bg n2-form-matrix-views <?php echo $classes; ?>">
                <?php
                $i     = 0;
                $class = ($underlined ? 'n2-underline' : '') . ' n2-h4 n2-uc n2-has-underline n2-form-matrix-menu';


                foreach ($this->_tabs AS $tabName => $tab) {


                    echo NHtml::tag("div", array(
                        "class" => $class . ($i == $active ? ' n2-active' : '') . ' n2-fm-' . $tabName
                    ), NHtml::tag("span", array("class" => "n2-underline"), n2_(N2XmlHelper::getAttribute($tab->_xml, 'label'))));

                    $i++;
                }
                ?>
            </div>

            <div class="n2-tabs">
                <?php
                $i = 0;
                foreach ($this->_tabs AS $tabName => $tab) {
                    echo NHtml::openTag('div', array(
                        'class' => 'n2-form-matrix-pane' . ($i == $active ? ' n2-active' : '') . ' n2-fm-' . $tabName
                    ));
                    $tab->render($control_name);
                    echo NHtml::closeTag('div');
                    $i++;
                }
                ?>
            </div>
        </div>

        <?php
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
        ?>
    <?php
    }

}
