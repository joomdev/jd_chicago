<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\GoogleSpreadsheetSave;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class GoogleSpreadsheetSave extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'Google Spreadsheet Save';
	//static $setup = array('simple' => array('title' => 'Data Save'));
	static $group = array('data_management' => 'Data Management');

	var $defaults = array(
		'enabled' => 1,
		'username' => '',
		'password' => '',
		'spreadsheet' => '',
		'worksheet' => '',
		'data_path' => 'GSheet',
	);

	function execute(&$form, $action_id){
		$config =  $form->actions_config[$action_id];
		$config = new \GCore\Libs\Parameter($config);

		$doc = new Spreadsheet();
		$doc->authenticate($config->get('username'), $config->get('password'));
		$doc->setSpreadsheet($config->get('spreadsheet'));
		$doc->setWorksheet($config->get('worksheet'));
		
		$path = $config->get('data_path', 'GSheet') ? explode('.', $config->get('data_path', 'GSheet')) : array();
		$data = \GCore\Libs\Arr::getVal($form->data, $path, array());
		//pr($data);
		$doc->add($data);
		$form->debug[$action_id][self::$title] = $doc->debug;
	}

	public static function config(){
		$tables = \GCore\Libs\Database::getInstance()->getTablesList();
		array_unshift($tables, '');
		$tables = array_combine($tables, $tables);

		echo \GCore\Helpers\Html::formStart('action_config google_spreadsheet_save_action_config', 'google_spreadsheet_save_action_config__XNX_');
		echo \GCore\Helpers\Html::formSecStart();
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][enabled]', array('type' => 'dropdown', 'label' => l_('CF_ENABLED'), 'options' => array(0 => l_('NO'), 1 => l_('YES'))));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][username]', array('type' => 'text', 'label' => l_('CF_GSPREADSHEET_SAVE_USERNAME'), 'class' => 'L', 'sublabel' => l_('CF_GSPREADSHEET_SAVE_USERNAME_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][password]', array('type' => 'text', 'label' => l_('CF_GSPREADSHEET_SAVE_PASSWORD'), 'class' => 'L', 'sublabel' => l_('CF_GSPREADSHEET_SAVE_PASSWORD_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][spreadsheet]', array('type' => 'text', 'label' => l_('CF_GSPREADSHEET_SAVE_SPREADSHEET'), 'class' => 'L', 'sublabel' => l_('CF_GSPREADSHEET_SAVE_SPREADSHEET_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][worksheet]', array('type' => 'text', 'label' => l_('CF_GSPREADSHEET_SAVE_WORKSHEET'), 'class' => 'L', 'sublabel' => l_('CF_GSPREADSHEET_SAVE_WORKSHEET_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][data_path]', array('type' => 'text', 'label' => l_('CF_GSPREADSHEET_SAVE_DATA_PATH'), 'class' => 'L', 'sublabel' => l_('CF_GSPREADSHEET_SAVE_DATA_PATH_DESC')));
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
	
	public static function config_check($data = array()){
		$diags = array();
		$diags[l_('CF_DIAG_ENABLED')] = !empty($data['enabled']);
		$diags[l_('CF_DIAG_USERNAME_SET')] = !empty($data['username']);
		$diags[l_('CF_DIAG_PASSWORD_SET')] = !empty($data['password']);
		$diags[l_('CF_DIAG_SPREADSHEET_SET')] = !empty($data['spreadsheet']);
		$diags[l_('CF_DIAG_WORKSHEET_SET')] = !empty($data['worksheet']);
		$diags[l_('CF_DIAG_DATAPATH_SET')] = !empty($data['data_path']) ? true : -1;
		return $diags;
	}
}