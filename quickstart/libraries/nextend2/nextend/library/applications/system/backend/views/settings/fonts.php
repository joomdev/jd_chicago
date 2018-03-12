<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
/**
 * @var $_class N2SystemBackendSettingsView
 * @see Actions
 */
$this->widget->init('topbar', array(
    "actions" => array(
        NHtml::tag('a', array(
            'href'    => '#',
            'class'   => 'n2-button n2-button-green n2-button-big n2-h4 n2-b n2-uc',
            'onclick' => 'return NextendForm.submit("#nextend-config");'
        ), n2_('Save'))
    )
));

?>
    <div class="n2-heading-bar">
        <div class="n2-h1 n2-heading"><?php echo n2_e('Fonts Configuration'); ?></div>
    </div>
<?php
$_class->renderFontsConfigurationForm();