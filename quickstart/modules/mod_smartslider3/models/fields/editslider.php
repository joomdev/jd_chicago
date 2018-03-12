<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
jimport('joomla.form.formfield');

class JFormFieldEditSlider extends JFormField
{

    protected $type = 'EditSlider';

    public function getInput() {
        return '<a href="#" onclick="window.open(\'' . JUri::base() . 'index.php?option=com_smartslider3&nextendcontroller=slider&nextendaction=edit&sliderid=\' + jQuery(\'#jform_params_slider\').val(), \'_blank\'); return false;" class="btn btn-small btn-success" target="_blank"><span class="icon-apply icon-white"></span>Edit selected slider</a>';
    }
}