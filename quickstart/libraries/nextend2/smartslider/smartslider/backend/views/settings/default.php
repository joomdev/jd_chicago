<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
/**
 * @var $_class N2SmartsliderBackendSettingsView
 */

$this->widget->init('topbar', array(
    "actions" => array(
        NHtml::tag('a', array(
            'href'    => '#',
            'class'   => 'n2-button n2-button-green n2-button-big n2-h4 n2-b n2-uc',
            'onclick' => 'return NextendForm.submit("#smartslider-form");'
        ), n2_('Save'))
    )
));

?>

<form id="smartslider-form" action="" method="post">
    <?php
    $_class->_renderDefaultForm();
    ?>
    <input name="save" value="1" type="hidden"/>
</form>
<?php N2SS3::showBeacon('Global settings'); ?>