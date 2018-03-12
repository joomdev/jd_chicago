<?php


defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if (!class_exists( 'VmConfig' )) require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');

if (!class_exists('ShopFunctions'))
require(VMPATH_ADMIN . DS . 'helpers' . DS . 'shopfunctions.php');
if (!class_exists('VirtueMartModelManufacturer'))
JLoader::import('manufacturer', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'models');


if(!class_exists('TableManufacturers')) require(VMPATH_ADMIN.DS.'tables'.DS.'manufacturers.php');
if (!class_exists( 'VirtueMartModelManufacturer' ))
JLoader::import( 'manufacturer', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'models' );
/**
 * Supports a modal Manufacturer picker.
 *
 *
 */
class JFormFieldManufacturer extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @author      Valerie Cartan Isaksen
	 * @var		string
	 *
	 */
	var $type = 'manufacturer';

	function getInput() {

		VmConfig::loadConfig();
		$model = VmModel::getModel('Manufacturer');
		$manufacturers = $model->getManufacturers(true, true, false);
		$emptyOption = JHtml::_ ('select.option', '', vmText::_ ('COM_VIRTUEMART_LIST_EMPTY_OPTION'), 'virtuemart_manufacturer_id', 'mf_name');
		if(!empty($manufacturers) and is_array($manufacturers)){
			array_unshift ($manufacturers, $emptyOption);
		} else {
			$manufacturers = array($emptyOption);
		}

		return JHtml::_('select.genericlist', $manufacturers, $this->name, 'class="inputbox"  size="1"', 'virtuemart_manufacturer_id', 'mf_name', $this->value, $this->id);
	}


}